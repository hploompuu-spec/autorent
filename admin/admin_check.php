<?php
require_once __DIR__ . '/../security.php';

if (empty($_SESSION['tuvastamine']) || ($_SESSION['role'] ?? '') !== 'administraator') {
    header('Location: login.php');
    exit();
}
