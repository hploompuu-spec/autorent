<?php
session_start();

if (!isset($_SESSION['tuvastamine']) || $_SESSION['role'] !== 'administraator') {
  header('Location: login.php');
  exit();
}
?>