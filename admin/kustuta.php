<?php include('admin_check.php'); ?>
<?php include('../config.php'); ?>
<?php
    if (!empty($_GET['delid'])) {
        require_csrf_token($_GET['csrf_token'] ?? null);
        $id = (int)$_GET['delid'];
        $paring = "DELETE FROM cars WHERE id=$id";
        $valjund = mysqli_query($yhendus, $paring);
        if ($valjund) {
            header("Location: index.php?msg=kustutatud");
            exit();
        } else {
            header("Location: index.php?msg=error");
            exit();
        }
    }

    
?>
