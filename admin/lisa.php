<?php 
include('../config.php'); 
include('admin_check.php');

$upload_dir = '../uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    require_csrf_token($_POST['csrf_token'] ?? null);

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
    
    // Handle image upload
    $image_path = 'http://dummyimage.com/200x150/cccccc/000000.png&text=no+img'; // default placeholder
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = basename($_FILES['image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Allowed image extensions
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        $mime = mime_content_type($file_tmp);
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (in_array($file_ext, $allowed_ext, true) && in_array($mime, $allowed_mimes, true) && $_FILES['image']['size'] <= 2 * 1024 * 1024) {
            // Generate unique filename
            $new_filename = 'car_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $image_path = '/uploads/' . $new_filename;
            }
        }
    }
    
    $sql = "INSERT INTO cars (mark, model, engine, fuel, price, image, year, transmission, seats, description, status) 
            VALUES ('".$mark."', '".$model."', '".$engine."', '".$fuel."', '".$price."', '".$image_path."', '".$year."', '".$transmission."', '".$seats."', '".$description."', '".$status."')";

    $valjund = mysqli_query($yhendus, $sql); 
    $tulemus = mysqli_affected_rows($yhendus);
    
    if ($tulemus == 1) {
        header("Location: index.php?msg=lisatud");
        exit();
    } else {
        echo "Kirjet ei lisatud: " . mysqli_error($yhendus);
    }
}
?>
<?php include('../header.php'); ?>

<!-- sisu -->
<div class="container">
    <h2>Auto lisamine</h2>
    <form action="lisa.php" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="row g-4">
            <div class="col-sm-6">
                <label for="mark" class="form-label">Mark</label>
                <input type="text" class="form-control" id="mark" name="mark" required>
                <label for="model" class="form-label">Model</label>
                <input type="text" class="form-control" id="model" name="model" required>
                <label for="engine" class="form-label">Mootor</label>
                <input type="text" class="form-control" id="engine" name="engine" required>
                <label for="fuel" class="form-label">Kütus</label>
                <select class="form-control" id="fuel" name="fuel" required>
                    <option value="">Vali kütus</option>
                    <option value="bensiin">Bensiin</option>
                    <option value="diisel">Diisel</option>
                    <option value="gaas">Gaas</option>
                    <option value="elekter">Elekter</option>
                    <option value="hübriid">Hübriid</option>
                </select>
                <label for="price" class="form-label">Hind</label>
                <input type="number" class="form-control" id="price" name="price" required>
            </div>
            <div class="col-sm-6">
                <label for="year" class="form-label">Aasta</label>
                <input type="number" class="form-control" id="year" name="year" required>
                <label for="transmission" class="form-label">Käigukast</label>
                <select class="form-control" id="transmission" name="transmission" required>
                    <option value="">Vali käigukast</option>
                    <option value="manuaalne">Manuaalne</option>
                    <option value="automaat">Automaat</option>
                    <option value="poolautomaat">Poolautomaat</option>
                </select>
                <label for="seats" class="form-label">Istmete arv</label>
                <input type="number" class="form-control" id="seats" name="seats" required>
                <label for="description" class="form-label">Muu info</label>
                <input type="text" class="form-control" id="description" name="description">
                <label for="status" class="form-label">Olek</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="vaba">Vaba</option>
                    <option value="broneeritud">Broneeritud</option>
                    <option value="rendidud">Rendidud</option>
                    <option value="hoolduses">Hoolduses</option>
                </select>
                <label for="image" class="form-label">Pilt (JPG, PNG, GIF, WebP)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <input type="submit" value="Salvesta" class="btn btn-success">
        </div>
    </form>
</div>
<!-- /sisu -->

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
