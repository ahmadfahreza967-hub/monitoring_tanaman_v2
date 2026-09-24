<?php
$host = "localhost";
$user = "root";
$pass = ""; // Kosongkan jika belum membuat password di XAMPP
$db   = "web_login"; // Nama database yang Anda buat di CMD kemarin

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>