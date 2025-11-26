<?php 
session_start();
include '../config/koneksi.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../admin/login.php');
    exit();
}

$success = '';
$error = '';

if (isset($_POST['update_status'])) {
    $id_pengaduan = mysqli_real_escape_string($koneksi, $_POST['id_pengaduan']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    
    $query = "UPDATE pengaduan SET status = '$status' WHERE id_pengaduan = '$id_pengaduan'";
    
    if (mysqli_query($koneksi, $query)) {
        $success = "Status pengaduan berhasil diupdate!";
    } else {
        $error = "Gagal mengupdate status: " . mysqli_error($koneksi);
    }
}

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $query = "DELETE FROM pengaduan WHERE id_pengaduan = '$id'";
    
    if (mysqli_query($koneksi, $query)) {
        $success = "Pengaduan berhasil dihapus!";
    } else {
        $error = "Gagal menghapus pengaduan: " . mysqli_error($koneksi);
    }
}

$pengaduan = mysqli_query($koneksi, "SELECT * FROM pengaduan ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengaduan - SVASEMBADA</title>
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700;800&display=swap" rel="stylesheet">
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
                    <h2>Kelola Pengaduan Masyarakat</h2>
                    <p>Kelola dan update status laporan dari masyarakat</p>
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

                <div class="dashboard-grid">
                    <?php
                    $total_pengaduan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan");
                    $total_pengaduan = mysqli_fetch_assoc($total_pengaduan)['total'];
                    
                    $pengaduan_pending = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan WHERE status = 'pending'");
                    $pengaduan_pending = mysqli_fetch_assoc($pengaduan_pending)['total'];
                    
                    $pengaduan_proses = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan WHERE status = 'proses'");
                    $pengaduan_proses = mysqli_fetch_assoc($pengaduan_proses)['total'];
                    
                    $pengaduan_selesai = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan WHERE status = 'selesai'");
                    $pengaduan_selesai = mysqli_fetch_assoc($pengaduan_selesai)['total'];
                    ?>
                    
                    <div class="dashboard-card">
                        <h3>Total Pengaduan</h3>
                        <span class="number"><?= $total_pengaduan ?></span>
                        <div class="description">Semua laporan</div>
                    </div>
                    <div class="dashboard-card">
                        <h3>Pending</h3>
                        <span class="number"><?= $pengaduan_pending ?></span>
                        <div class="description">Menunggu ditindak</div>
                    </div>
                    <div class="dashboard-card">
                        <h3>Proses</h3>
                        <span class="number"><?= $pengaduan_proses ?></span>
                        <div class="description">Sedang diproses</div>
                    </div>
                    <div class="dashboard-card">
                        <h3>Selesai</h3>
                        <span class="number"><?= $pengaduan_selesai ?></span>
                        <div class="description">Sudah ditangani</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Daftar Pengaduan</h3>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Pengadu</th>
                                    <th>Kecamatan</th>
                                    <th>Komoditas</th>
                                    <th>Isi Laporan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($pengaduan) > 0): ?>
                                    <?php $no = 1; ?>
                                    <?php while($row = mysqli_fetch_assoc($pengaduan)): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                            <td><?= $row['nama_pengadu'] ?></td>
                                            <td><?= $row['kecamatan'] ?></td>
                                            <td><?= $row['komoditas'] ?: '-' ?></td>
                                            <td>
                                                <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                                    <?= substr($row['isi_laporan'], 0, 50) ?>...
                                                </div>
                                            </td>
                                            <td>
                                                <form method="POST" action="" style="display: inline;">
                                                    <input type="hidden" name="id_pengaduan" value="<?= $row['id_pengaduan'] ?>">
                                                    <select name="status" onchange="this.form.submit()" 
                                                            style="padding: 4px 8px; border-radius: 4px; border: 1px solid #ddd;">
                                                        <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                        <option value="proses" <?= $row['status'] == 'proses' ? 'selected' : '' ?>>Proses</option>
                                                        <option value="selesai" <?= $row['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button type="button" class="btn btn-accent btn-sm" 
                                                            onclick="showDetail('<?= $row['nama_pengadu'] ?>', '<?= $row['kecamatan'] ?>', '<?= $row['komoditas'] ?>', '<?= addslashes($row['isi_laporan']) ?>')">
                                                        Detail
                                                    </button>
                                                    <a href="?hapus=<?= $row['id_pengaduan'] ?>" 
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm('Yakin ingin menghapus pengaduan ini?')">
                                                        Hapus
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center;">Belum ada pengaduan</td>
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

    <script>
    function showDetail(nama, kecamatan, komoditas, isiLaporan) {
        const detailHtml = `
            <div style="padding: 20px;">
                <h3 style="color: var(--primary); margin-bottom: 15px;">Detail Pengaduan</h3>
                <div style="margin-bottom: 10px;">
                    <strong>Nama Pengadu:</strong> ${nama}
                </div>
                <div style="margin-bottom: 10px;">
                    <strong>Kecamatan:</strong> ${kecamatan}
                </div>
                <div style="margin-bottom: 10px;">
                    <strong>Komoditas:</strong> ${komoditas || '-'}
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Isi Laporan:</strong>
                    <div style="margin-top: 5px; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                        ${isiLaporan}
                    </div>
                </div>
            </div>
        `;
        
        if (confirm(detailHtml.replace(/<[^>]*>/g, ''))) {
        }
    }
    </script>

    <script src="../assets/script.js"></script>
</body>
</html>