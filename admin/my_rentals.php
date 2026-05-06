<?php include('../config.php'); ?>
<?php include('../header.php'); ?>

<?php
if (!isset($_SESSION['tuvastamine'])) {
    header('Location: ../index.php');
    exit();
}
?>

<!-- sisu -->
<div class="container">
    <?php
    // Kuva edastust/veateade
    if (!empty($_GET['msg'])) {
        if ($_GET['msg'] === 'kustutatud') {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Broneering edukalt kustutatud. <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        } elseif ($_GET['msg'] === 'muudetud') {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Broneering edukalt muudetud. <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        } elseif ($_GET['msg'] === 'error') {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">Broneering kustutamise võimalus puudub. <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }
    }
    ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Broneeringud</h2>
        <a href="<?php echo ($_SESSION['role'] === 'administraator') ? 'index.php' : '../index.php'; ?>" class="btn btn-secondary">Tagasi pealehele</a>
    </div>
    <?php
    if (!isset($_SESSION['tuvastamine'])) {
        echo '<div class="alert alert-warning">Palun logi sisse, et näha oma renditud autosid.</div>';
    } else {
        if ($_SESSION['role'] === 'administraator') {
            $pealkiri = 'Kõik broneeringud';
            $base_paring = "SELECT r.id, r.start_date, r.end_date, r.total_price, r.status, r.lisakindlustus, c.mark, c.model, u.email, u.first_name, u.last_name
                           FROM reservations r
                           JOIN cars c ON r.car_id = c.id
                           JOIN users u ON r.user_id = u.id";
        } else {
            $pealkiri = 'Minu broneeringud';
            $user_id = $_SESSION['user_id'];
            $base_paring = "SELECT r.id, r.start_date, r.end_date, r.total_price, r.status, r.lisakindlustus, c.mark, c.model
                           FROM reservations r
                           JOIN cars c ON r.car_id = c.id
                           WHERE r.user_id = $user_id";
        }

        // Build filters
        $where_conditions = array();
        if ($_SESSION['role'] === 'administraator' && empty($_SESSION['user_id'])) {
            // Admin mode - no user restriction
        } else if ($_SESSION['role'] !== 'administraator') {
            // User mode - already has user_id in base query
        }

        // Add filter conditions
        if (!empty($_GET['car_filter'])) {
            $car_filter = explode('|', $_GET['car_filter']);
            $mark = mysqli_real_escape_string($yhendus, $car_filter[0]);
            $model = mysqli_real_escape_string($yhendus, $car_filter[1]);
            $where_conditions[] = "(c.mark = '$mark' AND c.model = '$model')";
        }
        if (!empty($_GET['status_filter'])) {
            $status = mysqli_real_escape_string($yhendus, $_GET['status_filter']);
            $where_conditions[] = "(r.status = '$status')";
        }
        if (!empty($_GET['start_date_filter'])) {
            $start_date = mysqli_real_escape_string($yhendus, $_GET['start_date_filter']);
            $where_conditions[] = "(r.start_date = '$start_date')";
        }
        if (!empty($_GET['end_date_filter'])) {
            $end_date = mysqli_real_escape_string($yhendus, $_GET['end_date_filter']);
            $where_conditions[] = "(r.end_date = '$end_date')";
        }
        if ($_SESSION['role'] === 'administraator' && !empty($_GET['user_filter'])) {
            $user_filter = mysqli_real_escape_string($yhendus, $_GET['user_filter']);
            $where_conditions[] = "(u.id = '$user_filter')";
        }

        // Build final query
        $paring = $base_paring;
        if ($_SESSION['role'] !== 'administraator') {
            // User query already has WHERE clause
            if (!empty($where_conditions)) {
                $paring .= " AND " . implode(" AND ", $where_conditions);
            }
        } else {
            // Admin query - add WHERE clause if needed
            if (!empty($where_conditions)) {
                $paring .= " WHERE " . implode(" AND ", $where_conditions);
            }
        }
        $paring .= " ORDER BY r.start_date DESC";

        // Get distinct values for filters
        if ($_SESSION['role'] === 'administraator') {
            $cars_paring = "SELECT DISTINCT c.mark, c.model FROM reservations r JOIN cars c ON r.car_id = c.id ORDER BY c.mark, c.model";
            $statuses_paring = "SELECT DISTINCT r.status FROM reservations r ORDER BY r.status";
            $start_dates_paring = "SELECT DISTINCT r.start_date FROM reservations r ORDER BY r.start_date DESC";
            $end_dates_paring = "SELECT DISTINCT r.end_date FROM reservations r ORDER BY r.end_date DESC";
            $users_paring = "SELECT DISTINCT u.id, u.first_name, u.last_name, u.email FROM reservations r JOIN users u ON r.user_id = u.id ORDER BY u.first_name, u.last_name";
        } else {
            $cars_paring = "SELECT DISTINCT c.mark, c.model FROM reservations r JOIN cars c ON r.car_id = c.id WHERE r.user_id = $user_id ORDER BY c.mark, c.model";
            $statuses_paring = "SELECT DISTINCT r.status FROM reservations r WHERE r.user_id = $user_id ORDER BY r.status";
            $start_dates_paring = "SELECT DISTINCT r.start_date FROM reservations r WHERE r.user_id = $user_id ORDER BY r.start_date DESC";
            $end_dates_paring = "SELECT DISTINCT r.end_date FROM reservations r WHERE r.user_id = $user_id ORDER BY r.end_date DESC";
        }

        $cars_result = mysqli_query($yhendus, $cars_paring);
        $statuses_result = mysqli_query($yhendus, $statuses_paring);
        $start_dates_result = mysqli_query($yhendus, $start_dates_paring);
        $end_dates_result = mysqli_query($yhendus, $end_dates_paring);
        if ($_SESSION['role'] === 'administraator') {
            $users_result = mysqli_query($yhendus, $users_paring);
        }

        echo '<h3>' . $pealkiri . '</h3>';
        
        // Display filter form
        echo '<div class="card mb-4 p-3">
                <h5>Filtreeri tulemusi</h5>
                <form method="get" class="row g-3">
                    <div class="col-md-3">
                        <label for="car_filter" class="form-label">Auto</label>
                        <select class="form-select" id="car_filter" name="car_filter">
                            <option value="">-- Vali auto --</option>';
        
        while ($car = mysqli_fetch_assoc($cars_result)) {
            $car_value = $car['mark'] . '|' . $car['model'];
            $selected = (!empty($_GET['car_filter']) && $_GET['car_filter'] === $car_value) ? 'selected' : '';
            echo '<option value="' . $car_value . '" ' . $selected . '>' . $car['mark'] . ' ' . $car['model'] . '</option>';
        }
        
        echo '        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status_filter" class="form-label">Staatus</label>
                        <select class="form-select" id="status_filter" name="status_filter">
                            <option value="">-- Vali staatus --</option>';
        
        while ($status = mysqli_fetch_assoc($statuses_result)) {
            $selected = (!empty($_GET['status_filter']) && $_GET['status_filter'] === $status['status']) ? 'selected' : '';
            echo '<option value="' . htmlspecialchars($status['status']) . '" ' . $selected . '>' . htmlspecialchars($status['status']) . '</option>';
        }
        
        echo '        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="start_date_filter" class="form-label">Alguskuupäev</label>
                        <select class="form-select" id="start_date_filter" name="start_date_filter">
                            <option value="">-- Vali kuupäev --</option>';
        
        while ($date = mysqli_fetch_assoc($start_dates_result)) {
            $selected = (!empty($_GET['start_date_filter']) && $_GET['start_date_filter'] === $date['start_date']) ? 'selected' : '';
            echo '<option value="' . $date['start_date'] . '" ' . $selected . '>' . $date['start_date'] . '</option>';
        }
        
        echo '        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="end_date_filter" class="form-label">Lõppkuupäev</label>
                        <select class="form-select" id="end_date_filter" name="end_date_filter">
                            <option value="">-- Vali kuupäev --</option>';
        
        while ($date = mysqli_fetch_assoc($end_dates_result)) {
            $selected = (!empty($_GET['end_date_filter']) && $_GET['end_date_filter'] === $date['end_date']) ? 'selected' : '';
            echo '<option value="' . $date['end_date'] . '" ' . $selected . '>' . $date['end_date'] . '</option>';
        }
        
        echo '        </select>
                    </div>';
        
        if ($_SESSION['role'] === 'administraator') {
            echo '    <div class="col-md-3">
                        <label for="user_filter" class="form-label">Kasutaja</label>
                        <select class="form-select" id="user_filter" name="user_filter">
                            <option value="">-- Vali kasutaja --</option>';
            
            while ($user = mysqli_fetch_assoc($users_result)) {
                $selected = (!empty($_GET['user_filter']) && $_GET['user_filter'] === $user['id']) ? 'selected' : '';
                echo '<option value="' . $user['id'] . '" ' . $selected . '>' . $user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['email'] . ')</option>';
            }
            
            echo '        </select>
                    </div>';
        }
        
        echo '      <div class="col-12">
                        <button type="submit" class="btn btn-primary">Filtreeri</button>
                        <a href="my_rentals.php" class="btn btn-secondary">Tühjenda</a>
                    </div>
                </form>
            </div>';

        $valjund = mysqli_query($yhendus, $paring);

        if (mysqli_num_rows($valjund) > 0) {
            echo '<table class="table">
                    <thead>
                        <tr>
                            <th>Auto</th>
                            <th>Alguskuupäev</th>
                            <th>Lõppkuupäev</th>
                            <th>Koguhind</th>
                            <th>Lisakindlustus</th>
                            <th>Staatus</th>';
            if ($_SESSION['role'] === 'administraator') {
                echo '<th>Kasutaja</th>';
            }
            echo '          <th>Tegevus</th>
                        </tr>
                    </thead>
                    <tbody>';

            while ($rida = mysqli_fetch_assoc($valjund)) {
                echo '<tr>
                        <td>' . $rida['mark'] . ' ' . $rida['model'] . '</td>
                        <td>' . $rida['start_date'] . '</td>
                        <td>' . $rida['end_date'] . '</td>
                        <td>' . $rida['total_price'] . ' €</td>
                        <td>' . ($rida['lisakindlustus'] === 'jah' ? 'Jah' : 'Ei') . '</td>
                        <td>' . $rida['status'] . '</td>';
                if ($_SESSION['role'] === 'administraator') {
                    echo '<td>' . $rida['first_name'] . ' ' . $rida['last_name'] . ' (' . $rida['email'] . ')</td>';
                }
                echo '      <td>
                            <a href="muuda_broneering.php?id=' . $rida['id'] . '" class="btn btn-warning btn-sm">Muuda</a>
                            <a href="kustuta_broneering.php?delid=' . $rida['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Kas oled kindel?\');">Kustuta</a>
                        </td>
                    </tr>';
            }

            echo '</tbody></table>';
        } else {
            echo '<div class="alert alert-info">Pole leitud ühtegi broneeringut vastavalt valitud filtritele.</div>';
        }
    }
    ?>
</div>
<!-- /sisu -->

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>