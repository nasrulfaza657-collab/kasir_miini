<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Set header untuk PDF
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=Laporan_Transaksi_" . date('Ymd') . ".pdf");

$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');

$query = "SELECT t.*, b.nama_barang 
          FROM transaksi t 
          JOIN barang b ON t.id_barang = b.id 
          WHERE DATE(t.tanggal) BETWEEN '$tanggal_awal' AND '$tanggal_akhir' 
          ORDER BY t.tanggal DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h2 {
            text-align: center;
            margin-bottom: 5px;
        }
        p {
            text-align: center;
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #333;
            color: white;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>

<h2>TOKO MINIMARKET</h2>
<p>Laporan Transaksi Periode: <?php echo date('d/m/Y', strtotime($tanggal_awal)); ?> - <?php echo date('d/m/Y', strtotime($tanggal_akhir)); ?></p>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Harga Satuan</th>
            <th>Total Harga</th>
            <th>Uang Bayar</th>
            <th>Kembalian</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $total_penjualan = 0;
        while ($row = mysqli_fetch_array($result)) {
            $harga_satuan = $row['total_harga'] / $row['jumlah'];
            $total_penjualan += $row['total_harga'];
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo date('d/m/Y H:i:s', strtotime($row['tanggal'])); ?></td>
                <td><?php echo $row['nama_barang']; ?></td>
                <td><?php echo $row['jumlah']; ?></td>
                <td>Rp <?php echo number_format($harga_satuan, 0, ',', '.'); ?></td>
                <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                <td>Rp <?php echo number_format($row['uang_bayar'], 0, ',', '.'); ?></td>
                <td>Rp <?php echo number_format($row['kembalian'], 0, ',', '.'); ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

<div class="total">
    <p>Total Pendapatan: Rp <?php echo number_format($total_penjualan, 0, ',', '.'); ?></p>
</div>

<div class="footer">
    <p>Dicetak pada: <?php echo date('d/m/Y H:i:s'); ?></p>
</div>

</body>
</html>