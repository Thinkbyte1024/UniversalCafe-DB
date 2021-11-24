<?php

$DB_HOST = 'database'; // Nama mesin virtual Docker pada Docker Compose
$DB_NAME = 'cafedb';
$DB_USERNAME = 'web_access_only';
$DB_PASSWORD = 'W3b-4access-77';

// Membuka koneksi
$conn_string = "host={$DB_HOST} dbname={$DB_NAME} user={$DB_USERNAME} password={$DB_PASSWORD}";
$connection = pg_connect($conn_string) or die("ERROR: Could not initiate PostgreSQL connection\n" . pg_errormessage($connection));
?>
