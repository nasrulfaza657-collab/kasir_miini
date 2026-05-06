<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: #e8f0fe;
            margin: 0;
            padding: 30px 20px;
        }

        /* efek garis-garis halus di background */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(#c5d5e8 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.4;
            pointer-events: none;
            z-index: 0;
        }

        .box {
            max-width: 550px;
            margin: 0 auto;
            background: #fffef7;
            border-radius: 24px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05), 0 2px 4px rgba(0,0,0,0.02);
            padding: 30px 28px;
            position: relative;
            z-index: 1;
            border: 1px solid #ffe6c7;
        }

        /* sudut dekorasi kecil */
        .box::after {
            content: "🛒";
            position: absolute;
            bottom: 15px;
            right: 20px;
            font-size: 40px;
            opacity: 0.1;
            pointer-events: none;
        }

        h1 {
            font-size: 28px;
            font-weight: 600;
            margin: 0 0 8px 0;
            color: #2c3e4e;
            letter-spacing: -0.3px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h1:before {
            content: "🏪";
            font-size: 32px;
        }

        .sub {
            color: #6c7a89;
            font-size: 14px;
            border-left: 3px solid #f5b042;
            padding-left: 12px;
            margin-bottom: 28px;
            margin-top: 5px;
        }

        form label {
            display: block;
            font-weight: 500;
            color: #2c3e4e;
            margin-bottom: 6px;
            font-size: 14px;
        }

        form input {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            font-size: 15px;
            background: #ffffff;
            transition: all 0.2s;
            box-sizing: border-box;
            font-family: inherit;
        }

        form input:focus {
            border-color: #f5b042;
            outline: none;
            background: #fffaf2;
            box-shadow: 0 0 0 3px rgba(245,176,66,0.1);
        }

        button {
            background: #2c3e4e;
            color: white;
            border: none;
            padding: 12px 18px;
            font-size: 16px;
            font-weight: 500;
            border-radius: 40px;
            cursor: pointer;
            width: 100%;
            transition: 0.2s;
            margin-top: 8px;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        button:hover {
            background: #1f2f3c;
            transform: scale(0.98);
        }

        .link-wrap {
            text-align: center;
            margin-top: 25px;
        }

        a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff0e0;
            color: #c17b2e;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 40px;
            font-size: 14px;
            font-weight: 500;
            transition: 0.2s;
            border: 1px solid #ffe0bf;
        }

        a:hover {
            background: #ffe6d0;
            border-color: #f5b042;
            color: #9b5e1a;
        }

        .alert-success {
            background: #e0f2e9;
            border-left: 4px solid #2e7d64;
            padding: 12px 16px;
            border-radius: 20px;
            margin-bottom: 25px;
            color: #1e5a48;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        hr {
            margin: 20px 0 10px;
            border: none;
            height: 1px;
            background: #f0e2d0;
        }

        /* biar di hp enak */
        @media (max-width: 550px) {
            body { padding: 16px; }
            .box { padding: 22px; }
            h1 { font-size: 24px; }
        }
    </style>
</head>
<body>

<div class="box">
    <h1>Tambah Barang</h1>
    <div class="sub">isi data dengan benar ya</div>

    <?php
    include "koneksi.php";
    if (isset($_POST['nama'])) {
        $nama = $_POST['nama'];
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];
        mysqli_query($conn, "INSERT INTO barang VALUES ('', '$nama', '$harga', '$stok')");
        echo '<div class="alert-success">✅  Data berhasil disimpan</div>';
    }
    ?>

    <form method="POST">
        <label> Nama barang</label>
        <input type="text" name="nama" placeholder="contoh: Indomie Goreng" required>

        <label> Harga</label>
        <input type="number" name="harga" placeholder="Rp" required>

        <label>Stok</label>
        <input type="number" name="stok" placeholder="jumlah" required>

        <button type="submit"> Simpan barang</button>
    </form>

    <hr>
    <div class="link-wrap">
        <a href="tampil_barang.php">Lihat daftar barang</a>
    </div>
</div>

</body>
</html>