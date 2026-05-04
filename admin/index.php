<?php
session_start();

if (!isset($_SESSION['tuvastamine']) || $_SESSION['role'] !== 'administraator') {
  header('Location: login.php');
  exit();
  }

?>

<?php include('../config.php'); ?>
<?php include('../header.php'); ?>

<!-- sisu -->
<div class="container">
    <h2>Adminni ala</h2>
    <div class="mb-3">
        <a href="lisa.php" class="btn btn-success">+ Lisa auto</a>
        <a href="users.php" class="btn btn-primary ms-2">Halda kasutajaid</a>
    </div>
    <div class="row row-cols-1 row-cols-md-4 g-4">
<!-- üks auto -->
<?php
    // sõnumi kuvamine
    if(isset($_GET['msg'])){
     // echo '<div class="alert alert-success" role="alert"> Kõik on hästi! </div>';
    }


  //autode kuvamine
    $paring = "SELECT * FROM cars";
    
    // Build WHERE clause for search
    $search_param = "";
    if (!empty($_GET["otsi"])) {
        $otsing = $_GET["otsi"];
        $paring .= " WHERE mark LIKE '%".$otsing."%'";
        $search_param = "&otsi=" . urlencode($otsing);
    }
    
    // Handle sorting
    $sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'id';
    $sort_direction = isset($_GET['direction']) ? $_GET['direction'] : 'ASC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['id', 'mark', 'model', 'engine', 'fuel', 'year', 'transmission', 'seats', 'status', 'price'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'id';
    }
    
    // Toggle direction if same column is clicked
    $next_direction = ($sort_direction === 'ASC') ? 'DESC' : 'ASC';
    
    $paring .= " ORDER BY " . $sort_column . " " . $sort_direction;
    
    $sort_param = "&sort=" . $sort_column . "&direction=" . $sort_direction;

    $valjund = mysqli_query($yhendus, $paring); //saadan päringu andmebaasi

?>

