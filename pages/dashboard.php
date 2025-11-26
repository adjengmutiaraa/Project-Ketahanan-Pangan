<?php 
session_start();
include '../config/koneksi.php';

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 1. Ambil data statistik GLOBAL/PUSAT
$total_komoditas = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM komoditas");
$total_komoditas = mysqli_fetch_assoc($total_komoditas)['total'];

$total_pengaduan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan");
$total_pengaduan = mysqli_fetch_assoc($total_pengaduan)['total'];

$pengaduan_pending = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan WHERE status = 'pending'");
$pengaduan_pending = mysqli_fetch_assoc($pengaduan_pending)['total'];

$pengaduan_selesai = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan WHERE status = 'selesai'");
$pengaduan_selesai = mysqli_fetch_assoc($pengaduan_selesai)['total'];

// 2. Filter data berdasarkan Role (Admin Lokal vs Admin Pusat)
$where_kecamatan_monitoring = ""; // Dipakai untuk query yang JOIN dengan tabel 'monitoring'
$where_kecamatan_simple = ""; // Dipakai untuk query yang HANYA tabel 'monitoring' atau 'pengaduan'
$kecamatan_label = "";

if ($_SESSION['role'] == 'admin_lokal') {
    $kecamatan_label = " (" . $_SESSION['kecamatan'] . ")";
    // Menggunakan alias tabel 'm' (monitoring) untuk JOIN
    $where_kecamatan_monitoring = " WHERE m.kecamatan = '{$_SESSION['kecamatan']}'"; 
    // Untuk query simple COUNT
    $where_kecamatan_simple = " WHERE kecamatan = '{$_SESSION['kecamatan']}'"; 
}

// Query COUNT monitoring tanpa JOIN - pakai $where_kecamatan_simple
$total_monitoring = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM monitoring $where_kecamatan_simple");
$total_monitoring = mysqli_fetch_assoc($total_monitoring)['total'];

// Data untuk chart (komoditas dengan stok terendah) - pakai $where_kecamatan_monitoring
$query_stok_rendah = "SELECT k.nama_komoditas, SUM(m.stok) as total_stok, k.satuan 
                     FROM monitoring m 
                     JOIN komoditas k ON m.id_komoditas = k.id_komoditas 
                     $where_kecamatan_monitoring 
                     GROUP BY k.id_komoditas, k.nama_komoditas, k.satuan
                     ORDER BY total_stok ASC 
                     LIMIT 5";
$stok_rendah_result = mysqli_query($koneksi, $query_stok_rendah);

$stok_label = [];
$stok_data = [];
$stok_unit = "";
while($row = mysqli_fetch_assoc($stok_rendah_result)) {
    $stok_label[] = $row['nama_komoditas'];
    $stok_data[] = $row['total_stok'];
    $stok_unit = $row['satuan']; 
}

// Data monitoring terbaru (JOIN user, komoditas) - pakai $where_kecamatan_monitoring
$query_monitoring = "SELECT m.*, k.nama_komoditas, k.satuan, u.nama as user_input 
                     FROM monitoring m 
                     JOIN komoditas k ON m.id_komoditas = k.id_komoditas 
                     JOIN users u ON m.id_user = u.id_user
                     $where_kecamatan_monitoring 
                     ORDER BY m.tanggal_update DESC 
                     LIMIT 5";
$monitoring_terbaru = mysqli_query($koneksi, $query_monitoring);

// Pengaduan terbaru (belum difilter berdasarkan kecamatan, tetap global)
$query_pengaduan = "SELECT * FROM pengaduan ORDER BY tanggal DESC LIMIT 5";
$pengaduan_terbaru = mysqli_query($koneksi, $query_pengaduan);

