<?php
// config/database.php

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_lomba_prestasi';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>