<?php
$conn = mysqli_connect("localhost", "root", "", "kasir_mini");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
