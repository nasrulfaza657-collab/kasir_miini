<?php
session_start();
include "koneksi.php";

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Ambil data user
$query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
$user = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - KasirMini</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            padding: 30px 20px;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
        }

        /* Header atas */
        .header {
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .back-btn {
            background: #6c757d;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }

        /* Card profil - SEMUA JADI SATU */
        .card-profil {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        /* Foto Profil - di atas */
        .foto-section {
            text-align: center;
            padding: 30px 25px 20px 25px;
            background: white;
            border-bottom: 1px solid #eee;
        }

        .avatar {
            width: 100px;
            height: 100px;
            margin: 0 auto;
            position: relative;
            cursor: pointer;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #333;
            background: #f0f2f5;
        }

        .avatar-default {
            width: 100px;
            height: 100px;
            background: #333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            font-weight: bold;
            color: white;
            margin: 0 auto;
            border: 3px solid #333;
        }

        .edit-foto-text {
            margin-top: 10px;
            font-size: 12px;
            color: #888;
        }

        /* Informasi Akun - di bawah langsung */
        .info-section {
            padding: 20px 25px 30px 25px;
        }

        .info-group {
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-group:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .label {
            font-size: 12px;
            color: #999;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .value {
            font-size: 16px;
            font-weight: 500;
            color: #333;
        }

        .status-active {
            display: inline-block;
            background: #d4edda;
            color: #155724;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }

        /* Modal Upload */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 25px;
            width: 90%;
            max-width: 350px;
            border-radius: 12px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 28px;
            cursor: pointer;
            color: #888;
        }

        .file-input {
            margin: 20px 0;
        }

        .file-input input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .preview {
            text-align: center;
            margin: 15px 0;
        }

        .preview img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #333;
        }

        .btn-simpan {
            background: #333;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 14px;
        }

        .btn-simpan:hover {
            background: #555;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="logo">👤 Profil Saya</div>
        <a href="admin.php" class="back-btn">← Kembali</a>
    </div>

    <!-- CARD PROFIL - NYATU SEMUA -->
    <div class="card-profil">
        <!-- Bagian Foto -->
        <div class="foto-section">
            <div class="avatar" onclick="openModal()">
                <img id="fotoPreview" src="" style="display: none;">
                <div id="avatarDefault" class="avatar-default">
                    <?php 
                    $inisial = isset($user['nama_lengkap']) && $user['nama_lengkap'] ? strtoupper(substr($user['nama_lengkap'], 0, 1)) : strtoupper(substr($user['username'], 0, 1));
                    echo $inisial;
                    ?>
                </div>
            </div>
            <div class="edit-foto-text">Klik foto untuk ganti</div>
        </div>

        <!-- Bagian Info (langsung, tanpa header terpisah) -->
        <div class="info-section">
            <div class="info-group">
                <div class="label">Nama Lengkap</div>
                <div class="value"><?php echo isset($user['nama_lengkap']) && $user['nama_lengkap'] ? $user['nama_lengkap'] : 'Belum diisi'; ?></div>
            </div>

            <div class="info-group">
                <div class="label">Username</div>
                <div class="value"><?php echo $user['username']; ?></div>
            </div>

            <div class="info-group">
                <div class="label">Email</div>
                <div class="value"><?php echo isset($user['email']) && $user['email'] ? $user['email'] : 'Belum diisi'; ?></div>
            </div>

            <div class="info-group">
                <div class="label">Role / Jabatan</div>
                <div class="value"><?php echo ucfirst($user['role']); ?></div>
            </div>

            <div class="info-group">
                <div class="label">Status</div>
                <div class="value">
                    <span class="status-active">● Aktif</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Foto -->
<div id="uploadModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3 style="margin-bottom: 20px;">Ganti Foto Profil</h3>
        <div class="preview">
            <img id="livePreview" src="#" alt="Preview" style="display: none;">
        </div>
        <div class="file-input">
            <input type="file" id="fotoInput" accept="image/jpeg,image/png,image/jpg,image/gif">
        </div>
        <small style="color: #888;">Format: JPG, PNG, GIF. Maksimal 2MB</small>
        <br><br>
        <button class="btn-simpan" onclick="simpanFoto()">Simpan Foto</button>
    </div>
</div>

<script>
    let fotoData = null;

    function openModal() {
        document.getElementById('uploadModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('uploadModal').style.display = 'none';
        document.getElementById('fotoInput').value = '';
        document.getElementById('livePreview').style.display = 'none';
    }

    // Preview gambar
    document.getElementById('fotoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.getElementById('livePreview');
                preview.src = event.target.result;
                preview.style.display = 'block';
                fotoData = event.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Simpan foto ke localStorage
    function simpanFoto() {
        if (fotoData) {
            localStorage.setItem('fotoProfil', fotoData);
            
            const img = document.getElementById('fotoPreview');
            const avatarDefault = document.getElementById('avatarDefault');
            
            img.src = fotoData;
            img.style.display = 'block';
            avatarDefault.style.display = 'none';
            
            closeModal();
            alert('Foto profil berhasil diganti!');
        } else {
            alert('Pilih foto dulu ya!');
        }
    }

    // Load foto dari localStorage
    window.onload = function() {
        const savedFoto = localStorage.getItem('fotoProfil');
        if (savedFoto) {
            const img = document.getElementById('fotoPreview');
            const avatarDefault = document.getElementById('avatarDefault');
            
            img.src = savedFoto;
            img.style.display = 'block';
            avatarDefault.style.display = 'none';
        }
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('uploadModal')) {
            closeModal();
        }
    }
</script>

</body>
</html>