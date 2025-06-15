<?php
session_start();

// Hapus semua session
session_unset();
session_destroy();

// Redirect ke halaman login admin
header("Location: admin_login.php"); // Ganti dengan login.php kalau itu nama file login kamu
exit;
?>