<table class="table">
  <thead>
    <tr>
      <th scope="col"><a href="?sort=id&direction=<?php echo ($sort_column === 'id' ? $next_direction : 'ASC'); echo $search_param; ?>" style="text-decoration: <?php echo ($sort_column === 'id') ? 'underline' : 'none'; ?>; color: inherit;">
        # <?php echo ($sort_column === 'id') ? ($sort_direction === 'ASC' ? '↑' : '↓') : ''; ?>
      </a></th>
      <th scope="col">Pilt</th>
      <th scope="col"><a href="?sort=mark&direction=<?php echo ($sort_column === 'mark' ? $next_direction : 'ASC'); echo $search_param; ?>" style="text-decoration: <?php echo ($sort_column === 'mark') ? 'underline' : 'none'; ?>; color: inherit;">
        Mark <?php echo ($sort_column === 'mark') ? ($sort_direction === 'ASC' ? '↑' : '↓') : ''; ?>
      </a></th>
      <th scope="col"><a href="?sort=model&direction=<?php echo ($sort_column === 'model' ? $next_direction : 'ASC'); echo $search_param; ?>" style="text-decoration: <?php echo ($sort_column === 'model') ? 'underline' : 'none'; ?>; color: inherit;">
        Mudel <?php echo ($sort_column === 'model') ? ($sort_direction === 'ASC' ? '↑' : '↓') : ''; ?>
      </a></th>
      <th scope="col"><a href="?sort=engine&direction=<?php echo ($sort_column === 'engine' ? $next_direction : 'ASC'); echo $search_param; ?>" style="text-decoration: <?php echo ($sort_column === 'engine') ? 'underline' : 'none'; ?>; color: inherit;">
        Mootor <?php echo ($sort_column === 'engine') ? ($sort_direction === 'ASC' ? '↑' : '↓') : ''; ?>
      </a></th>
      <th scope="col"><a href="?sort=fuel&direction=<?php echo ($sort_column === 'fuel' ? $next_direction : 'ASC'); echo $search_param; ?>" style="text-decoration: <?php echo ($sort_column === 'fuel') ? 'underline' : 'none'; ?>; color: inherit;">
        Kütus <?php echo ($sort_column === 'fuel') ? ($sort_direction === 'ASC' ? '↑' : '↓') : ''; ?>
      </a></th>
      <th scope="col"><a href="?sort=year&direction=<?php echo ($sort_column === 'year' ? $next_direction : 'ASC'); echo $search_param; ?>" style="text-decoration: <?php echo ($sort_column === 'year') ? 'underline' : 'none'; ?>; color: inherit;">
        Aasta <?php echo ($sort_column === 'year') ? ($sort_direction === 'ASC' ? '↑' : '↓') : ''; ?>
      </a></th>
      <th scope="col"><a href="?sort=transmission&direction=<?php echo ($sort_column === 'transmission' ? $next_direction : 'ASC'); echo $search_param; ?>" style="text-decoration: <?php echo ($sort_column === 'transmission') ? 'underline' : 'none'; ?>; color: inherit;">
        Käigukast <?php echo ($sort_column === 'transmission') ? ($sort_direction === 'ASC' ? '↑' : '↓') : ''; ?>
      </a></th>
      <th scope="col"><a href="?sort=seats&direction=<?php echo ($sort_column === 'seats' ? $next_direction : 'ASC'); echo $search_param; ?>" style="text-decoration: <?php echo ($sort_column === 'seats') ? 'underline' : 'none'; ?>; color: inherit;">
        Istekohti <?php echo ($sort_column === 'seats') ? ($sort_direction === 'ASC' ? '↑' : '↓') : ''; ?>
      </a></th>
      <th scope="col">Kirjeldus</th>
      <th scope="col"><a href="?sort=status&direction=<?php echo ($sort_column === 'status' ? $next_direction : 'ASC'); echo $search_param; ?>" style="text-decoration: <?php echo ($sort_column === 'status') ? 'underline' : 'none'; ?>; color: inherit;">
        Staatus <?php echo ($sort_column === 'status') ? ($sort_direction === 'ASC' ? '↑' : '↓') : ''; ?>
      </a></th>
      <th scope="col"><a href="?sort=price&direction=<?php echo ($sort_column === 'price' ? $next_direction : 'ASC'); echo $search_param; ?>" style="text-decoration: <?php echo ($sort_column === 'price') ? 'underline' : 'none'; ?>; color: inherit;">
        Hind <?php echo ($sort_column === 'price') ? ($sort_direction === 'ASC' ? '↑' : '↓') : ''; ?>
      </a></th>
      <th scope="col">Kustuta</th>
      <th scope="col">Muuda</th>
    </tr>
  </thead>
  <tbody>
    <?php
        while($rida = mysqli_fetch_assoc($valjund)){       //sikutan vastuse alla
            // var_dump($rida);                       //kuvan testvastuse
    ?>
    <?php
            $imgSrc = $rida["image"];
            if (!preg_match('/^https?:\/\//', $imgSrc) && strpos($imgSrc, '/') === 0) {
                $imgSrc = '/car_rent' . $imgSrc;
            }
            $placeholder = 'https://dummyimage.com/50x50/cccccc/000000.png&text=no+img';
            if (!preg_match('/^https?:\/\//', $imgSrc)) {
                $localPath = $_SERVER['DOCUMENT_ROOT'] . str_replace('/', DIRECTORY_SEPARATOR, $imgSrc);
                if (!file_exists($localPath)) {
                    $imgSrc = $placeholder;
                }
            }
    ?>
    <tr>
      <th scope="row"><?php echo $rida["id"]; ?></th>
      <td><img src="<?php echo $imgSrc; ?>" width="50" alt="<?php echo htmlspecialchars($rida["mark"] . ' ' . $rida["model"]); ?>"></td>
      <td><?php echo $rida["mark"]; ?></td>
      <td><?php echo $rida["model"]; ?></td>
      <td><?php echo $rida["engine"]; ?></td>
      <td><?php echo $rida["fuel"]; ?></td>
      <td><?php echo $rida["year"]; ?></td>
      <td><?php echo $rida["transmission"]; ?></td>
      <td><?php echo $rida["seats"]; ?></td>
      <td><?php echo $rida["description"]; ?></td>
      <td><?php echo $rida["status"]; ?></td>
      <td><?php echo $rida["price"]; ?></td>
      <td><a href="kustuta.php?delid=<?= $rida["id"]; ?>" class="btn btn-danger">Kustuta</a></td>
      <td><a href="muuda.php?editid=<?= $rida["id"]; ?>" class="btn btn-warning">Muuda</a></td>
    </tr>

    <?php } ?>

  </tbody>
</table>

</div>
<!-- /sisu -->

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>