<?php 
session_start();
include '../config/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    
    // Query untuk mencari user berdasarkan email
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($koneksi, $query);
    
    // ... (Bagian atas PHP tetap sama) ...

if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
    
    // ⚠️ Perbaikan Keamanan: Gunakan password_verify()
    // ASUMSI: Data user di database sudah di-hash. 
    // Jika belum, Anda harus meng-hash password di INSERT/UPDATE user baru.
    // Untuk saat ini, kita akan menggunakan plain text jika tidak ada hash (seperti di data sampel Anda)
    // Namun, idealnya, kode ini harus menggunakan:
    // if (password_verify($password, $user['password'])) { ... }
    
    // KODE SEMENTARA (Menggunakan plain text sesuai sample.sql)
    // KODE YANG SEHARUSNYA DIGUNAKAN DI PROYEK NYATA: if (password_verify($password, $user['password']))
    if ($password === $user['password']) {
        // Set session
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['kecamatan'] = $user['kecamatan'];
        
        // Redirect ke dashboard
       header('Location: ../pages/dashboard.php');
    exit();
    } else {
        $error = "Password yang Anda masukkan salah!";
    }
} else {
    $error = "Email tidak ditemukan!";
}

// ... (Bagian bawah PHP tetap sama) ...
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SVASEMBADA</title>
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
            <li><a href="../index.php">Beranda</a></li>
            <li><a href="../pages/monitoring.php">Monitoring</a></li>
            <li><a href="../pages/informasi.php">Informasi Pangan</a></li>
            <li><a href="../pages/pengaduan.php">Pengaduan</a></li>
            
            <li><a href="login.php" class="btn btn-secondary btn-sm active">LOGIN ADMIN</a></li>
        </ul>
    </div>
</header>
    <main>
        <section class="section" style="padding: 40px 0;">
            <div class="container">
                <div class="login-container">
                    <div class="login-box">
                        <div class="login-logo">
                            <h2>Login Admin</h2>
                            <p style="color: #666; margin-top: 10px;">Masuk ke sistem dashboard SVASEMBADA</p>
                        </div>

                        <?php if($error): ?>
                            <div class="alert alert-danger">
                                <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="form-group">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required 
                                       placeholder="Masukkan email admin" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required 
                                       placeholder="Masukkan password">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">Login</button>
                            </div>
                        </form>

                        <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                            <small style="color: #666;">
                                Hanya untuk admin terdaftar. Jika lupa password, hubungi administrator.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Informasi Akun Demo -->
                <div class="card" style="max-width: 800px; margin: 30px auto;">
                    <div class="card-header">
                        <h3>Informasi Login Demo</h3>
                    </div>
                    <div style="padding: 20px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                            <div class="dashboard-card">
                                <h3>Admin Pusat</h3>
                                <div style="margin-top: 15px;">
                                    <div><strong>Email:</strong> admin@svasembada.go.id</div>
                                    <div><strong>Password:</strong> admin123</div>
                                </div>
                            </div>
                            <div class="dashboard-card">
                                <h3>Admin Lokal (Kecamatan A)</h3>
                                <div style="margin-top: 15px;">
                                    <div><strong>Email:</strong> kecamatan_a@svasembada.go.id</div>
                                    <div><strong>Password:</strong> admin123</div>
                                </div>
                            </div>
                            <div class="dashboard-card">
                                <h3>Admin Lokal (Kecamatan B)</h3>
                                <div style="margin-top: 15px;">
                                    <div><strong>Email:</strong> kecamatan_b@svasembada.go.id</div>
                                    <div><strong>Password:</strong> admin123</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Hak Akses -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; margin-top: 30px; max-width: 800px; margin-left: auto; margin-right: auto;">
                    <div class="card">
                        <div class="card-header">
                            <h3 style="color: var(--primary);">Admin Pusat</h3>
                        </div>
                        <div style="padding: 20px;">
                            <ul style="list-style-type: none; padding: 0;">
                                <li style="padding: 8px 0; border-bottom: 1px solid #f5f5f5;">
                                    <span style="color: var(--success); margin-right: 10px;">✓</span>
                                    Akses ke semua kecamatan
                                </li>
                                <li style="padding: 8px 0; border-bottom: 1px solid #f5f5f5;">
                                    <span style="color: var(--success); margin-right: 10px;">✓</span>
                                    Kelola data komoditas
                                </li>
                                <li style="padding: 8px 0; border-bottom: 1px solid #f5f5f5;">
                                    <span style="color: var(--success); margin-right: 10px;">✓</span>
                                    Kelola user admin
                                </li>
                                <li style="padding: 8px 0; border-bottom: 1px solid #f5f5f5;">
                                    <span style="color: var(--success); margin-right: 10px;">✓</span>
                                    Input data monitoring
                                </li>
                                <li style="padding: 8px 0;">
                                    <span style="color: var(--success); margin-right: 10px;">✓</span>
                                    Kelola pengaduan
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 style="color: var(--primary);">Admin Lokal</h3>
                        </div>
                        <div style="padding: 20px;">
                            <ul style="list-style-type: none; padding: 0;">
                                <li style="padding: 8px 0; border-bottom: 1px solid #f5f5f5;">
                                    <span style="color: var(--success); margin-right: 10px;">✓</span>
                                    Akses terbatas ke kecamatan sendiri
                                </li>
                                <li style="padding: 8px 0; border-bottom: 1px solid #f5f5f5;">
                                    <span style="color: var(--danger); margin-right: 10px;">✗</span>
                                    Tidak bisa kelola komoditas
                                </li>
                                <li style="padding: 8px 0; border-bottom: 1px solid #f5f5f5;">
                                    <span style="color: var(--danger); margin-right: 10px;">✗</span>
                                    Tidak bisa kelola user
                                </li>
                                <li style="padding: 8px 0; border-bottom: 1px solid #f5f5f5;">
                                    <span style="color: var(--success); margin-right: 10px;">✓</span>
                                    Input data monitoring untuk kecamatan sendiri
                                </li>
                                <li style="padding: 8px 0;">
                                    <span style="color: var(--success); margin-right: 10px;">✓</span>
                                    Kelola pengaduan
                                </li>
                            </ul>
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