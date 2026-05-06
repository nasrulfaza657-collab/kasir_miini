<?php
include "koneksi.php";

// Fungsi hitung diskon
function hitungDiskon($harga, $diskon) {
    $total = $harga - ($harga * $diskon / 100);
    return $total;
}

// Fungsi hitung kembalian
function hitungKembalian($bayar, $total) {
    if ($bayar >= $total) {
        return $bayar - $total;
    } else {
        return 0;
    }
}

// Cek apakah ada data yang dikirim
if (isset($_POST['harga'])) {
    
    // Ambil data dari form
    $harga = $_POST['harga'];
    $diskon = $_POST['diskon'];
    $bayar = $_POST['bayar'];
    
    // Validasi 1: Harga harus lebih dari 0
    if ($harga <= 0) {
        echo "<script>
                alert('Harga tidak valid!');
                window.location.href = 'form_transaksi.php';
              </script>";
        exit();
    }
    
    // Validasi 2: Diskon tidak boleh lebih dari 100%
    if ($diskon > 100) {
        echo "<script>
                alert('Diskon tidak boleh lebih dari 100%!');
                window.location.href = 'form_transaksi.php';
              </script>";
        exit();
    }
    
    // Hitung total setelah diskon
    $total = hitungDiskon($harga, $diskon);
    
    // VALIDASI UTAMA: CEK UANG BAYAR KURANG
    if ($bayar < $total) {
        $kekurangan = $total - $bayar;
        
        // Tampilkan pesan error dengan detail
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Pembayaran Gagal</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    background-color: #f5f5f5;
                }
                .error-container {
                    background-color: white;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    max-width: 400px;
                    text-align: center;
                }
                .error-icon {
                    font-size: 60px;
                    color: #f44336;
                    margin-bottom: 20px;
                }
                .error-title {
                    color: #f44336;
                    font-size: 24px;
                    margin-bottom: 20px;
                }
                .detail {
                    background-color: #f9f9f9;
                    padding: 15px;
                    border-radius: 5px;
                    margin: 20px 0;
                    text-align: left;
                }
                .detail-item {
                    margin: 10px 0;
                    font-size: 16px;
                }
                .detail-item strong {
                    display: inline-block;
                    width: 120px;
                }
                .kekurangan {
                    color: #f44336;
                    font-size: 18px;
                    font-weight: bold;
                    margin-top: 10px;
                    padding-top: 10px;
                    border-top: 1px solid #ddd;
                }
                button {
                    background-color: #4CAF50;
                    color: white;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 16px;
                    margin: 5px;
                }
                button:hover {
                    background-color: #45a049;
                }
                .btn-back {
                    background-color: #f44336;
                }
                .btn-back:hover {
                    background-color: #da190b;
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-icon">⚠️</div>
                <div class="error-title">Pembayaran Gagal!</div>
                <p>Uang bayar Anda kurang dari total yang harus dibayar.</p>
                
                <div class="detail">
                    <div class="detail-item">
                        <strong>Total Harga:</strong> Rp <?php echo number_format($total, 0, ',', '.'); ?>
                    </div>
                    <div class="detail-item">
                        <strong>Diskon:</strong> <?php echo $diskon; ?>%
                    </div>
                    <div class="detail-item">
                        <strong>Uang Bayar:</strong> Rp <?php echo number_format($bayar, 0, ',', '.'); ?>
                    </div>
                    <div class="kekurangan">
                        💰 Kekurangan: Rp <?php echo number_format($kekurangan, 0, ',', '.'); ?>
                    </div>
                </div>
                
                <button class="btn-back" onclick="history.back()">Kembali ke Form</button>
                <button onclick="window.location.href='form_transaksi.php'">Form Baru</button>
            </div>
        </body>
        </html>
        <?php
        exit(); // Hentikan proses agar tidak lanjut ke insert
    }
    
    // Jika uang bayar cukup, hitung kembalian
    $kembalian = hitungKembalian($bayar, $total);
    
    // Simpan ke database
    $query = "INSERT INTO transaksi VALUES ('', NOW(), '$total', '$bayar', '$kembalian', '1')";
    
    if (mysqli_query($conn, $query)) {
        // Tampilkan struk pembayaran
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Struk Pembayaran</title>
            <style>
                body {
                    font-family: 'Courier New', monospace;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                    background-color: #f5f5f5;
                }
                .struk {
                    background-color: white;
                    width: 350px;
                    padding: 20px;
                    border-radius: 5px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .header {
                    text-align: center;
                    border-bottom: 1px dashed #000;
                    padding-bottom: 10px;
                    margin-bottom: 10px;
                }
                .header h2 {
                    margin: 0;
                }
                .header p {
                    margin: 5px 0;
                    font-size: 12px;
                }
                .divider {
                    border-top: 1px dashed #000;
                    margin: 10px 0;
                }
                .row {
                    display: flex;
                    justify-content: space-between;
                    margin: 8px 0;
                }
                .total-row {
                    border-top: 1px dashed #000;
                    border-bottom: 1px dashed #000;
                    padding: 10px 0;
                    margin: 10px 0;
                    font-weight: bold;
                }
                .kembalian-row {
                    color: green;
                    font-weight: bold;
                    font-size: 16px;
                }
                .footer {
                    text-align: center;
                    margin-top: 15px;
                    font-size: 11px;
                }
                button {
                    display: block;
                    width: 100%;
                    margin-top: 20px;
                    padding: 10px;
                    background-color: #4CAF50;
                    color: white;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 14px;
                }
                button:hover {
                    background-color: #45a049;
                }
                @media print {
                    button {
                        display: none;
                    }
                    body {
                        background-color: white;
                    }
                }
            </style>
        </head>
        <body>
            <div class="struk">
                <div class="header">
                    <h2>KASIR MINI RIYA</h2>
                    <p>Jl. Raya No. 123</p>
                    <p>Telp: 085746863781</p>
                    <p><?php echo date('d/m/Y H:i:s'); ?></p>
                </div>
                
                <div class="divider"></div>
                
                <div class="row">
                    <span>Total Belanja:</span>
                    <span>Rp <?php echo number_format($harga, 0, ',', '.'); ?></span>
                </div>
                
                <?php if ($diskon > 0) { ?>
                <div class="row">
                    <span>Diskon (<?php echo $diskon; ?>%):</span>
                    <span>- Rp <?php echo number_format($harga * $diskon / 100, 0, ',', '.'); ?></span>
                </div>
                <?php } ?>
                
                <div class="row total-row">
                    <span><strong>Total Bayar:</strong></span>
                    <span><strong>Rp <?php echo number_format($total, 0, ',', '.'); ?></strong></span>
                </div>
                
                <div class="row">
                    <span>Uang Tunai:</span>
                    <span>Rp <?php echo number_format($bayar, 0, ',', '.'); ?></span>
                </div>
                
                <div class="row kembalian-row">
                    <span>Kembalian:</span>
                    <span>Rp <?php echo number_format($kembalian, 0, ',', '.'); ?></span>
                </div>
                
                <div class="divider"></div>
                
                <div class="footer">
                    <p>Terima Kasih</p>
                    <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
                </div>
                
                <button onclick="window.print()">🖨️ Cetak Struk</button>
                <button onclick="window.location.href='form_transaksi.php'" style="margin-top: 10px; background-color: #2196F3;">➕ Transaksi Baru</button>
            </div>
            
            <script>
                // Otomatis cetak struk (opsional)
                // setTimeout(function() { window.print(); }, 500);
            </script>
        </body>
        </html>
        <?php
    } else {
        // Jika gagal menyimpan ke database
        echo "<script>
                alert('Gagal menyimpan transaksi: " . mysqli_error($conn) . "');
                window.location.href = 'form_transaksi.php';
              </script>";
    }
    
} else {
    // Jika tidak ada data POST
    header("Location: form_transaksi.php");
    exit();
}
?>