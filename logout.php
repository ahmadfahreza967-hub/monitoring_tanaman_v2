<?php
session_start();
session_unset(); // Menghapus semua data variabel session
session_destroy(); // Menghancurkan session login secara total

// Tendang balik ke halaman login utama
header("Location: index.php");
exit;
?>
