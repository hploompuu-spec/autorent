<?php 
include('../config.php'); 
include('admin_check.php');

$upload_dir = '../uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$rida = [];

if(isset($_GET["editid"])){
    $id = (int)$_GET["editid"];
    $paring = "SELECT * FROM cars WHERE id=$id";
    $valjund = mysqli_query($yhendus, $paring);
    $rida = mysqli_fetch_assoc($valjund);
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["updateid"])){
    require_csrf_token($_POST['csrf_token'] ?? null);

    $id = (int)$_POST["updateid"];
    $mark = mysqli_real_escape_string($yhendus, $_POST['mark']);
    $model = mysqli_real_escape_string($yhendus, $_POST['model']);
    $engine = mysqli_real_escape_string($yhendus, $_POST['engine']);
    
    // Validate fuel value
    $allowed_fuel = ['bensiin', 'diisel', 'gaas', 'elekter', 'hübriid'];
    $fuel = isset($_POST['fuel']) && in_array($_POST['fuel'], $allowed_fuel) ? $_POST['fuel'] : '';
    
    $price = (int)$_POST['price'];
    $year = (int)$_POST['year'];
    
    // Validate transmission value
    $allowed_transmission = ['manuaalne', 'automaat', 'poolautomaat'];
    $transmission = isset($_POST['transmission']) && in_array($_POST['transmission'], $allowed_transmission) ? $_POST['transmission'] : '';
    
    $seats = (int)$_POST['seats'];
    $description = mysqli_real_escape_string($yhendus, $_POST['description']);
    $status = mysqli_real_escape_string($yhendus, $_POST['status']);
    
    // Validate required fields
    if (empty($fuel) || empty($transmission)) {
        echo '<div class="alert alert-danger">Palun vali kehtiv kütus ja käigukast.</div>';
        return;
    }
    
    // Get current image or use new one
    $image_path = $_POST['current_image'] ?? $rida['image'];
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = basename($_FILES['image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        $mime = mime_content_type($file_tmp);
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (in_array($file_ext, $allowed_ext, true) && in_array($mime, $allowed_mimes, true) && $_FILES['image']['size'] <= 2 * 1024 * 1024) {
            $new_filename = 'car_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Delete old image if it's a local file
                if (!empty($rida['image']) && strpos($rida['image'], '/uploads/') === 0) {
                    @unlink('..' . $rida['image']);
                }
                $image_path = '/uploads/' . $new_filename;
            }
        }
    }

    $image_path = mysqli_real_escape_string($yhendus, $image_path);
    $paring = "UPDATE cars SET mark = '$mark', model = '$model', engine = '$engine', fuel = '$fuel', price = $price, year = $year, transmission = '$transmission', seats = $seats, description = '$description', status = '$status', image = '$image_path' WHERE cars.id = $id";

    $valjund = mysqli_query($yhendus, $paring);
    if ($valjund) {
        header("Location: index.php?msg=uuendatud");
        exit();
    }
}
?>
<?php include('../header.php'); ?>

<?php
    if(isset($_POST["updateid"]) && !$valjund) {
        echo '<div class="alert alert-danger">Viga uuendamisel: ' . mysqli_error($yhendus) . '</div>';
    }
?>

<!-- sisu -->
<div class="container">
    <h2>Auto muutmine</h2>
    <?php if (!empty($rida)): ?>
    <form action="muuda.php" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="row g-4">
            <div class="col-sm-6">
                <input type="hidden" name="updateid" value="<?= $rida['id']; ?>">
                <input type="hidden" name="current_image" value="<?= htmlspecialchars($rida['image']); ?>">

                <label for="mark" class="form-label">Mark</label>
                <input type="text" class="form-control" id="mark" name="mark" value="<?= htmlspecialchars($rida['mark']); ?>" required>
                <label for="model" class="form-label">Model</label>
                <input type="text" class="form-control" id="model" name="model" value="<?= htmlspecialchars($rida['model']); ?>" required>
                <label for="engine" class="form-label">Mootor</label>
                <input type="text" class="form-control" id="engine" name="engine" value="<?= htmlspecialchars($rida['engine']); ?>" required>
                <label for="fuel" class="form-label">Kütus</label>
                <select class="form-control" id="fuel" name="fuel" required>
                    <option value="">Vali kütus</option>
                    <option value="bensiin" <?= $rida['fuel'] === 'bensiin' ? 'selected' : ''; ?>>Bensiin</option>
                    <option value="diisel" <?= $rida['fuel'] === 'diisel' ? 'selected' : ''; ?>>Diisel</option>
                    <option value="gaas" <?= $rida['fuel'] === 'gaas' ? 'selected' : ''; ?>>Gaas</option>
                    <option value="elekter" <?= $rida['fuel'] === 'elekter' ? 'selected' : ''; ?>>Elekter</option>
                    <option value="hübriid" <?= $rida['fuel'] === 'hübriid' ? 'selected' : ''; ?>>Hübriid</option>
                </select>
                <label for="price" class="form-label">Hind</label>
                <input type="number" class="form-control" id="price" name="price" value="<?= $rida['price']; ?>" required>
            </div>
            <div class="col-sm-6">
                <label for="year" class="form-label">Aasta</label>
                <input type="number" class="form-control" id="year" name="year" value="<?= $rida['year']; ?>" required>
                <label for="transmission" class="form-label">Käigukast</label>
                <select class="form-control" id="transmission" name="transmission" required>
                    <option value="">Vali käigukast</option>
                    <option value="manuaalne" <?= $rida['transmission'] === 'manuaalne' ? 'selected' : ''; ?>>Manuaalne</option>
                    <option value="automaat" <?= $rida['transmission'] === 'automaat' ? 'selected' : ''; ?>>Automaat</option>
                    <option value="poolautomaat" <?= $rida['transmission'] === 'poolautomaat' ? 'selected' : ''; ?>>Poolautomaat</option>
                </select>
                <label for="seats" class="form-label">Istmete arv</label>
                <input type="number" class="form-control" id="seats" name="seats" value="<?= $rida['seats']; ?>" required>
                <label for="description" class="form-label">Muu info</label>
                <input type="text" class="form-control" id="description" name="description" value="<?= htmlspecialchars($rida['description']); ?>">
                <label for="status" class="form-label">Olek</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="vaba" <?= $rida['status'] === 'vaba' ? 'selected' : ''; ?>>Vaba</option>
                    <option value="broneeritud" <?= $rida['status'] === 'broneeritud' ? 'selected' : ''; ?>>Broneeritud</option>
                    <option value="rendidud" <?= $rida['status'] === 'rendidud' ? 'selected' : ''; ?>>Rendidud</option>
                    <option value="hoolduses" <?= $rida['status'] === 'hoolduses' ? 'selected' : ''; ?>>Hoolduses</option>
                </select>
                <label for="image" class="form-label">Uus pilt (JPG, PNG, GIF, WebP)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <small class="text-muted">Jäta tühjaks, et säilitada praegune pilt</small>
                <?php if (!empty($rida['image']) && strpos($rida['image'], '/uploads/') === 0): ?>
                    <div class="mt-2">
                        <small>Praegune pilt:</small><br>
                        <img src="<?= $rida['image']; ?>" style="max-width: 100px; height: auto; margin-top: 5px;">
                    </div>
                <?php endif; ?>
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
