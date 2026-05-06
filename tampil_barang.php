<?php
date_default_timezone_set('Asia/Jakarta');

// Set zona waktu ke WIB
$tanggal = date('Y-m-d H:i:s'); 

session_start();
include "koneksi.php";

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Proses Pembayaran
$pesan = "";
$error = "";
$struk_data = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proses_bayar'])) {
    $id_barang = $_POST['id_barang'];
    $jumlah = $_POST['jumlah'];
    $uang_bayar = $_POST['uang_bayar'];
    $diskon = isset($_POST['diskon']) ? $_POST['diskon'] : 0;
    
    // Ambil data barang
    $query = mysqli_query($conn, "SELECT * FROM barang WHERE id = '$id_barang'");
    $barang = mysqli_fetch_array($query);
    
    if ($barang) {
        $harga = $barang['harga'];
        $nama_barang = $barang['nama_barang'];
        $stok_lama = $barang['stok'];
        
        // Hitung total dengan diskon
        $subtotal = $harga * $jumlah;
        $total_diskon = $subtotal * $diskon / 100;
        $total_harga = $subtotal - $total_diskon;
        
        // Validasi stok
        if ($jumlah > $stok_lama) {
            $error = "Stok tidak mencukupi! Stok tersedia: $stok_lama";
        }
        elseif ($uang_bayar < $total_harga) {
            $kurang = $total_harga - $uang_bayar;
            $error = "Uang bayar kurang! Kekurangan: Rp " . number_format($kurang, 0, ',', '.');
        } 
        else {
            $kembalian = $uang_bayar - $total_harga;
            $stok_baru = $stok_lama - $jumlah;
            
            // Update stok
            $update_stok = mysqli_query($conn, "UPDATE barang SET stok = '$stok_baru' WHERE id = '$id_barang'");
            
            if ($update_stok) {
                $tanggal = date('Y-m-d H:i:s');
                $user_id = $_SESSION['user_id'] ?? 1;
                
                // PERBAIKAN: INSERT dengan data lengkap
                $insert_transaksi = mysqli_query($conn, "INSERT INTO transaksi (tanggal, id_barang, nama_barang, jumlah, total, bayar, kembalian, user_id) 
                                     VALUES ('$tanggal', '$id_barang', '$nama_barang', '$jumlah', '$total_harga', '$uang_bayar', '$kembalian', '$user_id')");
                
                if ($insert_transaksi) {
                    $pesan = "Pembelian berhasil!";
                    $struk_data = [
                        'nama_barang' => $nama_barang,
                        'jumlah' => $jumlah,
                        'harga' => $harga,
                        'diskon' => $diskon,
                        'subtotal' => $subtotal,
                        'total_diskon' => $total_diskon,
                        'total' => $total_harga,
                        'bayar' => $uang_bayar,
                        'kembalian' => $kembalian,
                        'tanggal' => $tanggal
                    ];
                } else {
                    $error = "Gagal menyimpan transaksi: " . mysqli_error($conn);
                }
            } else {
                $error = "Gagal mengupdate stok!";
            }
        }
    } else {
        $error = "Barang tidak ditemukan!";
    }
}

