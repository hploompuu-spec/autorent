<?php 
    session_start(); 
    include('config.php');
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registreerimine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
<?php
    $msg = "";
    $msg_type = "";

    if (!empty($_POST)) {
        // Get form data
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];

        // Validation
        $errors = array();

        if (empty($first_name)) {
            $errors[] = "Eesnimi on kohustuslik";
        }
        if (empty($last_name)) {
            $errors[] = "Perekonnanimi on kohustuslik";
        }
        if (empty($email)) {
            $errors[] = "E-mail on kohustuslik";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "E-mail ei ole kehtiv";
        }
        if (empty($phone)) {
            $errors[] = "Telefon on kohustuslik";
        }
        if (empty($password)) {
            $errors[] = "Parool on kohustuslik";
        } elseif (strlen($password) < 6) {
            $errors[] = "Parool peab olema vähemalt 6 tähemärki pikk";
        }
        if ($password !== $password_confirm) {
            $errors[] = "Paroolid ei ühthi";
        }

        // Check if email already exists
        if (empty($errors)) {
            $check_email = "SELECT email FROM users WHERE email='".$email."'";
            $result = mysqli_query($yhendus, $check_email);
            if (mysqli_num_rows($result) > 0) {
                $errors[] = "See e-mail on juba registreeritud";
            }
        }

        if (!empty($errors)) {
            $msg = implode("<br>", $errors);
            $msg_type = "error";
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Get the next ID
            $id_query = "SELECT MAX(id) as max_id FROM users";
            $id_result = mysqli_query($yhendus, $id_query);
            $id_row = mysqli_fetch_assoc($id_result);
            $next_id = $id_row['max_id'] + 1;

            // Insert into database
            $insert_query = "INSERT INTO users (id, role, first_name, last_name, email, phone, password_hash, created_at) 
                            VALUES (".$next_id.", 'kasutaja', '".$first_name."', '".$last_name."', '".$email."', '".$phone."', '".$password_hash."', NOW())";
            
            if (mysqli_query($yhendus, $insert_query)) {
                $msg = "Registreerimine õnnestus! Nüüd saate <a href='admin/login.php'>sisse logida</a>";
                $msg_type = "success";
                // Clear form
                $_POST = array();
            } else {
                $msg = "Viga registreerimisel: " . mysqli_error($yhendus);
                $msg_type = "error";
            }
        }
    }

?>
    <div class="container">
        <div class="row pt-4 mt-4">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <h2 class="mb-4">Registreerimine</h2>
                
                <?php if (!empty($msg)): ?>
                    <div class="alert alert-<?php echo ($msg_type === 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                        <?php echo $msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="post" action="register.php" autocomplete="off">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Eesnimi</label>
                            <input name="first_name" type="text" class="form-control" id="first_name" value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Perekonnanimi</label>
                            <input name="last_name" type="text" class="form-control" id="last_name" value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input name="email" type="email" class="form-control" id="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon</label>
                        <input name="phone" type="tel" class="form-control" id="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Parool</label>
                            <input name="password" type="password" class="form-control" id="password">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirm" class="form-label">Kinnita parool</label>
                            <input name="password_confirm" type="password" class="form-control" id="password_confirm">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Registreerimine</button>
                    <a href="admin/login.php" class="btn btn-secondary">Juba kasutaja? Logi sisse</a>
                </form>
            </div>
            <div class="col-sm-2"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
