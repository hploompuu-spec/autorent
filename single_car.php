<?php include('config.php'); ?>
<?php include('header.php'); ?>

<!-- sisu -->
<div class="container">

    <a href="index.php" class="btn btn-dark">Tagasi</a>

    <div class="row">
<?php
    $id = (int)($_GET['id'] ?? 0);
    $paring = "SELECT * FROM cars WHERE id=" . $id;
    $valjund = mysqli_query($yhendus, $paring);
    $rida = mysqli_fetch_assoc($valjund);

    $errors = [];
    $start_date = '';
    $end_date = '';
    $reserved_periods = [];
    $reservations_query = "SELECT start_date, end_date, status FROM reservations WHERE car_id = $id ORDER BY start_date DESC";
    $reservations_result = mysqli_query($yhendus, $reservations_query);
    while ($row = mysqli_fetch_assoc($reservations_result)) {
        $reserved_periods[] = $row;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['tuvastamine'])) {
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $start_invalid = false;
        $end_invalid = false;

        if ($start_date === '') {
            $errors[] = 'Alguskuupäev on kohustuslik.';
            $start_invalid = true;
        }
        if ($end_date === '') {
            $errors[] = 'Lõppkuupäev on kohustuslik.';
            $end_invalid = true;
        }

        if (empty($errors)) {
            $start = new DateTime($start_date);
            $end = new DateTime($end_date);

            if ($end < $start) {
                $errors[] = 'Lõppkuupäev peab olema alguskuupäevast hiljem.';
                $end_invalid = true;
            } else {
                $conflict_query = "SELECT start_date, end_date FROM reservations WHERE car_id = $id AND NOT (end_date < '$start_date' OR start_date > '$end_date')";
                $conflict_result = mysqli_query($yhendus, $conflict_query);
                if (mysqli_num_rows($conflict_result) > 0) {
                    $conflict = mysqli_fetch_assoc($conflict_result);
                    $errors[] = 'Valitud periood kattub olemasoleva broneeringuga: ' . $conflict['start_date'] . ' kuni ' . $conflict['end_date'] . '.';
                    $start_invalid = true;
                    $end_invalid = true;
                }
            }
        }

        if (empty($errors)) {
            $days = $start->diff($end)->days + 1;
            $total_price = $rida['price'] * $days;

            $id_query = "SELECT MAX(id) as max_id FROM reservations";
            $id_result = mysqli_query($yhendus, $id_query);
            $id_row = mysqli_fetch_assoc($id_result);
            $next_id = ($id_row['max_id'] ?? 0) + 1;

            $user_id = $_SESSION['user_id'];
            $insert = "INSERT INTO reservations (id, user_id, car_id, start_date, end_date, total_price, status, created_at) VALUES ($next_id, $user_id, $id, '$start_date', '$end_date', $total_price, 'broneeritud', NOW())";
            if (mysqli_query($yhendus, $insert)) {
                $success_message = 'Auto edukalt renditud!';
                $reserved_periods[] = ['start_date' => $start_date, 'end_date' => $end_date, 'status' => 'broneeritud'];
            } else {
                $errors[] = 'Viga rentimisel: ' . mysqli_error($yhendus);
            }
        }
    } else {
        $start_invalid = false;
        $end_invalid = false;
    }
?>
        <div class="col">
            <h1><?php echo $rida["mark"]; ?> <?php echo $rida["model"]; ?></h1>
            <p>Mootor:  <?php echo $rida["engine"]; ?></p>
            <p>Kütus:  <?php echo $rida["fuel"]; ?></p>
            <p>Aasta:  <?= $rida["year"]; ?></p>
            <p>Staatus:  <?php echo $rida["status"]; ?></p>
            <p>Käigukast:  <?php echo $rida["transmission"]; ?></p>
            <p>Istmed:  <?php echo $rida["seats"]; ?></p>
            <p class="fs-5">Hind:  <?php echo $rida["price"]; ?> €/päev</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
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
                                <td><?php echo $period['start_date']; ?></td>
                                <td><?php echo $period['end_date']; ?></td>
                                <td><?php echo $period['status']; ?></td>
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
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Alguskuupäev</label>
                        <input type="date" class="form-control <?php echo !empty($start_invalid) ? 'is-invalid' : ''; ?>" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" required>
                        <?php if (!empty($start_invalid)): ?>
                            <div class="invalid-feedback">Palun sisesta korrektne alguskuupäev.</div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Lõppkuupäev</label>
                        <input type="date" class="form-control <?php echo !empty($end_invalid) ? 'is-invalid' : ''; ?>" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" required>
                        <?php if (!empty($end_invalid)): ?>
                            <div class="invalid-feedback">Palun sisesta korrektne lõppkuupäev.</div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Rendi auto</button>
                </form>
            <?php else: ?>
                <a href="admin/login.php" class="btn btn-dark w-100">Logi sisse rentimiseks</a>
            <?php endif; ?>
        </div>
        <div class="col">
            <img src="https://loremflickr.com/800/500/<?php echo str_replace(" ","", $rida["mark"]); ?>" class="card-img-top img-fluid" alt="<?php echo $rida["mark"]; ?>">
        </div>
    </div>
</div>
<!-- /sisu -->

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>