<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="et">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Autorent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
    <!-- menüü -->
      <nav class="navbar navbar-expand-lg bg-body-tertiary mb-4">
  <div class="container">
    <?php
        $currentScript = $_SERVER['SCRIPT_NAME'] ?? '';
        $isAdminUser = !empty($_SESSION['role']) && $_SESSION['role'] === 'administraator';
        $showAdminNav = $isAdminUser;
        $reservationsLabel = 'Broneeringud';
    ?>
    <a class="navbar-brand" href="/car_rent/index.php">Autorent</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" href="/car_rent/index.php">Pealehele</a>
        </li>
        <?php if ($showAdminNav): ?>
        <li class="nav-item">
          <a class="nav-link" href="/car_rent/admin/index.php">Admin. pealehele</a>
        </li>
        <?php endif; ?>
        <?php if ($isAdminUser): ?>
        <li class="nav-item">
          <a class="nav-link" href="/car_rent/admin/users.php">Kasutajad</a>
        </li>
        <?php endif; ?>
        <?php if (!empty($_SESSION['tuvastamine'])): ?>
        <li class="nav-item">
          <a class="nav-link" href="/car_rent/admin/my_rentals.php"><?php echo $reservationsLabel; ?></a>
        </li>
        <?php endif; ?>

      </ul>
      <form class="d-flex" role="search" method="get" action="/car_rent/index.php">
        <input class="form-control me-2" type="search" placeholder="Otsi autot" aria-label="Search" name="otsi">
        <button class="btn btn-outline-success" type="submit">Otsi</button>
      </form>
      <?php if (!empty($_SESSION['tuvastamine'])): ?>
        <a href="/car_rent/admin/logout.php" class="ms-4 btn btn-danger">Logi välja</a>
      <?php else: ?>
        <a href="/car_rent/admin/login.php" class="ms-2 btn btn-primary">Logi sisse</a>
        <a href="/car_rent/register.php" class="ms-2 btn btn-success">Registreeri</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
    <!-- /menüü -->