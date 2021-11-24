<?php
// Buat sesi baru
session_start();

$_SESSION = array();

// Hancurkan sesi lama
session_destroy();

$cookie_days = 30; // Waktu cookie dalam hari

// Hancurkan cookie
setcookie("MEMBERID_", "", time() - ($cookie_days * 24 * 60 * 60), "/");

header("location: /");
exit();
?>
