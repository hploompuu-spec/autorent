<?php include('config.php'); ?>
<?php include('header.php'); ?>

<div class="container">
    <a href="index.php" class="btn btn-dark">Tagasi</a>

<?php
$id = max(0, (int)($_GET['id'] ?? 0));
$rida = db_fetch_one($yhendus, 'SELECT * FROM cars WHERE id = ?', 'i', $id);
if ($rida === null) {
    http_response_code(404);
    echo '<div class="alert alert-warning mt-3">Autot ei leitud.</div></div></body></html>';
    exit();
}

$errors = [];
$success_message = '';
$start_date = '';
$end_date = '';
$lisakindlustus = 'ei';
$reserved_periods = [];
$today = date('Y-m-d');
$start_invalid = false;
$end_invalid = false;

$stmt = db_prepare($yhendus, 'SELECT start_date, end_date, status FROM reservations WHERE car_id = ? ORDER BY start_date DESC', 'i', $id);
mysqli_stmt_execute($stmt);
$reservations_result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($reservations_result)) {
    $reserved_periods[] = $row;
}
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['tuvastamine'])) {
    require_csrf_token($_POST['csrf_token'] ?? null);

    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $lisakindlustus = $_POST['lisakindlustus'] ?? 'ei';

    if (!validate_date_string($start_date)) {
        $errors[] = 'Alguskuupäev on kohustuslik.';
        $start_invalid = true;
    }
    if (!validate_date_string($end_date)) {
        $errors[] = 'Lõppkuupäev on kohustuslik.';
        $end_invalid = true;
    }
    if (!in_array($lisakindlustus, ['jah', 'ei'], true)) {
        $lisakindlustus = 'ei';
    }
    if (($_SESSION['role'] ?? '') !== 'administraator' && $start_date < $today) {
        $errors[] = 'Alguskuupäev ei saa olla minevikus.';
        $start_invalid = true;
    }

    if (empty($errors)) {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);

        if ($end < $start) {
            $errors[] = 'Lõppkuupäev peab olema alguskuupäevast hiljem.';
            $end_invalid = true;
        } else {
            $conflict = db_fetch_one(
                $yhendus,
                'SELECT start_date, end_date FROM reservations WHERE car_id = ? AND NOT (end_date < ? OR start_date > ?) LIMIT 1',
                'iss',
                $id,
                $start_date,
                $end_date
            );
            if ($conflict !== null) {
                $errors[] = 'Valitud periood kattub olemasoleva broneeringuga: ' . $conflict['start_date'] . ' kuni ' . $conflict['end_date'] . '.';
                $start_invalid = true;
                $end_invalid = true;
            }
        }
    }

    if (empty($errors)) {
        $days = $start->diff($end)->days + 1;
        $total_price = (int)$rida['price'] * $days;
        $user_id = (int)$_SESSION['user_id'];

        $created = db_execute(
            $yhendus,
            "INSERT INTO reservations (user_id, car_id, start_date, end_date, total_price, status, lisakindlustus, created_at) VALUES (?, ?, ?, ?, ?, 'broneeritud', ?, NOW())",
            'iissis',
            $user_id,
            $id,
            $start_date,
            $end_date,
            $total_price,
            $lisakindlustus
        );

        if ($created) {
            $success_message = 'Auto edukalt renditud!';
            $reserved_periods[] = ['start_date' => $start_date, 'end_date' => $end_date, 'status' => 'broneeritud'];
        } else {
            $errors[] = 'Viga rentimisel.';
        }
    }
}
?>

    <div class="row">
        <div class="col">
            <h1><?php echo e($rida['mark']); ?> <?php echo e($rida['model']); ?></h1>
            <p>Mootor: <?php echo e($rida['engine']); ?></p>
            <p>Kütus: <?php echo e($rida['fuel']); ?></p>
            <p>Aasta: <?php echo e($rida['year']); ?></p>
            <p>Staatus: <?php echo e($rida['status']); ?></p>
            <p>Käigukast: <?php echo e($rida['transmission']); ?></p>
            <p>Istmed: <?php echo e($rida['seats']); ?></p>
            <p class="fs-5">Hind: <?php echo e($rida['price']); ?> €/päev</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if ($success_message !== ''): ?>
                <div class="alert alert-success"><?php echo e($success_message); ?></div>
            <?php endif; ?>

            <?php if (!empty($reserved_periods)): ?>
                <div class="mb-4">
                    <h5>Reserveeritud ajavahemikud</h5>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Algus</th>
                                <th>Lõpp</th>
                                <th>Staatus</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($reserved_periods as $period): ?>
                            <tr class="table-danger">
                                <td><?php echo e($period['start_date']); ?></td>
                                <td><?php echo e($period['end_date']); ?></td>
                                <td><?php echo e($period['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-4">Sellel autol pole veel broneeringuid.</div>
            <?php endif; ?>

            <?php if (isset($_SESSION['tuvastamine'])): ?>
                <form method="post">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Alguskuupäev</label>
                        <input type="date" class="form-control <?php echo $start_invalid ? 'is-invalid' : ''; ?>" id="start_date" name="start_date" value="<?php echo e($start_date); ?>" <?php echo ($_SESSION['role'] !== 'administraator' ? 'min="' . e($today) . '"' : ''); ?> required>
                        <?php if ($start_invalid): ?>
                            <div class="invalid-feedback">Palun sisesta korrektne alguskuupäev.</div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Lõppkuupäev</label>
                        <input type="date" class="form-control <?php echo $end_invalid ? 'is-invalid' : ''; ?>" id="end_date" name="end_date" value="<?php echo e($end_date); ?>" <?php echo ($_SESSION['role'] !== 'administraator' ? 'min="' . e($today) . '"' : ''); ?> required>
                        <?php if ($end_invalid): ?>
                            <div class="invalid-feedback">Palun sisesta korrektne lõppkuupäev.</div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="lisakindlustus" class="form-label">Lisakindlustus</label>
                        <select class="form-control" id="lisakindlustus" name="lisakindlustus" required>
                            <option value="ei" <?php echo $lisakindlustus === 'ei' ? 'selected' : ''; ?>>Ei</option>
                            <option value="jah" <?php echo $lisakindlustus === 'jah' ? 'selected' : ''; ?>>Jah</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Rendi auto</button>
                </form>
            <?php else: ?>
                <a href="admin/login.php" class="btn btn-dark w-100">Logi sisse rentimiseks</a>
            <?php endif; ?>
        </div>
        <div class="col">
            <img src="https://loremflickr.com/800/500/<?php echo rawurlencode(str_replace(' ', '', $rida['mark'])); ?>" class="card-img-top img-fluid" alt="<?php echo e($rida['mark']); ?>">
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
