<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Set zona waktu ke WIB (Asia/Jakarta)
date_default_timezone_set('Asia/Jakarta');

// Ambil filter (bulan dan tahun)
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Buat tanggal awal dan akhir
$tanggal_awal = "$tahun-$bulan-01";
$tanggal_akhir = date("Y-m-t", strtotime($tanggal_awal));

// Query ambil data transaksi
$query = "SELECT * FROM transaksi 
          WHERE DATE(tanggal) BETWEEN '$tanggal_awal' AND '$tanggal_akhir' 
          ORDER BY tanggal DESC";

$result = mysqli_query($conn, $query);

// Fungsi format tanggal INDONESIA (LANGSUNG, tanpa konversi UTC)
function tgl_indonesia($tanggal) {
    $nama_hari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    
    $nama_bulan = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];
    
    // LANGSUNG pakai waktu dari database (sudah WIB)
    $hari = date('l', strtotime($tanggal));
    $tgl = date('d', strtotime($tanggal));
    $bln = date('m', strtotime($tanggal));
    $thn = date('Y', strtotime($tanggal));
    $jam = date('H:i:s', strtotime($tanggal));
    
    return $nama_hari[$hari] . ', ' . $tgl . ' ' . $nama_bulan[$bln] . ' ' . $thn . ' - ' . $jam . ' WIB';
}

// Hitung total
$total_penjualan = 0;
$total_transaksi = 0;
$data_transaksi = [];
while ($row = mysqli_fetch_array($result)) {
    $total_penjualan += $row['total'];
    $total_transaksi++;
    $data_transaksi[] = $row;
}

// Nama bulan
$nama_bulan = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - KasirMini</title>
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
            max-width: 1200px;
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
            flex-wrap: wrap;
            gap: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .header h2 {
            color: #333;
        }

        .back-btn {
            background: #6c757d;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
        }

        .filter-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .filter-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-size: 12px;
            margin-bottom: 5px;
            color: #666;
            font-weight: 500;
        }

        .filter-group select, 
        .filter-group input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn-filter {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-filter:hover {
            background: #0069d9;
        }

        .stats {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            flex: 1;
            min-width: 150px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            font-size: 28px;
            color: #28a745;
            margin-bottom: 5px;
        }

        .stat-card p {
            color: #666;
            font-size: 14px;
        }

        .period-info {
            background: #e3f2fd;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
            color: #1565c0;
        }

        .table-wrapper {
            background: white;
            border-radius: 8px;
            overflow-x: auto;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th {
            background: #343a40;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
        }

        td {
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
            font-size: 14px;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .total-footer {
            background: white;
            padding: 15px 20px;
            margin-top: 20px;
            border-radius: 8px;
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        @media print {
            .header, .filter-card, .stats, .back-btn, .btn-filter {
                display: none;
            }
            .table-wrapper {
                box-shadow: none;
            }
            body {
                background: white;
                padding: 0;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>📊 LAPORAN TRANSAKSI</h2>
        <a href="admin.php" class="back-btn">← Kembali ke Dashboard</a>
    </div>

    <!-- Filter Bulan dan Tahun -->
    <div class="filter-card">
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <label>📅 Pilih Bulan</label>
                <select name="bulan">
                    <option value="01" <?php echo $bulan == '01' ? 'selected' : ''; ?>>Januari</option>
                    <option value="02" <?php echo $bulan == '02' ? 'selected' : ''; ?>>Februari</option>
                    <option value="03" <?php echo $bulan == '03' ? 'selected' : ''; ?>>Maret</option>
                    <option value="04" <?php echo $bulan == '04' ? 'selected' : ''; ?>>April</option>
                    <option value="05" <?php echo $bulan == '05' ? 'selected' : ''; ?>>Mei</option>
                    <option value="06" <?php echo $bulan == '06' ? 'selected' : ''; ?>>Juni</option>
                    <option value="07" <?php echo $bulan == '07' ? 'selected' : ''; ?>>Juli</option>
                    <option value="08" <?php echo $bulan == '08' ? 'selected' : ''; ?>>Agustus</option>
                    <option value="09" <?php echo $bulan == '09' ? 'selected' : ''; ?>>September</option>
                    <option value="10" <?php echo $bulan == '10' ? 'selected' : ''; ?>>Oktober</option>
                    <option value="11" <?php echo $bulan == '11' ? 'selected' : ''; ?>>November</option>
                    <option value="12" <?php echo $bulan == '12' ? 'selected' : ''; ?>>Desember</option>
                </select>
            </div>
            <div class="filter-group">
                <label>📆 Pilih Tahun</label>
                <select name="tahun">
                    <?php for ($y = 2023; $y <= date('Y') + 1; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo $tahun == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="filter-group">
                <button type="submit" class="btn-filter">🔍 TAMPILKAN</button>
            </div>
            <div class="filter-group">
                <button type="button" onclick="window.print()" class="btn-filter" style="background:#17a2b8;">🖨️ PRINT</button>
            </div>
        </form>
    </div>

    <!-- Info Periode -->
    <div class="period-info">
        📅 LAPORAN BULAN <?php echo strtoupper($nama_bulan[$bulan]); ?> <?php echo $tahun; ?> 
        (<?php echo date('d/m/Y', strtotime($tanggal_awal)); ?> - <?php echo date('d/m/Y', strtotime($tanggal_akhir)); ?>)
    </div>

    <!-- Statistik -->
    <div class="stats">
        <div class="stat-card">
            <h3><?php echo $total_transaksi; ?></h3>
            <p>Total Transaksi</p>
        </div>
        <div class="stat-card">
            <h3>Rp <?php echo number_format($total_penjualan, 0, ',', '.'); ?></h3>
            <p>Total Pendapatan</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_transaksi > 0 ? round($total_penjualan / $total_transaksi) : 0; ?></h3>
            <p>Rata-rata per Transaksi</p>
        </div>
    </div>

    <!-- Tabel Laporan -->
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal & Waktu (WIB)</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Uang Bayar</th>
                    <th>Kembalian</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($data_transaksi) > 0): ?>
                    <?php $no = 1; foreach ($data_transaksi as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo tgl_indonesia($row['tanggal']); ?></td>
                            <td><strong><?php echo $row['nama_barang'] ?: '-'; ?></strong></td>
                            <td><?php echo $row['jumlah'] ?: '-'; ?> buah</strong></td>
                            <td>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($row['bayar'], 0, ',', '.'); ?></strong></td>
                            <td style="color: green; font-weight: bold;">Rp <?php echo number_format($row['kembalian'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="no-data">
                            📭 Belum ada transaksi pada bulan <?php echo $nama_bulan[$bulan]; ?> <?php echo $tahun; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (count($data_transaksi) > 0): ?>
    <div class="total-footer">
        <p>Total Keseluruhan: Rp <?php echo number_format($total_penjualan, 0, ',', '.'); ?></p>
        <p style="font-size: 11px; font-weight: normal; margin-top: 5px;">
            Dicetak: <?php echo date('d/m/Y H:i:s'); ?> WIB
        </p>
    </div>
    <?php endif; ?>
</div>

</body>
</html>