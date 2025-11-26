<?php 
session_start();
include '../config/koneksi.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_pengadu = mysqli_real_escape_string($koneksi, $_POST['nama_pengadu']);
    $kecamatan = mysqli_real_escape_string($koneksi, $_POST['kecamatan']);
    $komoditas = mysqli_real_escape_string($koneksi, $_POST['komoditas']);
    $isi_laporan = mysqli_real_escape_string($koneksi, $_POST['isi_laporan']);
    
    $query = "INSERT INTO pengaduan (nama_pengadu, kecamatan, komoditas, isi_laporan, status, tanggal) 
              VALUES ('$nama_pengadu', '$kecamatan', '$komoditas', '$isi_laporan', 'pending', NOW())";
    
    if (mysqli_query($koneksi, $query)) {
        $success = "Pengaduan berhasil dikirim! Kami akan menindaklanjuti laporan Anda.";
    } else {
        $error = "Terjadi kesalahan saat mengirim pengaduan. Silakan coba lagi.";
    }
}

$komoditas_list = mysqli_query($koneksi, "SELECT * FROM komoditas ORDER BY nama_komoditas");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Masyarakat - SVASEMBADA</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
    body {
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #386641, #1a8a3a) !important;
        background-attachment: fixed;
        font-family: 'Poppins', sans-serif;
    }
    .section-title {
        text-align: center;
        background: linear-gradient(135deg, #ffa600ee, #ffea00ff);
        padding: 30px;
        border-radius: 25px;
    }

    .section-title h2 {
        color: white;
        font-family: 'Poppins', sans-serif;
        margin-bottom: 10px;
    }

    .section-title p {
        color: white;
        font-family: 'Poppins', sans-serif;
    }
    .form-container {
        background: linear-gradient(135deg, #ffa600ee, #ffea00ff);
    }
    .logo {
    display: flex;
    align-items: center;
    gap: 10px;        
    }

    .nav-logo {
    height: 50px;
    width: auto;
    object-fit: contain;
    }
    .card-header {
    background: linear-gradient(135deg, #ffa600ee, #ffea00ff);
    padding: 30px;
    border-radius: 25px;
    }
</style>

</head>
<body>
   <header>
    <div class="container header-container">
        <div class="logo">
            <h1 class="brand">
                <img src="../assets/img/logo_svasembada.png" alt="SVASEMBADA Logo" class="brand-logo">
                SVASEMBADA
            </h1>
        </div>
        <button class="mobile-menu-btn">☰</button>
        <ul class="nav-menu">
            <li><a href="../index.php">Beranda</a></li>
            <li><a href="monitoring.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'monitoring.php') ? 'active' : '' ?>">Monitoring</a></li>
            <li><a href="informasi.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'informasi.php') ? 'active' : '' ?>">Informasi Pangan</a></li>
            <li><a href="pengaduan.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'pengaduan.php') ? 'active' : '' ?>">Pengaduan</a></li>
            <li><a href="../admin/login.php" class="btn btn-secondary btn-sm">LOGIN</a></li>
        </ul>
    </div>
</header>

    <main>
        <section class="section">
            <div class="container">
                <div class="section-title">
                    <h2>Form Pengaduan Masyarakat</h2> 
                    <p>Laporkan kendala distribusi, kelangkaan, atau lonjakan harga bahan pangan di daerah Anda</p>
                </div>

                <?php if($success): ?>
                    <div class="alert alert-success">
                        <?= $success ?>
                    </div>
                <?php endif; ?>

                <?php if($error): ?>
                    <div class="alert alert-danger">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <div class="form-container">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label class="form-label" for="nama_pengadu">Nama Lengkap *</label>
                            <input type="text" class="form-control" id="nama_pengadu" name="nama_pengadu" required 
                                   placeholder="Masukkan nama lengkap Anda">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="kecamatan">Kecamatan *</label>
                            <input type="text" class="form-control" id="kecamatan" name="kecamatan" required 
                                   placeholder="Masukkan nama kecamatan">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="komoditas">Komoditas yang Dilaporkan</label>
                            <select class="form-select" id="komoditas" name="komoditas">
                                <option value="">Pilih Komoditas</option>
                                <?php while($komoditas = mysqli_fetch_assoc($komoditas_list)): ?>
                                    <option value="<?= $komoditas['nama_komoditas'] ?>">
                                        <?= $komoditas['nama_komoditas'] ?>
                                    </option>
                                <?php endwhile; ?>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="isi_laporan">Isi Laporan *</label>
                            <textarea class="form-control form-textarea" id="isi_laporan" name="isi_laporan" required 
                                      placeholder="Jelaskan secara detail kendala yang Anda alami: kelangkaan, lonjakan harga, distribusi, dll."></textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Kirim Pengaduan</button>
                        </div>

                        <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #0a0a0aff;">
                            <small style="color: #0c0c0cff;">
                                * Data yang Anda berikan akan dijaga kerahasiaannya dan digunakan hanya untuk keperluan penanganan pengaduan.
                            </small>
                        </div>
                    </form>
                </div>

                <div class="card" style="margin-top: 40px;">
                    <div class="card-header">
                        <h3>Jenis Pengaduan yang Dapat Dilaporkan</h3>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; padding: 20px; background: linear-gradient(135deg, #ffa600ee, #ffea00ff); border-radius: 25px; ">
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; color: var(--danger); margin-bottom: 10px;">📈</div>
                            <h4 style="color: var(--primary); margin-bottom: 10px;">Lonjakan Harga</h4>
                            <p style="color: #666; font-size: 0.9rem;">Laporkan jika terjadi kenaikan harga yang tidak wajar pada komoditas pangan</p>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; color: var(--warning); margin-bottom: 10px;">🚫</div>
                            <h4 style="color: var(--primary); margin-bottom: 10px;">Kelangkaan Stok</h4>
                            <p style="color: #666; font-size: 0.9rem;">Informasikan jika bahan pangan sulit ditemukan di pasaran</p>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; color: var(--info); margin-bottom: 10px;">🚚</div>
                            <h4 style="color: var(--primary); margin-bottom: 10px;">Kendala Distribusi</h4>
                            <p style="color: #666; font-size: 0.9rem;">Laporkan masalah dalam rantai pasok dan distribusi pangan</p>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; color: var(--success); margin-bottom: 10px;">🛒</div>
                            <h4 style="color: var(--primary); margin-bottom: 10px;">Kualitas Produk</h4>
                            <p style="color: #666; font-size: 0.9rem;">Sampaikan keluhan mengenai kualitas bahan pangan yang tidak memadai</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="copyright">
                <p>&copy; 2025 SVASEMBADA - Sistem Ketahanan Pangan Daerah. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../assets/script.js"></script>
</body>
</html>