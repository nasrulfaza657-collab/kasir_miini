<?php
$data = mysqli_query($conn, "SELECT * FROM transaksi");
while ($d = mysqli_fetch_array($data)) {
    echo "Total: ".$d['total']." | Bayar: ".$d['bayar']." | Kembalian: ".$d['kembalian']."<br>";
}
?>
