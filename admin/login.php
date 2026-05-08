<?php
include('../config.php');

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_token($_POST['csrf_token'] ?? null);

    $uname = trim($_POST['user'] ?? '');
    $password = $_POST['password'] ?? '';

    $rida = db_fetch_one(
        $yhendus,
        'SELECT id, email AS user, password_hash AS password, role FROM users WHERE email = ? LIMIT 1',
        's',
        $uname
    );

    if (!empty($rida) && password_verify($password, $rida['password'])) {
        session_regenerate_id(true);
        $_SESSION['tuvastamine'] = 'misiganes';
        $_SESSION['role'] = $rida['role'];
        $_SESSION['user_id'] = (int)$rida['id'];

        if ($rida['role'] === 'administraator') {
            header('Location: index.php');
            exit();
        }

        header('Location: ../index.php');
        exit();
    }

    $msg = 'Vale e-post või parool.';
}
?>
<!doctype html>
<html lang="et">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
        <div class="row pt-4 mt-4">
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <a href="../index.php" class="btn btn-secondary mb-3">Tagasi</a>
                <form method="post" action="login.php" autocomplete="off">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label for="u" class="form-label">E-post</label>
                        <input name="user" type="email" class="form-control" id="u" required>
                    </div>
                    <div class="mb-3">
                        <label for="p" class="form-label">Parool</label>
                        <input name="password" type="password" class="form-control" id="p" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Logi sisse</button>
                </form>
                <?php if ($msg !== ''): ?>
                    <div class="alert alert-danger mt-3"><?php echo e($msg); ?></div>
                <?php endif; ?>
                <hr>
                <p class="text-center">Kasutajat pole? <a href="../register.php">Registreeri siin</a></p>
            </div>
            <div class="col-sm-4"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
