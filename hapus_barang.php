<?php
include "koneksi.php";

if (isset($_GET['id'])) {
    
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    
    $query = "DELETE FROM barang WHERE id = '$id'";  
    
    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Data berhasil dihapus!');
                window.location.href = 'tampil_barang.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: tampil_barang.php");
    exit();
}
?>