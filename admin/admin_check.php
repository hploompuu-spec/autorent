<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['tuvastamine']) || $_SESSION['role'] !== 'administraator') {
  header('Location: login.php');
  exit();
}
?>