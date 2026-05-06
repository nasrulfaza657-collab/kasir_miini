<?php include "koneksi.php"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 30px 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: #2c3e50;
            color: white;
            padding: 15px 20px;
        }

        .header h3 {
            font-size: 20px;
            font-weight: 500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #34495e;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 500;
            font-size: 14px;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 14px;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .btn-hapus {
            background: #e74c3c;
            color: white;
            padding: 5px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            transition: background 0.2s;
        }

        .btn-hapus:hover {
            background: #c0392b;
        }

        .btn-tambah {
            display: inline-block;
            background: #27ae60;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            margin: 20px;
            transition: background 0.2s;
        }

        .btn-tambah:hover {
            background: #219a52;
        }

        .kosong {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }

        .footer {
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #ecf0f1;
            font-size: 12px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h3>📦 Data Barang</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $data = mysqli_query($conn, "SELECT * FROM barang");
            $no = 1;
            if (mysqli_num_rows($data) > 0) {
                while ($d = mysqli_fetch_array($data)) {
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $d['nama_barang']; ?></td>
                        <td>Rp <?php echo number_format($d['harga'], 0, ',', '.'); ?></td>
                        <td>
                            <a href="hapus_barang.php?id=<?php echo $d['id']; ?>" class="btn-hapus" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="4" class="kosong">Belum ada data barang</td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

    <div style="padding: 0 20px 20px 20px;">
        <a href="barang.php" class="btn-tambah">+ Tambah Barang</a>
    </div>

    <div class="footer">
        Total: <?php echo mysqli_num_rows($data); ?> barang
    </div>
</div>

</body>
</html>