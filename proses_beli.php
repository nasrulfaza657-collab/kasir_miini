<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = $_POST['id_barang'];
    $jumlah = $_POST['jumlah'];
    $uang_bayar = $_POST['uang_bayar'];
    
    // Ambil data barang
    $query = mysqli_query($conn, "SELECT * FROM barang WHERE id = '$id_barang'");
    $barang = mysqli_fetch_array($query);
    
    if (!$barang) {
        echo "<script>alert('Barang tidak ditemukan!'); window.location.href='tampil_barang.php';</script>";
        exit();
    }
    
    $harga = $barang['harga'];
    $stok_lama = $barang['stok'];
    $total_harga = $harga * $jumlah;
    
    // Cek stok
    if ($jumlah > $stok_lama) {
        echo "<script>alert('Stok tidak mencukupi! Stok tersedia: $stok_lama'); window.location.href='tampil_barang.php';</script>";
        exit();
    }
    
    // Cek uang bayar
    if ($uang_bayar < $total_harga) {
        $kurang = $total_harga - $uang_bayar;
        echo "<script>alert('Uang bayar kurang! Kekurangan: Rp " . number_format($kurang, 0, ',', '.') . "'); window.location.href='tampil_barang.php';</script>";
        exit();
    }
    
    $kembalian = $uang_bayar - $total_harga;
    $stok_baru = $stok_lama - $jumlah;
    
    // Update stok barang
    $update_stok = mysqli_query($conn, "UPDATE barang SET stok = '$stok_baru' WHERE id = '$id_barang'");
    
    if ($update_stok) {
        // Simpan ke tabel transaksi
        $tanggal = date('Y-m-d H:i:s');
        $insert_transaksi = mysqli_query($conn, "INSERT INTO transaksi (id_barang, jumlah, total_harga, uang_bayar, kembalian, tanggal) VALUES ('$id_barang', '$jumlah', '$total_harga', '$uang_bayar', '$kembalian', '$tanggal')");
        
        // Tampilkan struk
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Struk Pembayaran</title>
            <style>
                body {
                    font-family: monospace;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    background: #f5f5f5;
                }
                .struk {
                    background: white;
                    width: 320px;
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                }
                .struk h3 {
                    text-align: center;
                    margin-bottom: 10px;
                }
                .garis {
                    border-top: 1px dashed #000;
                    margin: 10px 0;
                }
                .row {
                    display: flex;
                    justify-content: space-between;
                    margin: 8px 0;
                }
                .total {
                    font-weight: bold;
                    border-top: 1px dashed #000;
                    border-bottom: 1px dashed #000;
                    padding: 10px 0;
                    margin: 10px 0;
                }
                .kembalian {
                    color: green;
                    font-weight: bold;
                }
                .footer {
                    text-align: center;
                    margin-top: 15px;
                    font-size: 11px;
                }
                button {
                    display: block;
                    width: 100%;
                    margin-top: 10px;
                    padding: 10px;
                    background: #28a745;
                    color: white;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                }
                @media print {
                    button { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="struk">
                <h3>TOKO MINIMARKET</h3>
                <p style="text-align:center">Jl. Raya No. 123<br>Telp: 08123456789</p>
                <div class="garis"></div>
                <div class="row">
                    <span><?php echo $barang['nama']; ?></span>
                    <span><?php echo $jumlah; ?> x Rp <?php echo number_format($harga, 0, ',', '.'); ?></span>
                </div>
                <div class="row total">
                    <span>Total</span>
                    <span>Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></span>
                </div>
                <div class="row">
                    <span>Tunai</span>
                    <span>Rp <?php echo number_format($uang_bayar, 0, ',', '.'); ?></span>
                </div>
                <div class="row kembalian">
                    <span>Kembalian</span>
                    <span>Rp <?php echo number_format($kembalian, 0, ',', '.'); ?></span>
                </div>
                <div class="garis"></div>
                <div class="footer">
                    Terima Kasih<br>
                    Barang yang sudah dibeli tidak dapat dikembalikan<br>
                    <?php echo date('d/m/Y H:i:s'); ?>
                </div>
                <button onclick="window.print()">🖨️ Cetak Struk</button>
                <button onclick="window.location.href='tampil_barang.php'">← Kembali</button>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "<script>alert('Gagal memproses pembelian!'); window.location.href='tampil_barang.php';</script>";
    }
}
?>