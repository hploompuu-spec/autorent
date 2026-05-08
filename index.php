<?php include('config.php'); ?>
<?php include('header.php'); ?>

<!-- sisu -->
<div class="container">
<!-- sisu -->
<div class="container">
    <!-- Sorting Controls -->
    <div class="row mb-4">
        <div class="col-12 text-end">
            <form method="get" class="d-inline">
                <label for="sort" class="me-2">Sorteeri:</label>
                <select name="sort" id="sort" class="form-select d-inline w-auto" onchange="this.form.submit()">
                    <option value="mark_ASC" <?php echo (($_GET['sort'] ?? 'id_ASC') === 'mark_ASC') ? 'selected' : ''; ?>>Mark (A kuni Z)</option>
                    <option value="mark_DESC" <?php echo (($_GET['sort'] ?? 'id_ASC') === 'mark_DESC') ? 'selected' : ''; ?>>Mark (Z kuni A)</option>
                    <option value="model_ASC" <?php echo (($_GET['sort'] ?? 'id_ASC') === 'model_ASC') ? 'selected' : ''; ?>>Mudel (A kuni Z)</option>
                    <option value="model_DESC" <?php echo (($_GET['sort'] ?? 'id_ASC') === 'model_DESC') ? 'selected' : ''; ?>>Mudel (Z kuni A)</option>
                    <option value="price_ASC" <?php echo (($_GET['sort'] ?? 'id_ASC') === 'price_ASC') ? 'selected' : ''; ?>>Hind (kasvavalt)</option>
                    <option value="price_DESC" <?php echo (($_GET['sort'] ?? 'id_ASC') === 'price_DESC') ? 'selected' : ''; ?>>Hind (kahanevalt)</option>
                    <option value="year_DESC" <?php echo (($_GET['sort'] ?? 'id_ASC') === 'year_DESC') ? 'selected' : ''; ?>>Aasta (uusimast)</option>
                    <option value="year_ASC" <?php echo (($_GET['sort'] ?? 'id_ASC') === 'year_ASC') ? 'selected' : ''; ?>>Aasta (vanimast)</option>
                </select>
            </form>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-4 g-4">
<!-- üks auto -->
<?php
    // Pagination settings
    $items_per_page = 12;
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($current_page < 1) $current_page = 1;
    $offset = ($current_page - 1) * $items_per_page;

    // Handle sorting
    $sort_options = [
        'id_ASC' => 'id ASC',
        'id_DESC' => 'id DESC',
        'mark_ASC' => 'mark ASC',
        'mark_DESC' => 'mark DESC',
        'model_ASC' => 'model ASC',
        'model_DESC' => 'model DESC',
        'price_ASC' => 'price ASC',
        'price_DESC' => 'price DESC',
        'year_ASC' => 'year ASC',
        'year_DESC' => 'year DESC'
    ];
    
    $sort_param_get = $_GET['sort'] ?? 'id_ASC';
    $order_by = isset($sort_options[$sort_param_get]) ? $sort_options[$sort_param_get] : 'id ASC';
    $sort_param = "&sort=" . urlencode($sort_param_get);

    // Get total count
    $count_paring = "SELECT COUNT(*) as total FROM cars";
    $count_result = mysqli_query($yhendus, $count_paring);
    $count_row = mysqli_fetch_assoc($count_result);
    $total_items = $count_row['total'];
    $total_pages = ceil($total_items / $items_per_page);

    // Get paginated and sorted results
    $paring = "SELECT * FROM cars ORDER BY " . $order_by . " LIMIT " . $items_per_page . " OFFSET " . $offset;
    $valjund = mysqli_query($yhendus, $paring);
    
    while($rida = mysqli_fetch_assoc($valjund)){       //sikutan vastuse alla
        // var_dump($rida);                            //kuvan testvastuse
?>
    <div class="col">
        <div class="card">
        <img src="https://loremflickr.com/400/250/<?php echo rawurlencode(str_replace(' ', '', $rida["mark"])); ?>" class="card-img-top" alt="<?php echo e($rida["mark"]); ?>">
        <div class="card-body">
            <h5 class="card-title"><?php echo e($rida["mark"]); ?> <?php echo e($rida["model"]); ?></h5>
            <p class="card-text">
                Mootor: <?php echo e($rida["engine"]); ?> <br>
                Kütus: <?php echo e($rida["fuel"]); ?><br>
                Hind: <?php echo e($rida["price"]); ?>€/päev<br>
            </p>
            <a href="single_car.php?id=<?php echo (int)$rida["id"]; ?>" class="btn btn-dark w-100">Rendi</a>
        </div>
        </div>
    </div>
    <?php } ?>
        <!-- /üks auto -->
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation" class="mt-5">
        <ul class="pagination justify-content-center">
            <?php if ($current_page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="index.php?page=1<?php echo $sort_param; ?>">Esimene</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="index.php?page=<?php echo ($current_page - 1) . $sort_param; ?>">Eelmine</a>
                </li>
            <?php endif; ?>

            <?php
            // Show page numbers (max 5 pages at a time)
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++):
            ?>
                <li class="page-item <?php echo ($i === $current_page) ? 'active' : ''; ?>">
                    <a class="page-link" href="index.php?page=<?php echo $i . $sort_param; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="index.php?page=<?php echo ($current_page + 1) . $sort_param; ?>">Järgmine</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="index.php?page=<?php echo $total_pages . $sort_param; ?>">Viimane</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <!-- /Pagination -->
</div>
<!-- /sisu -->

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
