<?php include('../config.php'); ?>
<?php include('../header.php'); ?>

<?php
if (!isset($_SESSION['tuvastamine'])) {
    header('Location: ../index.php');
    exit();
}

$reservation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($reservation_id === 0) {
    header('Location: my_rentals.php');
    exit();
}

// Get reservation data
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role === 'administraator') {
    $paring = "SELECT r.id, r.car_id, r.start_date, r.end_date, r.total_price, r.status, r.user_id, c.mark, c.model, c.price
               FROM reservations r
               JOIN cars c ON r.car_id = c.id
               WHERE r.id = $reservation_id";
} else {
    $paring = "SELECT r.id, r.car_id, r.start_date, r.end_date, r.total_price, r.status, r.user_id, c.mark, c.model, c.price
               FROM reservations r
               JOIN cars c ON r.car_id = c.id
               WHERE r.id = $reservation_id AND r.user_id = $user_id";
}

$valjund = mysqli_query($yhendus, $paring);

if (mysqli_num_rows($valjund) === 0) {
    header('Location: my_rentals.php');
    exit();
}

$reservation = mysqli_fetch_assoc($valjund);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    // Validate dates
    if (strtotime($end_date) <= strtotime($start_date)) {
        $error = "Lõppkuupäev peab olema pärast alguskuupäeva!";
    } else {
        // Calculate new total price
        $start = strtotime($start_date);
        $end = strtotime($end_date);
        $days = ($end - $start) / (60 * 60 * 24);
        $total_price = $days * $reservation['price'];

        $start_date = mysqli_real_escape_string($yhendus, $start_date);
        $end_date = mysqli_real_escape_string($yhendus, $end_date);
        $status = mysqli_real_escape_string($yhendus, $status);

        if ($role === 'administraator') {
            $update_paring = "UPDATE reservations SET start_date = '$start_date', end_date = '$end_date', status = '$status', total_price = '$total_price' WHERE id = $reservation_id";
        } else {
            // Regular users can only change dates, not status
            $update_paring = "UPDATE reservations SET start_date = '$start_date', end_date = '$end_date', total_price = '$total_price' WHERE id = $reservation_id AND user_id = $user_id";
        }

        $result = mysqli_query($yhendus, $update_paring);

        if ($result) {
            header('Location: my_rentals.php?msg=muudetud');
            exit();
        } else {
            $error = "Viga broneerigu uuendamisel!";
        }
    }
}
?>

<!-- sisu -->
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Broneerigu muutmine</h2>
        <button type="button" class="btn btn-secondary" onclick="history.back();">Tagasi</button>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card p-4" style="max-width: 600px;">
        <h5 class="card-title">Auto: <?php echo htmlspecialchars($reservation['mark'] . ' ' . $reservation['model']); ?></h5>
        <p class="card-text">Hind: <?php echo $reservation['price']; ?>€/päev</p>

        <form method="post">
            <div class="mb-3">
                <label for="start_date" class="form-label">Alguskuupäev</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $reservation['start_date']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="end_date" class="form-label">Lõppkuupäev</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $reservation['end_date']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="total_price" class="form-label">Koguhind (arvutatakse automaatselt)</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="total_price" value="<?php echo $reservation['total_price']; ?>" disabled>
                    <span class="input-group-text">€</span>
                </div>
            </div>

            <?php if ($role === 'administraator'): ?>
            <div class="mb-3">
                <label for="status" class="form-label">Staatus</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="confirmed" <?php echo ($reservation['status'] === 'confirmed') ? 'selected' : ''; ?>>Kinnitatud</option>
                    <option value="pending" <?php echo ($reservation['status'] === 'pending') ? 'selected' : ''; ?>>Ootel</option>
                    <option value="cancelled" <?php echo ($reservation['status'] === 'cancelled') ? 'selected' : ''; ?>>Tühistatud</option>
                    <option value="completed" <?php echo ($reservation['status'] === 'completed') ? 'selected' : ''; ?>>Lõpetatud</option>
                </select>
            </div>
            <?php else: ?>
                <input type="hidden" name="status" value="<?php echo $reservation['status']; ?>">
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Salvesta muudatused</button>
            <button type="reset" class="btn btn-secondary">Tühista</button>
        </form>
    </div>
</div>
<!-- /sisu -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

<script>
    // Update total price when dates change
    document.getElementById('start_date').addEventListener('change', updatePrice);
    document.getElementById('end_date').addEventListener('change', updatePrice);

    function updatePrice() {
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(document.getElementById('end_date').value);
        const price = <?php echo $reservation['price']; ?>;

        if (startDate && endDate && endDate > startDate) {
            const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
            const total = days * price;
            document.getElementById('total_price').value = total.toFixed(2);
        }
    }
</script>

</body>
</html>
