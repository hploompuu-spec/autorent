<?php
include('config.php');

$msg = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_token($_POST['csrf_token'] ?? null);

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    $errors = [];

    if ($first_name === '') {
        $errors[] = 'Eesnimi on kohustuslik';
    }
    if ($last_name === '') {
        $errors[] = 'Perekonnanimi on kohustuslik';
    }
    if ($email === '') {
        $errors[] = 'E-mail on kohustuslik';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'E-mail ei ole kehtiv';
    }
    if ($phone === '') {
        $errors[] = 'Telefon on kohustuslik';
    }
    if ($password === '') {
        $errors[] = 'Parool on kohustuslik';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Parool peab olema vähemalt 8 tähemärki pikk';
    }
    if ($password !== $password_confirm) {
        $errors[] = 'Paroolid ei ühti';
    }

    if (empty($errors)) {
        $existing_user = db_fetch_one($yhendus, 'SELECT id FROM users WHERE email = ?', 's', $email);
        if ($existing_user !== null) {
            $errors[] = 'See e-mail on juba registreeritud';
        }
    }

    if (!empty($errors)) {
        $msg = implode('<br>', array_map('e', $errors));
        $msg_type = 'error';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $created = db_execute(
            $yhendus,
            "INSERT INTO users (role, first_name, last_name, email, phone, password_hash, created_at) VALUES ('kasutaja', ?, ?, ?, ?, ?, NOW())",
            'sssss',
            $first_name,
            $last_name,
            $email,
            $phone,
            $password_hash
        );

        if ($created) {
            $msg = "Registreerimine õnnestus! Nüüd saate <a href='admin/login.php'>sisse logida</a>.";
            $msg_type = 'success';
            $_POST = [];
        } else {
            $msg = 'Viga registreerimisel.';
            $msg_type = 'error';
        }
    }
}
?>
<!doctype html>
<html lang="et">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registreerimine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
        <div class="row pt-4 mt-4">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <h2 class="mb-4">Registreerimine</h2>

                <?php if ($msg !== ''): ?>
                    <div class="alert alert-<?php echo $msg_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                        <?php echo $msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="post" action="register.php" autocomplete="off">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Eesnimi</label>
                            <input name="first_name" type="text" class="form-control" id="first_name" value="<?php echo isset($_POST['first_name']) ? e($_POST['first_name']) : ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Perekonnanimi</label>
                            <input name="last_name" type="text" class="form-control" id="last_name" value="<?php echo isset($_POST['last_name']) ? e($_POST['last_name']) : ''; ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input name="email" type="email" class="form-control" id="email" value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon</label>
                        <input name="phone" type="tel" class="form-control" id="phone" value="<?php echo isset($_POST['phone']) ? e($_POST['phone']) : ''; ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Parool</label>
                            <input name="password" type="password" class="form-control" id="password" minlength="8" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirm" class="form-label">Kinnita parool</label>
                            <input name="password_confirm" type="password" class="form-control" id="password_confirm" minlength="8" required>
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
