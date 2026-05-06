<?php
    // Sinu andmed
    $db_server = getenv('DB_HOST') ?: 'localhost';
    $db_andmebaas = getenv('DB_NAME') ?: 'car_rent';
    $db_kasutaja = getenv('DB_USER') ?: 'hannes';
    $db_salasona = getenv('DB_PASSWORD') ?: 'Passw0rd';

    // Ühendus andmebaasiga
    $yhendus = mysqli_connect($db_server, $db_kasutaja, $db_salasona, $db_andmebaas);

    // Ühenduse kontroll
    if (!$yhendus) {
        die('Ei saa ühendust andmebaasiga: ' . mysqli_connect_error());
    }
?>
