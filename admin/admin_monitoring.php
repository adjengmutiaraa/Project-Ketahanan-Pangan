<?php 
session_start();
include '../config/koneksi.php';

// Cek auth
if (!isset($_SESSION['user_id'])) {
    header('Location: ../admin/login.php');
    exit();
}

$success = '';
$error = '';

// Tambah data monitoring
if (isset($_POST['tambah_monitoring'])) {
    $id_komoditas = mysqli_real_escape_string($koneksi, $_POST['id_komoditas']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    
    // Untuk admin lokal, gunakan kecamatan dari session
    $kecamatan = ($_SESSION['role'] == 'admin_lokal') ? $_SESSION['kecamatan'] : mysqli_real_escape_string($koneksi, $_POST['kecamatan']);
    
    $query = "INSERT INTO monitoring (id_user, id_komoditas, stok, harga, kecamatan, tanggal_update) 
              VALUES ('{$_SESSION['user_id']}', '$id_komoditas', '$stok', '$harga', '$kecamatan', NOW())";
    
    if (mysqli_query($koneksi, $query)) {
        $success = "Data monitoring berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan data monitoring: " . mysqli_error($koneksi);
    }
}

// Hapus data monitoring
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    
    // Cek ownership (admin lokal hanya bisa hapus data sendiri)
    $check_query = "SELECT * FROM monitoring WHERE id_monitoring = '$id'";
    $check_result = mysqli_query($koneksi, $check_query);
    $data = mysqli_fetch_assoc($check_result);
    
    if ($_SESSION['role'] == 'admin_pusat' || $data['id_user'] == $_SESSION['user_id']) {
        $query = "DELETE FROM monitoring WHERE id_monitoring = '$id'";
        if (mysqli_query($koneksi, $query)) {
            $success = "Data monitoring berhasil dihapus!";
        } else {
            $error = "Gagal menghapus data monitoring: " . mysqli_error($koneksi);
        }
    } else {
        $error = "Anda tidak memiliki akses untuk menghapus data ini!";
    }
}

// Ambil data monitoring
$where_condition = "";
if ($_SESSION['role'] == 'admin_lokal') {
    $where_condition = "WHERE m.kecamatan = '{$_SESSION['kecamatan']}'";
}

$query = "SELECT m.*, k.nama_komoditas, k.satuan, k.kategori, u.nama 
          FROM monitoring m 
          JOIN komoditas k ON m.id_komoditas = k.id_komoditas 
          JOIN users u ON m.id_user = u.id_user 
          $where_condition 
          ORDER BY m.tanggal_update DESC";
$monitoring = mysqli_query($koneksi, $query);

// Ambil daftar komoditas untuk form
$komoditas_list = mysqli_query($koneksi, "SELECT * FROM komoditas ORDER BY nama_komoditas");

// Ambil daftar kecamatan (hanya untuk admin pusat)
$kecamatan_list = mysqli_query($koneksi, "SELECT DISTINCT kecamatan FROM monitoring ORDER BY kecamatan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Monitoring - SVASEMBADA</title>
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .data-table tbody tr:hover {
            background-color: var(--light) !important;
        }
    </style>
</head>
<body>
   <header>
    <div class="container header-container">
        <div class="logo">
            <h1 class="brand">
                <img src="../assets/img/logo_svasembada.png" alt="SVASEMBADA" class="brand-logo">
                SVASEMBADA
            </h1>
        </div>
        <button class="mobile-menu-btn">☰</button>
        <ul class="nav-menu">
            <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

            <li><a href="admin_monitoring.php" class="<?= ($currentPage == 'admin_monitoring.php') ? 'active' : '' ?>">Monitoring</a></li>
            <li><a href="admin_pengaduan.php" class="<?= ($currentPage == 'admin_pengaduan.php') ? 'active' : '' ?>">Pengaduan</a></li>
            
            <?php if($_SESSION['role'] == 'admin_pusat'): ?>
                <li><a href="admin_komoditas.php" class="<?= ($currentPage == 'admin_komoditas.php') ? 'active' : '' ?>">Master Komoditas</a></li>
                <li><a href="admin_user.php" class="<?= ($currentPage == 'admin_user.php') ? 'active' : '' ?>">Manajemen User</a></li>
            <?php endif; ?>
            <li><a href="../pages/dashboard.php" class="<?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">Dashboard</a></li>
            <li><a href="logout.php" class="btn btn-secondary btn-sm">Logout</a></li>
        </ul>
    </div>
</header>

    <main>
        <section class="section">
            <div class="container">
                <div class="section-title">
                    <h2>Kelola Data Monitoring</h2>
                    <p>Input dan kelola data stok & harga bahan pangan</p>
                    <?php if($_SESSION['role'] == 'admin_lokal'): ?>
                        <p style="color: var(--secondary);"><strong>Wilayah:</strong> <?= $_SESSION['kecamatan'] ?></p>
                    <?php endif; ?>
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

                <!-- Form Input Data -->
                <div class="card">
                    <div class="card-header">
                        <h3>Input Data Monitoring Baru</h3>
                    </div>
                    <div style="padding: 20px;">
                        <form method="POST" action="">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                <div class="form-group">
                                    <label class="form-label">Komoditas</label>
                                    <select class="form-select" name="id_komoditas" required>
                                        <option value="">Pilih Komoditas</option>
                                        <?php while($komoditas = mysqli_fetch_assoc($komoditas_list)): ?>
                                            <option value="<?= $komoditas['id_komoditas'] ?>">
                                                <?= $komoditas['nama_komoditas'] ?> (<?= $komoditas['satuan'] ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Stok</label>
                                    <input type="number" class="form-control" name="stok" required 
                                           placeholder="Jumlah stok" min="0">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Harga (Rp)</label>
                                    <input type="number" class="form-control" name="harga" required 
                                           placeholder="Harga per satuan" min="0">
                                </div>
                                
                                <?php if($_SESSION['role'] == 'admin_pusat'): ?>
                                    <div class="form-group">
                                        <label class="form-label">Kecamatan</label>
                                        <select class="form-select" name="kecamatan" required>
                                            <option value="">Pilih Kecamatan</option>
                                            <?php while($kecamatan = mysqli_fetch_assoc($kecamatan_list)): ?>
                                                <option value="<?= $kecamatan['kecamatan'] ?>">
                                                    <?= $kecamatan['kecamatan'] ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group" style="margin-top: 20px;">
                                <button type="submit" name="tambah_monitoring" class="btn btn-primary">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Daftar Data Monitoring -->
                <div class="card">
                    <div class="card-header">
                        <h3>Data Monitoring Terkini</h3>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Kecamatan</th>
                                    <th>Komoditas</th>
                                    <th>Stok</th>
                                    <th>Harga</th>
                                    <th>Input Oleh</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($monitoring) > 0): ?>
                                    <?php $no = 1; ?>
                                    <?php while($row = mysqli_fetch_assoc($monitoring)): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_update'])) ?></td>
                                            <td><?= $row['kecamatan'] ?></td>
                                            <td><?= $row['nama_komoditas'] ?></td>
                                            <td><?= number_format($row['stok']) ?> <?= $row['satuan'] ?></td>
                                            <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                            <td><?= $row['nama'] ?></td>
                                            <td>
                                                <a href="?hapus=<?= $row['id_monitoring'] ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center;">Belum ada data monitoring</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="../pages/dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="copyright">
                <p>&copy; 2023 SVASEMBADA - Sistem Ketahanan Pangan Daerah. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../assets/script.js"></script>
</body>
</html>