// Proses Hapus Barang
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    $query_hapus = mysqli_query($conn, "DELETE FROM barang WHERE id = '$id_hapus'");
    if ($query_hapus) {
        $pesan_hapus = "Barang berhasil dihapus!";
        echo "<script>window.location.href='tampil_barang.php';</script>";
        exit();
    } else {
        $error_hapus = "Gagal menghapus barang!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang - KasirMini</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            flex-wrap: wrap;
            gap: 10px;
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .btn-tambah {
            background: #28a745;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }

        .back-btn {
            background: #6c757d;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }

        .struk {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            max-width: 350px;
            margin-left: auto;
            margin-right: auto;
        }

        .struk h3 {
            text-align: center;
            margin-bottom: 5px;
        }

        .struk p {
            text-align: center;
            font-size: 11px;
            margin-bottom: 10px;
        }

        .garis {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin: 6px 0;
            font-size: 13px;
        }

        .total {
            font-weight: bold;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 8px 0;
            margin: 8px 0;
        }

        .btn-print {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }

        .table-wrapper {
            background: white;
            border-radius: 10px;
            overflow-x: auto;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th {
            background: #343a40;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        td {
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
            font-size: 14px;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .stok-low {
            color: #dc3545;
            font-weight: bold;
        }

        .stok-medium {
            color: #fd7e14;
            font-weight: bold;
        }

        .stok-aman {
            color: #28a745;
            font-weight: bold;
        }

        .btn-beli {
            background: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 12px;
            margin-right: 5px;
        }

        .btn-beli:hover {
            background: #218838;
        }

        .btn-hapus {
            background: #dc3545;
            color: white;
            padding: 5px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            display: inline-block;
        }

        .btn-hapus:hover {
            background: #c82333;
        }

        .form-beli {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: none;
        }

        .form-beli h3 {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #28a745;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 13px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group input[readonly] {
            background: #e9ecef;
        }

        .btn-bayar {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 15px;
            font-weight: bold;
        }

        .btn-bayar:hover {
            background: #0069d9;
        }

        .btn-batal {
            background: #6c757d;
            margin-top: 10px;
        }

        .btn-batal:hover {
            background: #5a6268;
        }

        .title-section {
            margin: 20px 0 15px 0;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="logo">🏪 KasirMini</div>
        <div>
            <a href="tambah_barang.php" class="btn-tambah">+ Tambah Barang</a>
            <a href="admin.php" class="back-btn">← Kembali</a>
        </div>
    </div>

    <?php if (isset($pesan_hapus)): ?>
        <div class="alert-success">✅ <?php echo $pesan_hapus; ?></div>
    <?php endif; ?>

    <?php if (isset($error_hapus)): ?>
        <div class="alert-danger">⚠️ <?php echo $error_hapus; ?></div>
    <?php endif; ?>

    <?php if ($pesan): ?>
        <div class="alert-success">✅ <?php echo $pesan; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert-danger">⚠️ <?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Tampilkan Struk -->
    <?php if ($struk_data): ?>
    <div class="struk" id="struk">
        <h3>TOKO MINIMARKET</h3>
        <p>Jl. Raya No. 123<br>Telp: 08123456789</p>
        <div class="garis"></div>
        <div class="row">
            <span><?php echo $struk_data['nama_barang']; ?></span>
            <span><?php echo $struk_data['jumlah']; ?> x Rp <?php echo number_format($struk_data['harga'], 0, ',', '.'); ?></span>
        </div>
        <?php if ($struk_data['diskon'] > 0): ?>
        <div class="row">
            <span>Diskon (<?php echo $struk_data['diskon']; ?>%)</span>
            <span>- Rp <?php echo number_format($struk_data['total_diskon'], 0, ',', '.'); ?></span>
        </div>
        <?php endif; ?>
        <div class="row total">
            <span>Total</span>
            <span>Rp <?php echo number_format($struk_data['total'], 0, ',', '.'); ?></span>
        </div>
        <div class="row">
            <span>Tunai</span>
            <span>Rp <?php echo number_format($struk_data['bayar'], 0, ',', '.'); ?></span>
        </div>
        <div class="row" style="color: green; font-weight: bold;">
            <span>Kembalian</span>
            <span>Rp <?php echo number_format($struk_data['kembalian'], 0, ',', '.'); ?></span>
        </div>
        <div class="garis"></div>
        <div class="row" style="font-size: 10px; text-align: center; justify-content: center;">
            Terima Kasih<br><?php echo date('d/m/Y H:i:s', strtotime($struk_data['tanggal'])); ?>
        </div>
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Struk</button>
    </div>
    <?php endif; ?>

    <!-- Tabel Barang -->
    <div class="title-section">📋 DAFTAR BARANG</div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Nama Barang</th>
                    <th width="120">Harga</th>
                    <th width="70">Stok</th>
                    <th width="100">Status</th>
                    <th width="130">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $query = mysqli_query($conn, "SELECT * FROM barang ORDER BY id DESC");
                
                if (mysqli_num_rows($query) > 0) {
                    while ($row = mysqli_fetch_array($query)) {
                        if ($row['stok'] <= 0) {
                            $status = "<span class='stok-low'>✗ Habis</span>";
                        } elseif ($row['stok'] <= 5) {
                            $status = "<span class='stok-low'>⚠️ Stok Menipis</span>";
                        } elseif ($row['stok'] <= 10) {
                            $status = "<span class='stok-medium'>✓ Tersedia</span>";
                        } else {
                            $status = "<span class='stok-aman'>✓ Stok Aman</span>";
                        }
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo $row['nama_barang']; ?></strong></td>
                            <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                            <td><?php echo $row['stok']; ?> buah</td>
                            <td><?php echo $status; ?></td>
                            <td>
                                <button class="btn-beli" onclick="showFormBeli(<?php echo $row['id']; ?>, '<?php echo $row['nama_barang']; ?>', <?php echo $row['harga']; ?>, <?php echo $row['stok']; ?>)">🛒 Beli</button>
                                <a href="?hapus=<?php echo $row['id']; ?>" class="btn-hapus" onclick="return confirm('Hapus <?php echo $row['nama_barang']; ?>?')">🗑️ Hapus</a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center; padding:40px;'>📭 Belum ada data barang</td><tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Form Pembelian -->
    <div id="formBeli" class="form-beli">
        <h3>🛒 FORM PEMBELIAN</h3>
        <form method="POST" action="">
            <input type="hidden" name="id_barang" id="form_id_barang">
            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" id="form_nama_barang" readonly>
            </div>
            <div class="form-group">
                <label>Harga Satuan</label>
                <input type="text" id="form_harga" readonly>
            </div>
            <div class="form-group">
                <label>Stok Tersedia</label>
                <input type="text" id="form_stok" readonly>
            </div>
            <div class="form-group">
                <label>Jumlah Beli</label>
                <input type="number" name="jumlah" id="form_jumlah" min="1" value="1" required oninput="hitungTotalForm()">
            </div>
            <div class="form-group">
                <label>Diskon (%)</label>
                <select name="diskon" id="form_diskon" onchange="hitungTotalForm()">
                    <option value="0">0% - Tidak Ada Diskon</option>
                    <option value="5">5%</option>
                    <option value="10">10%</option>
                    <option value="15">15%</option>
                    <option value="20">20%</option>
                    <option value="25">25%</option>
                    <option value="30">30%</option>
                    <option value="50">50%</option>
                </select>
            </div>
            <div class="form-group">
                <label>Subtotal</label>
                <input type="text" id="form_subtotal" readonly>
            </div>
            <div class="form-group">
                <label>Potongan Diskon</label>
                <input type="text" id="form_potongan" readonly>
            </div>
            <div class="form-group">
                <label>Total Harga</label>
                <input type="text" id="form_total" readonly style="font-weight:bold; color:#28a745;">
            </div>
            <div class="form-group">
                <label>Uang Bayar</label>
                <input type="number" name="uang_bayar" id="form_uang_bayar" required oninput="hitungKembalianForm()">
            </div>
            <div class="form-group">
                <label>Kembalian</label>
                <input type="text" id="form_kembalian" readonly>
            </div>
            <button type="submit" name="proses_bayar" class="btn-bayar" id="btnBayar" disabled>💸 PROSES PEMBAYARAN</button>
            <button type="button" class="btn-bayar btn-batal" onclick="hideFormBeli()">✖ BATAL</button>
        </form>
    </div>
</div>

<script>
    let currentId = 0;
    let currentHarga = 0;
    let currentStok = 0;

    function showFormBeli(id, nama, harga, stok) {
        currentId = id;
        currentHarga = harga;
        currentStok = stok;
        
        document.getElementById('form_id_barang').value = id;
        document.getElementById('form_nama_barang').value = nama;
        document.getElementById('form_harga').value = 'Rp ' + harga.toLocaleString('id-ID');
        document.getElementById('form_stok').value = stok + ' buah';
        document.getElementById('form_jumlah').value = 1;
        document.getElementById('form_diskon').value = 0;
        document.getElementById('form_uang_bayar').value = '';
        document.getElementById('form_kembalian').value = '';
        
        hitungTotalForm();
        document.getElementById('formBeli').style.display = 'block';
        document.getElementById('formBeli').scrollIntoView({ behavior: 'smooth' });
    }

    function hideFormBeli() {
        document.getElementById('formBeli').style.display = 'none';
        currentId = 0;
    }

    function hitungTotalForm() {
        let jumlah = parseInt(document.getElementById('form_jumlah').value) || 0;
        let diskon = parseInt(document.getElementById('form_diskon').value) || 0;
        
        if (jumlah > currentStok) {
            alert('Jumlah melebihi stok! Stok tersedia: ' + currentStok);
            document.getElementById('form_jumlah').value = currentStok;
            jumlah = currentStok;
        }
        
        let subtotal = currentHarga * jumlah;
        let potongan = subtotal * diskon / 100;
        let total = subtotal - potongan;
        
        document.getElementById('form_subtotal').value = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById('form_potongan').value = 'Rp ' + potongan.toLocaleString('id-ID');
        document.getElementById('form_total').value = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('form_total').setAttribute('data-total', total);
        
        hitungKembalianForm();
    }

    function hitungKembalianForm() {
        let total = parseInt(document.getElementById('form_total').getAttribute('data-total')) || 0;
        let uangBayar = parseInt(document.getElementById('form_uang_bayar').value) || 0;
        let kembalian = uangBayar - total;
        let btnBayar = document.getElementById('btnBayar');
        
        if (uangBayar > 0 && uangBayar < total) {
            let kurang = total - uangBayar;
            document.getElementById('form_kembalian').value = 'Kurang Rp ' + kurang.toLocaleString('id-ID');
            document.getElementById('form_kembalian').style.color = 'red';
            btnBayar.disabled = true;
            btnBayar.style.opacity = '0.5';
        } else if (uangBayar >= total && total > 0) {
            document.getElementById('form_kembalian').value = 'Rp ' + kembalian.toLocaleString('id-ID');
            document.getElementById('form_kembalian').style.color = 'green';
            btnBayar.disabled = false;
            btnBayar.style.opacity = '1';
        } else {
            document.getElementById('form_kembalian').value = '';
            btnBayar.disabled = true;
            btnBayar.style.opacity = '0.5';
        }
    }

    document.getElementById('form_jumlah').addEventListener('input', hitungTotalForm);
    document.getElementById('form_diskon').addEventListener('change', hitungTotalForm);
    document.getElementById('form_uang_bayar').addEventListener('input', hitungKembalianForm);
</script>

</body>
</html>