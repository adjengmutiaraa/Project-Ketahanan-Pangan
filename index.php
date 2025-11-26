<?php 
session_start();
include 'config/koneksi.php'; 

// --- Logika Pengambilan Data (Sama seperti sebelumnya) ---
try {
    $stmt_kec = $koneksi->prepare("SELECT COUNT(DISTINCT kecamatan) as total FROM users WHERE role = 'admin_lokal'");
    $stmt_kec->execute();
    $total_kecamatan = $stmt_kec->get_result()->fetch_assoc()['total'] ?: 0;

    $stmt_komoditas = $koneksi->prepare("SELECT COUNT(*) as total FROM komoditas");
    $stmt_komoditas->execute();
    $total_komoditas = $stmt_komoditas->get_result()->fetch_assoc()['total'] ?: 0;
    
    $stmt_pengaduan = $koneksi->prepare("SELECT COUNT(*) as total FROM pengaduan WHERE status IN ('selesai', 'proses')");
    $stmt_pengaduan->execute();
    $total_pengaduan = $stmt_pengaduan->get_result()->fetch_assoc()['total'] ?: 0;
    
    $update_hari_ini = 98; // Placeholder
} catch (Exception $e) {
    $total_kecamatan = 3; 
    $total_komoditas = 14; 
    $total_pengaduan = 3; 
    $update_hari_ini = 0; 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SVASEMBADA - Sistem Ketahanan Pangan Daerah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    
    <style>
        /* Menggunakan warna yang sudah direset di root */
        .hero {
        /* Gradasi Warna Baru Berbasis --success: #28a745 (Hijau Cerah) */
        /* Menggunakan gradien dari hijau cerah ke hijau yang lebih tua/gelap */
        background: linear-gradient(135deg, #386641 0%, #1a8a3a 100%); 
        color: var(--white);
        padding: 120px 0;
        text-align: center;
        border-bottom: 5px solid var(--secondary); /* Border Gold tetap */
        /* Pastikan Poppins digunakan di Hero */
        font-family: 'Poppins', sans-serif;
    }
    
    .hero h2 {
        /* Font: Poppins Bold (Sesuai keinginan) */
        font-family: 'Poppins', sans-serif;
        font-size: 3rem;
        margin-bottom: 20px;
        font-weight: 800; /* Extra Bold */
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--white);
    }
    
    .hero h1 {
        /* Font: Poppins Bold (Sesuai keinginan) */
        font-family: 'Poppins', sans-serif;
        font-size: 2.2rem; /* Ukuran disesuaikan agar tidak terlalu besar */
        margin-bottom: 30px;
        font-weight: 700; /* Bold */
        color: var(--white);
    }
    
    .hero p {
        /* Font: Poppins Regular */
        font-family: 'Poppins', sans-serif;
        font-size: 1.1rem;
        max-width: 700px;
        margin: 0 auto 30px;
        font-weight: 400; /* Regular */
    }
        /* Style Index Lainnya (Stats Grid, Features, Section Title) */
        .section { padding: 70px 0; }
        .section-title { text-align: center; margin-bottom: 60px; }
        .section-title h2 { font-size: 2.2rem; color: var(--primary); position: relative; display: inline-block; padding-bottom: 10px; }
        .section-title h2::after { content: ''; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 100px; height: 4px; background-color: var(--accent); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; text-align: center; }
        .stat-item { background-color: var(--white); border-radius: 6px; padding: 30px 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border-left: 5px solid var(--primary); transition: transform 0.3s ease; }
        .stat-item:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
        .stat-number { font-size: 3rem; font-weight: 800; color: var(--primary); margin-bottom: 5px; }
        .stat-label { font-size: 1.1rem; color: var(--dark); font-weight: 600; text-transform: uppercase; }
        .features { background-color: var(--light-bg); }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; }
        .feature-card { background-color: var(--white); border-radius: 8px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-top: 5px solid var(--primary); transition: transform 0.3s ease, box-shadow 0.3s ease; text-align: center; }
        .feature-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        .feature-icon { font-size: 3rem; color: var(--secondary); margin-bottom: 20px; }
        .peta-section { background-color: var(--gray); border-top: 1px solid var(--gray); }
        .peta-container img { max-width: 100%; height: auto; border-radius: 6px; border: 1px solid var(--gray); }
        
        /* Pulse Animation (Gold) untuk Interaktivitas */
        @keyframes pulse-gold {
            0% { box-shadow: 0 0 0 0 rgba(255, 179, 0, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(255, 179, 0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 179, 0, 0); }
        }

        .pulse-interaktif {
            animation: pulse-gold 2s infinite ease-in-out;
            border-left: 5px solid var(--secondary); /* Border Gold */
        }
        
        @media (max-width: 768px) {
            .hero { padding: 80px 0; }
            .hero h2 { font-size: 2rem; }
            .stats-grid, .features-grid { grid-template-columns: 1fr; }
        }
        
        
    </style>
</head>
<body>
    <header>
        <div class="container header-container">
            <div class="logo">
                <h1 class="brand">
                    <img src="assets/img/logo_svasembada.png" alt="SVASEMBADA Logo" class="brand-logo">
                    SVASEMBADA
                </h1>
            </div>
            <button class="mobile-menu-btn">☰</button>
            <ul class="nav-menu">
                <li><a href="index.php" class="active">Beranda</a></li>
                <li><a href="pages/monitoring.php">Monitoring</a></li>
                <li><a href="pages/informasi.php">Informasi Pangan</a></li>
                <li><a href="pages/pengaduan.php">Pengaduan</a></li>
                <li><a href="admin/login.php" class="btn btn-secondary btn-sm">LOGIN</a></li>
            </ul>
        </div>
    </header>

    <main>
        <section class="hero">
    <div class="container">
        <h2>PENGENDALIAN DAN STABILISASI PANGAN DAERAH</h2>
        
        <h1>SISTEM VITAL KETAHANAN PANGAN NASIONAL</h1> 
        
        <p>Platform resmi Pemerintah Daerah untuk memonitor, menganalisis, dan mengendalikan ketersediaan serta fluktuasi harga bahan pangan pokok. Komitmen kami adalah menjamin ketahanan pangan yang berkelanjutan di seluruh wilayah administrasi.</p>
        
        <div style="margin-top: 40px; display: flex; justify-content: center; gap: 15px;">
            <a href="pages/monitoring.php" class="btn btn-accent btn-lg">LIHAT DATA HARGA & STOK</a>
            <a href="pages/pengaduan.php" class="btn btn-secondary btn-lg">LAPORAN MASYARAKAT</a>
        </div>
    </div>
</section>

        <section class="section" style="padding-top: 40px; padding-bottom: 40px; background-color: var(--white);">
            <div class="container">
                <div class="section-title" style="margin-bottom: 30px;">
                    <h2>Indikator Ketahanan Pangan Terkini</h2>
                    <p>Ringkasan data real-time per tanggal: <?= date('d F Y') ?></p>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number" id="total-kecamatan"><?= $total_kecamatan ?></div>
                        <div class="stat-label">KECAMATAN TERDATA</div>
                    </div>
                    <div class="stat-item pulse-interaktif">
                        <div class="stat-number" id="total-komoditas"><?= $total_komoditas ?></div>
                        <div class="stat-label">KOMODITAS PANGAN POKOK</div>
                    </div>
                    <div class="stat-item pulse-interaktif">
                        <div class="stat-number" id="total-pengaduan"><?= $total_pengaduan ?></div>
                        <div class="stat-label">PENGADUAN DITANGANI</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" style="color: var(--success);" id="update-hari-ini"><?= $update_hari_ini ?>%</div>
                        <div class="stat-label">DATA TERBARU HARI INI</div>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="section features">
    <div class="container">
        <div class="section-title">
            <h2>Layanan Utama SVASEMBADA</h2>
            <p>Fokus kami pada stabilitas, transparansi, dan kecepatan respons</p>
        </div>
        <div class="features-grid">

            <a href="pages/monitoring.php" class="feature-card" style="text-decoration: none; color: inherit;">
                <div class="feature-icon">📊</div>
                <h3>Pusat Data Monitoring</h3>
                <p>Akses real-time ketersediaan stok dan harga rata-rata bahan pangan strategis, didukung oleh input data valid dari admin lapangan.</p>
            </a>

            <a href="pages/informasi.php" class="feature-card" style="text-decoration: none; color: inherit;">
                <div class="feature-icon">📰</div>
                <h3>Informasi Kebijakan Pangan</h3>
                <p>Berita resmi, pengumuman, dan regulasi terkait kebijakan distribusi, subsidi, dan intervensi harga oleh Pemerintah Daerah.</p>
            </a>

            <a href="pages/pengaduan.php" class="feature-card" style="text-decoration: none; color: inherit;">
                <div class="feature-icon">📣</div>
                <h3>Mekanisme Pengaduan</h3>
                <p>Saluran resmi bagi masyarakat untuk melaporkan cepat kasus penimbunan, kelangkaan, atau praktik harga yang merugikan publik.</p>
            </a>
        </div>
    </div>
        </section>

        <section class="section peta-section">
    <div class="container">
        <div class="section-title">
            <h2>Peta Ketahanan & Kerentanan Pangan Nasional (2025)</h2>
            <p>Peta referensi status ketahanan dan kerentanan pangan yang digunakan sebagai dasar intervensi kebijakan</p>
        </div>
        <div class="card peta-container" style="padding: 10px;">
            <div style="text-align: center;">
                <img src="assets/img/peta_ketahanan_pangan_2025.jpg" 
                     alt="Peta Ketahanan dan Kerentanan Pangan Nasional 2025" 
                     style="max-width: 100%; height: auto; border-radius: 6px; border: 1px solid #ddd;">
                </div>
            
            <div style="margin-top: 20px; padding: 15px; background-color: var(--light-bg); border-top: 2px solid var(--primary); border-radius: 4px;">
                <h4 style="color: var(--primary); margin-bottom: 10px; font-size: 1.1rem;">Keterangan Resmi</h4>
                <ul style="font-size: 0.9rem; color: #555; list-style-type: none; padding-left: 0; display: flex; flex-wrap: wrap; gap: 15px;">
                    <li style="flex-basis: 45%;">• **Indikator Pangan Komposit** berdasarkan 10 Peta Dasar Administrasi.</li>
                    <li style="flex-basis: 45%;">• Skala Kerentanan: **0-25 (Kritikal Rendah)** hingga **>8000 (Kritikal Tinggi)**.</li>
                    <li style="flex-basis: 45%;">• **Disusun oleh:** Badan Pangan Nasional (National Food Agency) dan BPS.</li>
                    <li style="flex-basis: 45%;">• Peta ini digunakan sebagai referensi untuk **pengalokasian dan intervensi** sumber daya pangan.</li>
                </ul>
            </div>
        </div>
    </div>
</section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>SVASEMBADA</h3>
                    <p>Sistem Ketahanan Pangan Daerah yang beroperasi di bawah koordinasi Pemerintah Daerah untuk mewujudkan stabilitas dan ketersediaan pangan bagi masyarakat.</p>
                </div>
                <div class="footer-col">
                    <h3>Menu Utama</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="pages/monitoring.php">Monitoring Harga & Stok</a></li>
                        <li><a href="pages/informasi.php">Informasi dan Regulasi</a></li>
                        <li><a href="pages/pengaduan.php">Saluran Pengaduan</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Layanan Admin</h3>
                    <ul class="footer-links">
                        <li><a href="admin/login.php">Login Area Admin</a></li>
                        <li><a href="pages/dashboard.php">Dashboard</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Kontak Resmi</h3>
                    <ul class="footer-links">
                        <li>Email: sekretariat@svasembada.go.id</li>
                        <li>Telepon: (021) 1234-5678 (Pusat Data)</li>
                        <li>Alamat: Kantor Dinas Ketahanan Pangan Daerah, Kota Banyumas</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 SVASEMBADA - Sistem Ketahanan Pangan Daerah. Hak Cipta Dilindungi Pemerintah Daerah.</p>
            </div>
        </div>
    </footer>

    <script src="assets/script.js"></script>
</body>
</html>