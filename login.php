<?php 
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-4">
                
                <?php if(isset($_GET['pesan']) && $_GET['pesan'] == "gagal"): ?>
                    <div class="alert alert-danger text-center" role="alert">
                        Username atau Password salah!
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="card-title text-center mb-4">Login</h3>
                        
                        <form action="proses_login.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="********" required>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Ingat saya</label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="login" class="btn btn-primary">Masuk</button>
                            </div>
                            <div class="mb-3">
                             <div class="g-recaptcha" data-sitekey="6Ldt_sgsAAAAABCjooyfqObtzGFmVAgxNWb2aCX-"><

                                </div>
                        <div class="mt-3 text-center">
                            <small>Belum punya akun? <a href="#">Daftar sekarang</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</body>
</html>