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

// Tambah user admin
if (isset($_POST['tambah_user'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);
    $kecamatan = ($role == 'admin_lokal') ? mysqli_real_escape_string($koneksi, $_POST['kecamatan']) : NULL;
    
    // Cek email sudah ada
    $check_email = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        $error = "Email sudah terdaftar!";
    } else {
        $query = "INSERT INTO users (nama, email, password, role, kecamatan) 
                  VALUES ('$nama', '$email', '$password', '$role', '$kecamatan')";
        
        if (mysqli_query($koneksi, $query)) {
            $success = "User admin berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan user: " . mysqli_error($koneksi);
        }
    }
}

// Hapus user
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    
    // Tidak boleh hapus diri sendiri
    if ($id == $_SESSION['user_id']) {
        $error = "Tidak dapat menghapus akun sendiri!";
    } else {
        $query = "DELETE FROM users WHERE id_user = '$id'";
        if (mysqli_query($koneksi, $query)) {
            $success = "User berhasil dihapus!";
        } else {
            $error = "Gagal menghapus user: " . mysqli_error($koneksi);
        }
    }
}

// Ambil data users
$users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY role, nama");

// Ambil daftar kecamatan
$kecamatan_list = mysqli_query($koneksi, "SELECT DISTINCT kecamatan FROM monitoring WHERE kecamatan IS NOT NULL ORDER BY kecamatan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Admin - SVASEMBADA</title>
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
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
                    <h2>Kelola User Admin</h2>
                    <p>Kelola akun admin pusat dan admin lokal</p>
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

                <!-- Form Tambah User -->
                <div class="card">
                    <div class="card-header">
                        <h3>Tambah User Admin Baru</h3>
                    </div>
                    <div style="padding: 20px;">
                        <form method="POST" action="">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                <div class="form-group">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" name="nama" required 
                                           placeholder="Nama lengkap admin">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" required 
                                           placeholder="Email admin">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" required 
                                           placeholder="Password">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Role</label>
                                    <select class="form-select" name="role" id="roleSelect" required onchange="toggleKecamatan()">
                                        <option value="">Pilih Role</option>
                                        <option value="admin_pusat">Admin Pusat</option>
                                        <option value="admin_lokal">Admin Lokal</option>
                                    </select>
                                </div>
                                
                                <div class="form-group" id="kecamatanField" style="display: none;">
                                    <label class="form-label">Kecamatan</label>
                                    <select class="form-select" name="kecamatan">
                                        <option value="">Pilih Kecamatan</option>
                                        <?php while($kecamatan = mysqli_fetch_assoc($kecamatan_list)): ?>
                                            <option value="<?= $kecamatan['kecamatan'] ?>">
                                                <?= $kecamatan['kecamatan'] ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 20px;">
                                <button type="submit" name="tambah_user" class="btn btn-primary">Tambah User</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Daftar User -->
                <div class="card">
                    <div class="card-header">
                        <h3>Daftar User Admin</h3>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Kecamatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($users) > 0): ?>
                                    <?php $no = 1; ?>
                                    <?php while($row = mysqli_fetch_assoc($users)): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <?= $row['nama'] ?>
                                                <?php if($row['id_user'] == $_SESSION['user_id']): ?>
                                                    <span class="badge badge-primary">Anda</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $row['email'] ?></td>
                                            <td>
                                                <?php 
                                                $badge_class = ($row['role'] == 'admin_pusat') ? 'badge-success' : 'badge-info';
                                                $role_text = ($row['role'] == 'admin_pusat') ? 'Admin Pusat' : 'Admin Lokal';
                                                ?>
                                                <span class="badge <?= $badge_class ?>"><?= $role_text ?></span>
                                            </td>
                                            <td><?= $row['kecamatan'] ?: '-' ?></td>
                                            <td>
                                                <?php if($row['id_user'] != $_SESSION['user_id']): ?>
                                                    <a href="?hapus=<?= $row['id_user'] ?>" 
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm('Yakin ingin menghapus user ini?')">
                                                        Hapus
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Tidak tersedia</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center;">Belum ada data user</td>
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
    function toggleKecamatan() {
        const roleSelect = document.getElementById('roleSelect');
        const kecamatanField = document.getElementById('kecamatanField');
        
        if (roleSelect.value === 'admin_lokal') {
            kecamatanField.style.display = 'block';
            kecamatanField.querySelector('select').required = true;
        } else {
            kecamatanField.style.display = 'none';
            kecamatanField.querySelector('select').required = false;
        }
    }
    </script>

    <script src="../assets/script.js"></script>
</body>
</html>