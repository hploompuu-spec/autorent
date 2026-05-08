<?php
require_once __DIR__ . '/security.php';

$db_server = getenv('DB_HOST') ?: 'localhost';
$db_andmebaas = getenv('DB_NAME') ?: 'car_rent';
$db_kasutaja = getenv('DB_USER') ?: 'hannes';
$db_salasona = getenv('DB_PASSWORD') ?: 'Passw0rd';

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $yhendus = mysqli_connect($db_server, $db_kasutaja, $db_salasona, $db_andmebaas);
    mysqli_set_charset($yhendus, 'utf8mb4');
} catch (mysqli_sql_exception $exception) {
    error_log($exception->getMessage());
    http_response_code(500);
    exit('Andmebaasi ühendus ebaõnnestus.');
}
