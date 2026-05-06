<?php
include "koneksi.php";

echo "<h2>Data di Tabel Transaksi</h2>";

$query = mysqli_query($conn, "SELECT * FROM transaksi ORDER BY id DESC LIMIT 10");
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr>
        <th>ID</th>
        <th>id_barang</th>
        <th>nama_barang</th>
        <th>jumlah</th>
        <th>tanggal</th>
        <th>total</th>
        <th>bayar</th>
        <th>kembalian</th>
      </tr>";

if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_array($query)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . ($row['id_barang'] ?? '-') . "</td>";
        echo "<td>" . ($row['nama_barang'] ?? '-') . "</td>";
        echo "<td>" . ($row['jumlah'] ?? '-') . "</td>";
        echo "<td>" . $row['tanggal'] . "</td>";
        echo "<td>" . number_format($row['total'], 0, ',', '.') . "</td>";
        echo "<td>" . number_format($row['bayar'], 0, ',', '.') . "</td>";
        echo "<td>" . number_format($row['kembalian'], 0, ',', '.') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>Belum ada data transaksi</td></tr>";
}
echo "</table>";

echo "<h2>Struktur Tabel Transaksi</h2>";
$struktur = mysqli_query($conn, "DESCRIBE transaksi");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
while ($kolom = mysqli_fetch_array($struktur)) {
    echo "<tr>";
    echo "<td>" . $kolom['Field'] . "</td>";
    echo "<td>" . $kolom['Type'] . "</td>";
    echo "<td>" . $kolom['Null'] . "</td>";
    echo "<td>" . $kolom['Key'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>