<?php 
session_start();
include '../config/koneksi.php';

// Cek auth dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin_pusat') {
    header('Location: ../admin/login.php');
    exit();
}

$success = '';
$error = '';

// Tambah komoditas
if (isset($_POST['tambah_komoditas'])) {
    $nama_komoditas = mysqli_real_escape_string($koneksi, $_POST['nama_komoditas']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $satuan = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    
    $query = "INSERT INTO komoditas (nama_komoditas, kategori, satuan) 
              VALUES ('$nama_komoditas', '$kategori', '$satuan')";
    
    if (mysqli_query($koneksi, $query)) {
        $success = "Komoditas berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan komoditas: " . mysqli_error($koneksi);
    }
}

// Hapus komoditas
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $query = "DELETE FROM komoditas WHERE id_komoditas = '$id'";
    
    if (mysqli_query($koneksi, $query)) {
        $success = "Komoditas berhasil dihapus!";
    } else {
        $error = "Gagal menghapus komoditas: " . mysqli_error($koneksi);
    }
}

// Ambil data komoditas
$komoditas = mysqli_query($koneksi, "SELECT * FROM komoditas ORDER BY kategori, nama_komoditas");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Komoditas - SVASEMBADA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/style.css">
    
    <style>
        /* CSS INTERNAL: Hanya mengubah konten utama, TIDAK mengubah Navbar */
        
        :root {
            --primary: #386641;   /* Hijau Tua */
            --secondary: #F87A01; /* Oranye */
            --accent: #FED16A;    /* Kuning Emas */
            --light: #FFF4A4;     /* Kuning Muda */
            --white: #ffffff;
            --text-dark: #333333;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        /* --- STYLING KONTEN UTAMA (Kecuali Navbar) --- */

        /* Judul Section & Card */
        .section-title h2, 
        .card-header h3 { 
            color: var(--primary); 
            font-weight: 700;
        }

        /* Tombol Primary (Hijau Tua) */
        .btn-primary {
            background-color: var(--primary) !important;
            color: var(--white) !important;
            border: 1px solid var(--primary) !important;
        }
        .btn-primary:hover { 
            background-color: #2b5033 !important; 
        }

        /* Tombol Secondary (Oranye) */
        .btn-secondary {
            background-color: var(--secondary) !important;
            color: var(--white) !important;
            border: 1px solid var(--secondary) !important;
        }
        .btn-secondary:hover { 
            background-color: #d66900 !important;
        }
        
        /* Tombol Danger (Hapus) */
        .btn-danger {
            border-radius: 6px;
        }

        /* Tabel Styling */
        .data-table thead {
            background-color: var(--primary) !important;
            color: var(--white) !important;
        }
        
        /* Efek Hover Baris Tabel (Kuning Muda) */
        .data-table tbody tr:hover {
            background-color: var(--light) !important;
        }

        /* Badge Info (Kuning Emas) */
        .badge-info {
            background-color: var(--accent) !important;
            color: #333 !important;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: 600;
        }

        /* Form Input Styling */
        .form-control, .form-select {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 8px 12px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary) !important;
            box-shadow: 0 0 0 3px rgba(248, 122, 1, 0.2) !important;
            outline: none;
        }
        
        /* Alert Messages */
        .alert-success {
            background-color: #d1e7dd; /* Default bootstrap soft green agar terbaca */
            border-left: 5px solid var(--primary);
            color: var(--primary);
        }
        
        /* Card Styling Enhancement */
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid #eee;
            overflow: hidden;
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
                    <h2>Kelola Data Komoditas</h2>
                    <p>Tambahkan, edit, atau hapus jenis komoditas bahan pangan</p>
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

                <div class="card">
                    <div class="card-header">
                        <h3>Tambah Komoditas Baru</h3>
                    </div>
                    <div style="padding: 20px;">
                        <form method="POST" action="">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                <div class="form-group">
                                    <label class="form-label">Nama Komoditas</label>
                                    <input type="text" class="form-control" name="nama_komoditas" required 
                                           placeholder="Contoh: Beras Premium">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Kategori</label>
                                    <select class="form-select" name="kategori" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="beras">Beras</option>
                                        <option value="sayur">Sayuran</option>
                                        <option value="buah">Buah-buahan</option>
                                        <option value="protein">Protein</option>
                                        <option value="bumbu">Bumbu</option>
                                        <option value="minyak">Minyak</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Satuan</label>
                                    <input type="text" class="form-control" name="satuan" required 
                                           placeholder="Contoh: kg, liter, ikat">
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 20px;">
                                <button type="submit" name="tambah_komoditas" class="btn btn-primary">Tambah Komoditas</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Daftar Komoditas</h3>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Komoditas</th>
                                    <th>Kategori</th>
                                    <th>Satuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($komoditas) > 0): ?>
                                    <?php $no = 1; ?>
                                    <?php while($row = mysqli_fetch_assoc($komoditas)): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row['nama_komoditas'] ?></td>
                                            <td>
                                                <span class="badge badge-info"><?= ucfirst($row['kategori']) ?></span>
                                            </td>
                                            <td><?= $row['satuan'] ?></td>
                                            <td>
                                                <a href="?hapus=<?= $row['id_komoditas'] ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Yakin ingin menghapus komoditas ini?')">
                                                    Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center;">Belum ada data komoditas</td>
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