// Statistik pengaduan per status
$pengaduan_stats = mysqli_query($koneksi, 
    "SELECT status, COUNT(*) as total FROM pengaduan GROUP BY status"
);
$pengaduan_data = [];
while($row = mysqli_fetch_assoc($pengaduan_stats)) {
    $pengaduan_data[$row['status']] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SVASEMBADA</title>
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>

    <style>
        /* Gaya tambahan yang cocok untuk dashboard */
        .dashboard-grid {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }
        .data-card .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-link {
            color: var(--accent);
            font-size: 0.9rem;
            font-weight: 600;
        }
        .data-table .commodity-info strong {
            display: block;
        }
        .data-table .commodity-info span {
            font-size: 0.8em;
            color: #777;
        }
        .data-table .no-data-cell {
            text-align: center;
        }
        .no-data {
            padding: 20px;
            color: #999;
        }
        .no-data-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .chart-container {
            height: 300px; /* Tinggi standar untuk chart */
            padding: 15px;
        }
        .welcome-section {
            background: linear-gradient(135deg, var(--primary) 0%, #2a4d30 100%);
            color: var(--white);
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .welcome-section h1 {
            color: var(--white);
            margin: 0;
            font-size: 1.8rem;
        }
        .location-info {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
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
                <li><a href="../admin/admin_monitoring.php">Monitoring</a></li>
                <li><a href="../admin/admin_pengaduan.php">Pengaduan</a></li>
                <?php if($_SESSION['role'] == 'admin_pusat'): ?>
                    <li><a href="../admin/admin_komoditas.php">Master Komoditas</a></li>
                    <li><a href="../admin/admin_user.php">Manajemen User</a></li>
                <?php endif; ?>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="../admin/logout.php" class="btn btn-secondary btn-sm">Logout</a></li>
            </ul>
        </div>
    </header>

    <main>
        <section class="section" style="padding-top: 40px;">
            <div class="container">
                <div class="welcome-section">
                    <div class="welcome-content">
                        <h1>Selamat datang, <?= $_SESSION['nama'] ?>! 👋</h1>
                    </div>
                    <?php if($_SESSION['role'] == 'admin_lokal'): ?>
                        <div class="location-info">
                            Wilayah Tugas: <?= $_SESSION['kecamatan'] ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <div class="card-icon">🛒</div>
                        <h3>Total Komoditas</h3>
                        <span class="number"><?= $total_komoditas ?></span>
                        <div class="description">Jenis bahan pangan terdata</div>
                        <div class="card-trend positive">
                            <span>+5%</span> dari bulan lalu
                        </div>
                    </div>
                    <div class="dashboard-card">
                        <div class="card-icon">📈</div>
                        <h3>Data Monitoring<?= $kecamatan_label ?></h3>
                        <span class="number"><?= $total_monitoring ?></span>
                        <div class="description">Entri data stok & harga</div>
                        <div class="card-trend positive">
                            <span>+12%</span> update hari ini
                        </div>
                    </div>
                    <div class="dashboard-card">
                        <div class="card-icon">📢</div>
                        <h3>Total Pengaduan</h3>
                        <span class="number"><?= $total_pengaduan ?></span>
                        <div class="description">Laporan dari masyarakat</div>
                        <div class="card-trend <?= $total_pengaduan > 0 ? 'negative' : 'positive' ?>">
                            <span><?= $total_pengaduan > 0 ? '+'.$total_pengaduan : '0' ?></span> bulan ini
                        </div>
                    </div>
                    <div class="dashboard-card">
                        <div class="card-icon">⏳</div>
                        <h3>Pending Tindak Lanjut</h3>
                        <span class="number"><?= $pengaduan_pending ?></span>
                        <div class="description">Pengaduan menunggu penanganan</div>
                        <div class="card-trend <?= $pengaduan_pending > 0 ? 'negative' : 'positive' ?>">
                            <span><?= $pengaduan_pending ?></span> perlu tindakan
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="action-buttons">
                        <a href="../admin/admin_monitoring.php" class="btn btn-primary">
                            📊 Kelola Data Monitoring
                        </a>
                        <a href="../admin/admin_pengaduan.php" class="btn btn-secondary">
                            📝 Kelola Pengaduan
                        </a>
                        <?php if($_SESSION['role'] == 'admin_pusat'): ?>
                            <a href="../admin/admin_komoditas.php" class="btn btn-accent">
                                🛒 Kelola Komoditas
                            </a>
                            <a href="../admin/admin_user.php" class="btn btn-success">
                                👥 Kelola Admin
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; margin-top: 30px;">
                    
                    <div class="card">
                        <div class="card-header">
                            <h3>Komoditas Stok Terendah</h3>
                            <span class="btn-link" style="color: <?= empty($stok_label) ? 'var(--info)' : 'var(--danger)' ?>;">
                                Berdasarkan Total Stok <?= $kecamatan_label ?>
                            </span>
                        </div>
                        <div class="chart-container">
                            <canvas id="stokRendahChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3>Statistik Status Pengaduan</h3>
                            <span class="btn-link" style="color: var(--primary);">
                                Total Pengaduan: <?= $total_pengaduan ?>
                            </span>
                        </div>
                        <div class="chart-container">
                            <canvas id="pengaduanStatusChart"></canvas>
                        </div>
                    </div>

                    <div class="data-card">
                        <div class="card-header">
                            <h3>Data Monitoring Terbaru</h3>
                            <a href="../admin/admin_monitoring.php" class="btn-link">Lihat Semua →</a>
                        </div>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Komoditas</th>
                                        <th>Stok</th>
                                        <th>Harga</th>
                                        <th>Update</th>
                                        <th>Input Oleh</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($monitoring_terbaru) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($monitoring_terbaru)): ?>
                                            <?php 
                                            $status_class = 'badge-success';
                                            $status_text = 'Aman';
                                            if ($row['stok'] < 100) {
                                                $status_class = 'badge-danger';
                                                $status_text = 'Rendah';
                                            } elseif ($row['stok'] < 500) {
                                                $status_class = 'badge-warning';
                                                $status_text = 'Sedang';
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="commodity-info">
                                                        <strong><?= $row['nama_komoditas'] ?></strong>
                                                        <span><?= $row['kecamatan'] ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="stock-info">
                                                        <strong><?= number_format($row['stok']) ?></strong>
                                                        <span><?= $row['satuan'] ?></span>
                                                    </div>
                                                </td>
                                                <td class="price-cell">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                                <td class="time-cell"><?= date('d/m H:i', strtotime($row['tanggal_update'])) ?></td>
                                                <td><?= $row['user_input'] ?></td>
                                                <td>
                                                    <span class="badge <?= $status_class ?>"><?= $status_text ?></span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="no-data-cell">
                                                <div class="no-data">
                                                    <div class="no-data-icon">📊</div>
                                                    <p>Belum ada data monitoring</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <div class="card">
                        <div class="card-header">
                            <h3>Pengaduan Terbaru</h3>
                            <a href="../admin/admin_pengaduan.php" class="btn-link">Lihat Semua →</a>
                        </div>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Kecamatan</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($pengaduan_terbaru) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($pengaduan_terbaru)): ?>
                                            <tr>
                                                <td><?= substr($row['nama_pengadu'], 0, 15) ?>...</td>
                                                <td><?= $row['kecamatan'] ?></td>
                                                <td>
                                                    <?php 
                                                    $badge_class = 'badge-warning';
                                                    if ($row['status'] == 'proses') $badge_class = 'badge-info';
                                                    if ($row['status'] == 'selesai') $badge_class = 'badge-success';
                                                    ?>
                                                    <span class="badge <?= $badge_class ?>"><?= ucfirst($row['status']) ?></span>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" style="text-align: center;">Tidak ada pengaduan</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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

    <script>
        // Data PHP ke JavaScript
        const stokLabel = <?= json_encode($stok_label) ?>;
        const stokData = <?= json_encode($stok_data) ?>;
        const stokUnit = '<?= $stok_unit ?: "kg" ?>';

        const pengaduanData = <?= json_encode($pengaduan_data) ?>;
        const pengaduanLabel = Object.keys(pengaduanData);
        const pengaduanValue = Object.values(pengaduanData);

        // Chart 1: Komoditas Stok Terendah
        const ctxStok = document.getElementById('stokRendahChart');
        if (ctxStok) {
            new Chart(ctxStok, {
                type: 'bar',
                data: {
                    labels: stokLabel,
                    datasets: [{
                        label: 'Sisa Stok (' + stokUnit + ')',
                        data: stokData,
                        backgroundColor: [
                            'rgba(211, 47, 47, 0.8)', // Merah untuk yang terendah
                            'rgba(255, 193, 7, 0.8)', // Kuning
                            'rgba(0, 77, 64, 0.8)', // Primary/Teal
                            'rgba(0, 121, 107, 0.8)',
                            'rgba(0, 150, 136, 0.8)'
                        ],
                        borderColor: 'rgba(255, 255, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        title: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true },
                        x: { display: true }
                    }
                }
            });
        }
        
        // Chart 2: Statistik Status Pengaduan
        const ctxPengaduan = document.getElementById('pengaduanStatusChart');
        if (ctxPengaduan) {
            new Chart(ctxPengaduan, {
                type: 'doughnut',
                data: {
                    labels: pengaduanLabel.map(label => label.charAt(0).toUpperCase() + label.slice(1)), // Kapitalisasi
                    datasets: [{
                        label: 'Status Pengaduan',
                        data: pengaduanValue,
                        backgroundColor: [
                            'rgba(251, 192, 45, 0.8)', // Warning (Pending)
                            'rgba(25, 118, 210, 0.8)', // Info (Proses)
                            'rgba(56, 142, 60, 0.8)' // Success (Selesai)
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        title: { display: false }
                    }
                }
            });
        }
    </script>
    <script src="../assets/script.js"></script>
</body>
</html>