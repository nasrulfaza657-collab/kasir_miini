<?php
session_start();
include "koneksi.php";

if (isset($_POST['username'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $data = mysqli_query($conn, "SELECT * FROM users WHERE username='$user' AND password='$pass'");
    $cek = mysqli_num_rows($data);

    if ($cek > 0) {
        $d = mysqli_fetch_assoc($data);
        $_SESSION['role'] = $d['role'];

        if ($d['role'] == 'admin') {
            header("location:admin.php");
        } else {
            header("location:kasir.php");
        }
    } else {
        echo "Login berhasil";
    }
}
?>
