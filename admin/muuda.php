<?php include('../config.php'); ?>
<?php include('../header.php'); ?>
<?php include('admin_check.php'); ?>

<?php
    $rida = [];

    if(isset($_GET["editid"])){
        $id = (int)$_GET["editid"];
        $paring = "SELECT * FROM cars WHERE id=$id";
        $valjund = mysqli_query($yhendus, $paring);
        $rida = mysqli_fetch_assoc($valjund);
    }

    if(isset($_GET["updateid"])){
        $id = (int)$_GET["updateid"];
        $mark = mysqli_real_escape_string($yhendus, $_GET['mark']);
        $model = mysqli_real_escape_string($yhendus, $_GET['model']);
        $engine = mysqli_real_escape_string($yhendus, $_GET['engine']);
        $fuel = mysqli_real_escape_string($yhendus, $_GET['fuel']);
        $price = (int)$_GET['price'];
        $year = (int)$_GET['year'];
        $transmission = mysqli_real_escape_string($yhendus, $_GET['transmission']);
        $seats = (int)$_GET['seats'];
        $description = mysqli_real_escape_string($yhendus, $_GET['description']);
        $status = mysqli_real_escape_string($yhendus, $_GET['status']);

        $paring = "UPDATE cars SET mark = '$mark', model = '$model', engine = '$engine', fuel = '$fuel', price = $price, year = $year, transmission = '$transmission', seats = $seats, description = '$description', status = '$status' WHERE cars.id = $id";

        $valjund = mysqli_query($yhendus, $paring);
        if ($valjund) {
            header("Location: index.php?msg=uuendatud");
            exit();
        } else {
            echo '<div class="alert alert-danger">Viga uuendamisel: ' . mysqli_error($yhendus) . '</div>';
        }
    }

?>

<!-- sisu -->
<div class="container">
    <h2>Auto muutmine</h2>
    <?php if (!empty($rida)): ?>
    <form action="muuda.php" method="get">
        <div class="row g-4">
            <div class="col-sm-6">
                <input type="hidden" name="updateid" value="<?= $rida['id']; ?>">

                <label for="mark" class="form-label">Mark</label>
                <input type="text" class="form-control" id="mark" name="mark" value="<?= htmlspecialchars($rida['mark']); ?>" required>
                <label for="model" class="form-label">Model</label>
                <input type="text" class="form-control" id="model" name="model" value="<?= htmlspecialchars($rida['model']); ?>" required>
                <label for="engine" class="form-label">Mootor</label>
                <input type="text" class="form-control" id="engine" name="engine" value="<?= htmlspecialchars($rida['engine']); ?>" required>
                <label for="fuel" class="form-label">Kütus</label>
                <input type="text" class="form-control" id="fuel" name="fuel" value="<?= htmlspecialchars($rida['fuel']); ?>" required>
                <label for="price" class="form-label">Hind</label>
                <input type="number" class="form-control" id="price" name="price" value="<?= $rida['price']; ?>" required>
            </div>
            <div class="col-sm-6">
                <label for="year" class="form-label">Aasta</label>
                <input type="number" class="form-control" id="year" name="year" value="<?= $rida['year']; ?>" required>
                <label for="transmission" class="form-label">Käigukast</label>
                <input type="text" class="form-control" id="transmission" name="transmission" value="<?= htmlspecialchars($rida['transmission']); ?>" required>
                <label for="seats" class="form-label">Istmete arv</label>
                <input type="number" class="form-control" id="seats" name="seats" value="<?= $rida['seats']; ?>" required>
                <label for="description" class="form-label">Muu info</label>
                <input type="text" class="form-control" id="description" name="description" value="<?= htmlspecialchars($rida['description']); ?>">
                <label for="status" class="form-label">Olek</label>
                <input type="text" class="form-control" id="status" name="status" value="<?= htmlspecialchars($rida['status']); ?>">
            </div>
        </div>
        <input type="submit" value="Salvesta" class="btn btn-success">
    </form>
    <?php else: ?>
        <div class="alert alert-warning">Palun vali auto, mida soovid muuta.</div>
    <?php endif; ?>

</div>
<!-- /sisu -->

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>