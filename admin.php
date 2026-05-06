<?php
// Hapus semua spasi atau output sebelum session_start()
// Pastikan tidak ada karakter apapun sebelum <?php
session_start();
include "koneksi.php";

$nama_admin = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';

// Cek stok menipis
$query_stok = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang WHERE stok < 5");
$stok_menipis = mysqli_fetch_assoc($query_stok)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - KasirMini</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f0f2f5;
        }

        /* Header sederhana */
        .header {
            background: white;
            padding: 15px 20px;
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
            color: #4CAF50;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 6px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }

        .logout-btn:hover {
            background: #c82333;
        }

        /* Container */
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 0 20px;
        }

        /* Welcome box */
        .welcome {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .welcome h2 {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .welcome p {
            color: #666;
            font-size: 14px;
        }

        /* Alert stok */
        .alert {
            background: #fff3cd;
            border-left: 3px solid #ffc107;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
        }

        /* Stats sederhana */
        .stats {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .stat-box {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            flex: 1;
            min-width: 120px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stat-box .number {
            font-size: 28px;
            font-weight: bold;
            color: #4CAF50;
        }

        .stat-box .label {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }

        /* Menu grid */
        .menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .menu-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .menu-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .menu-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .menu-item h4 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .menu-item p {
            font-size: 12px;
            color: #888;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            color: #888;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="logo">KasirMini</div>
    <div class="user-info">
        <span>👋 <?php echo htmlspecialchars($nama_admin); ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="container">
    <div class="welcome">
        <h2>Dashboard Admin</h2>
        <p>Kelola data barang dan transaksi</p>
    </div>

    <?php if($stok_menipis > 0): ?>
    <div class="alert">
        ⚠️ Ada <?php echo $stok_menipis; ?> barang dengan stok < 5
    </div>
    <?php endif; ?>

    <div class="stats">
        <div class="stat-box">
            <div class="number" id="totalBarang">-</div>
            <div class="label">Total Barang</div>
        </div>
        <div class="stat-box">
            <div class="number" id="totalTransaksi">-</div>
            <div class="label">Total Transaksi</div>
        </div>
        <div class="stat-box">
            <div class="number">1</div>
            <div class="label">Admin Aktif</div>
        </div>
    </div>

    <div class="menu">
        <a href="barang.php" class="menu-item">
            <div class="menu-icon">📦</div>
            <h4>Kelola Barang</h4>
            <p>Tambah, edit, hapus</p>
        </a>
        <a href="form_transaksi.php" class="menu-item">
            <div class="menu-icon">🛒</div>
            <h4>Transaksi</h4>
            <p>Input penjualan</p>
        </a>
        <a href="laporan_transaksi.php" class="menu-item">
            <div class="menu-icon">📊</div>
            <h4>Laporan</h4>
            <p>Lihat laporan</p>
        </a>
        <a href="profile.php" class="menu-item">
            <div class="menu-icon">⚙️</div>
            <h4>Pengaturan</h4>
            <p>Profile & akun</p>
        </a>
    </div>
</div>

<div class="footer">
    &copy; <?php echo date('Y'); ?> KasirMini
</div>

<script>

fetch('get_stats.php')
    .then(res => res.json())
    .then(data => {
        if(data.totalBarang) document.getElementById('totalBarang').innerText = data.totalBarang;
        if(data.totalTransaksi) document.getElementById('totalTransaksi').innerText = data.totalTransaksi;
    })
    .catch(() => {
        document.getElementById('totalBarang').innerText = '0';
        document.getElementById('totalTransaksi').innerText = '0';
    });
</script>

</body>
</html>