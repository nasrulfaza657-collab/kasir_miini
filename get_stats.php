<?php
header('Content-Type: application/json');
include "koneksi.php";

// Hitung total barang
$query_barang = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang");
$total_barang = mysqli_fetch_assoc($query_barang)['total'];

// Hitung total transaksi (jika ada tabel transaksi)
$query_transaksi = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi");
$total_transaksi = mysqli_fetch_assoc($query_transaksi)['total'];

echo json_encode([
    'totalBarang' => $total_barang,
    'totalTransaksi' => $total_transaksi
]);
?>