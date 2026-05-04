<?php 
    session_start(); 
    include('../config.php');
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
<?php
    $msg = "";

    if (!empty($_POST)) {

        //kasutaja vormist
        $uname = $_POST['user'];
        $password = $_POST['password'];

        //kasutaja andmebaasist
        $paring = "SELECT id, email as user, password_hash as password, role FROM users WHERE email='".$uname."'";
        $valjund = mysqli_query($yhendus, $paring);
        $rida = mysqli_fetch_assoc($valjund);

        if (!empty($rida)) {
            $hash = $rida['password'];
            if ($uname== $rida['user'] && password_verify($password, $hash)) {
                $_SESSION['tuvastamine'] = 'misiganes';
                $_SESSION['role'] = $rida['role'];
                $_SESSION['user_id'] = $rida['id'];
                if ($rida['role'] == 'administraator') {
                    header("Location: index.php");
                    exit();
                } else {
                    header("Location: ../index.php");
                    exit();
                }
            }else{
                $msg = "kasutaja vale";
            }
        }

    }

?>
    <div class="container">
        <div class="row pt-4 mt-4">
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <a href="../index.php" class="btn btn-secondary mb-3">Tagasi</a>
                <form method="post" action="login.php" autocomplete="off">
                    <div class="mb-3">
                        <label for="u" class="form-label">E-post</label>
                        <input name="user" type="text" class="form-control" id="u">
                    </div>
                    <div class="mb-3">
                        <label for="p" class="form-label">Parool</label>
                        <input name="password" type="password" class="form-control" id="p" >
                    </div>
                    <button type="submit" class="btn btn-primary">Logi sisse</button>
                </form>
                <?php echo $msg; ?>
                <hr>
                <p class="text-center">Kasutajat pole? <a href="../register.php">Registreeri siin</a></p>
            </div>
            <div class="col-sm-4"></div>
        </div>
    </div>
  


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>