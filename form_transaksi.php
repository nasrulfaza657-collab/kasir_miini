<!DOCTYPE html>
<html>
<head>
    <title>Form Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 350px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .info {
            background-color: #e3f2fd;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Form Transaksi</h2>
        <form method="POST" action="proses_transaksi.php" id="formTransaksi">
            <div class="form-group">
                <label>Total Harga (Rp):</label>
                <input type="number" name="harga" id="harga" required min="0" step="1000">
            </div>
            
            <div class="form-group">
                <label>Diskon (%):</label>
                <input type="number" name="diskon" id="diskon" value="0" min="0" max="100">
            </div>
            
            <div class="info" id="infoTotal">
                Total setelah diskon: Rp 0
            </div>
            
            <div class="form-group">
                <label>Uang Bayar (Rp):</label>
                <input type="number" name="bayar" id="bayar" required min="0" step="1000">
            </div>
            
            <div id="pesanValidasi" style="margin-bottom: 15px;"></div>
            
            <button type="submit" id="btnSubmit">Proses Pembayaran</button>
        </form>
    </div>
    
    <script>
        const hargaInput = document.getElementById('harga');
        const diskonInput = document.getElementById('diskon');
        const bayarInput = document.getElementById('bayar');
        const infoTotal = document.getElementById('infoTotal');
        const pesanValidasi = document.getElementById('pesanValidasi');
        const btnSubmit = document.getElementById('btnSubmit');
        
        function hitungTotal() {
            let harga = parseInt(hargaInput.value) || 0;
            let diskon = parseInt(diskonInput.value) || 0;
            
            let total = harga - (harga * diskon / 100);
            
            infoTotal.innerHTML = `Total setelah diskon: Rp ${total.toLocaleString('id-ID')}`;
            
            return total;
        }
        
        function validasiBayar() {
            let total = hitungTotal();
            let bayar = parseInt(bayarInput.value) || 0;
            
            if (bayar > 0 && bayar < total) {
                let kurang = total - bayar;
                pesanValidasi.innerHTML = `
                    <div style="background-color: #ffebee; color: #c62828; padding: 10px; border-radius: 4px; border-left: 3px solid #c62828;">
                        ⚠️ Uang bayar kurang! Kekurangan: Rp ${kurang.toLocaleString('id-ID')}
                    </div>
                `;
                btnSubmit.disabled = true;
                btnSubmit.style.opacity = '0.5';
                btnSubmit.style.cursor = 'not-allowed';
            } else if (bayar >= total && total > 0) {
                let kembali = bayar - total;
                pesanValidasi.innerHTML = `
                    <div style="background-color: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 4px; border-left: 3px solid #2e7d32;">
                        ✓ Uang cukup. Kembalian: Rp ${kembali.toLocaleString('id-ID')}
                    </div>
                `;
                btnSubmit.disabled = false;
                btnSubmit.style.opacity = '1';
                btnSubmit.style.cursor = 'pointer';
            } else if (total > 0 && bayar == 0) {
                pesanValidasi.innerHTML = `
                    <div style="background-color: #fff3e0; color: #e65100; padding: 10px; border-radius: 4px; border-left: 3px solid #e65100;">
                        ⚠️ Masukkan jumlah uang bayar
                    </div>
                `;
                btnSubmit.disabled = true;
                btnSubmit.style.opacity = '0.5';
            } else {
                pesanValidasi.innerHTML = '';
                btnSubmit.disabled = false;
                btnSubmit.style.opacity = '1';
            }
        }
        
        hargaInput.addEventListener('input', validasiBayar);
        diskonInput.addEventListener('input', validasiBayar);
        bayarInput.addEventListener('input', validasiBayar);
    </script>
</body>
</html>