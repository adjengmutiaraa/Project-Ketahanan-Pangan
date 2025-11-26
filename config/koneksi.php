<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "svasembada";

$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Fungsi keamanan
function sanitize($data) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, htmlspecialchars(strip_tags(trim($data))));
}
?>