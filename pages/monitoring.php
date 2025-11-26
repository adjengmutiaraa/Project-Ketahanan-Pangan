<?php 
session_start();
include '../config/koneksi.php';

// Ambil data filter dari URL
$kecamatan_filter = isset($_GET['kecamatan']) ? $_GET['kecamatan'] : '';
$komoditas_filter = isset($_GET['komoditas']) ? $_GET['komoditas'] : '';

// Query data monitoring dengan filter
$query = "SELECT m.*, k.nama_komoditas, k.satuan, k.kategori 
          FROM monitoring m 
          JOIN komoditas k ON m.id_komoditas = k.id_komoditas 
          WHERE 1=1";

if (!empty($kecamatan_filter)) {
    $query .= " AND m.kecamatan = '$kecamatan_filter'";
}
if (!empty($komoditas_filter)) {
    $query .= " AND m.id_komoditas = '$komoditas_filter'";
}

$query .= " ORDER BY m.tanggal_update DESC, m.kecamatan";
$result = mysqli_query($koneksi, $query);

// Ambil daftar kecamatan dan komoditas untuk filter
$kecamatan_list = mysqli_query($koneksi, "SELECT DISTINCT kecamatan FROM monitoring ORDER BY kecamatan");
$komoditas_list = mysqli_query($koneksi, "SELECT * FROM komoditas ORDER BY nama_komoditas");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Pangan - SVASEMBADA</title>
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* Status indicators */
        .status-box{
        display:flex;
        gap:20px;
        justify-content:center;
        margin:16px auto 28px;
        max-width:980px;
        }
        .status{
        min-width:230px;
        border-radius:12px;
        padding:14px 18px;
        color:#fff;
        font-weight:700;
        text-align:center;
        box-shadow:0 4px 12px rgba(0,0,0,0.06);
        font-size:16px;
        }
        .status small{display:block;font-weight:400;margin-top:6px;font-size:13px;opacity:0.95}
        .status-red{background:var(--danger)}
        .status-yellow{background:var(--warning);color:#3b2b00}
        .status-green{background:var(--success)}

        /* FILTER CONTAINER -------------------------------------------------- */
        .filter-container {
            width: 100%;
        }

        /* Form wrapper */
        .filter-container .filter-form {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Dua dropdown → harus melebar */
        .filter-container .filter-group {
            flex: 1; /* dropdown memanjang */
        }

        /* Style select agar mengikuti lebar 100% */
        .filter-container .filter-group select.form-select {
            width: 100%;
            padding: 12px 15px;
        }

                    /* Grup tombol → jangan melebar */
                    .filter-container .filter-group:last-child {
                        flex: 0; /* tombol tidak memanjang */
                        display: flex;
                        gap: 10px;
                        white-space: nowrap;
                    }

                    /* Style tombol */
                    .filter-container .filter-group:last-child .btn {
                        padding: 10px 18px;
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
            <li><a href="../index.php">Beranda</a></li>
            <li><a href="../pages/monitoring.php" class="active">Monitoring</a></li>
            <li><a href="../pages/informasi.php">Informasi Pangan</a></li>
            <li><a href="../pages/pengaduan.php">Pengaduan</a></li>
            <li><a href="../admin/login.php" class="btn btn-secondary btn-sm">LOGIN</a></li>
        </ul>
    </div>
</header>
    <main>
        <section class="section">
            <div class="container">
                <div class="section-title">
                    <h2>Monitoring Stok & Harga Pangan</h2>
                    <p>Data terkini ketersediaan stok dan harga bahan pangan di seluruh kecamatan</p>
                </div>

                <!-- Filter Section -->
                <div class="filter-container">
                    <form method="GET" class="filter-form">
                        <div class="filter-group">
                            <select name="kecamatan" class="form-select">
                                <option value="">Semua Kecamatan</option>
                                <?php while($kecamatan = mysqli_fetch_assoc($kecamatan_list)): ?>
                                    <option value="<?= $kecamatan['kecamatan'] ?>" 
                                        <?= $kecamatan_filter == $kecamatan['kecamatan'] ? 'selected' : '' ?>>
                                        <?= $kecamatan['kecamatan'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <select name="komoditas" class="form-select">
                                <option value="">Semua Komoditas</option>
                                <?php while($komoditas = mysqli_fetch_assoc($komoditas_list)): ?>
                                    <option value="<?= $komoditas['id_komoditas'] ?>" 
                                        <?= $komoditas_filter == $komoditas['id_komoditas'] ? 'selected' : '' ?>>
                                        <?= $komoditas['nama_komoditas'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <button type="submit" class="btn btn-primary">Cari</button>
                            <a href="monitoring.php" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>

                <!-- STATUS -->
                <div class="status-box" role="list" aria-label="Keterangan status stok">
                    <div class="status status-red" role="listitem">RENDAH <small>Stok &lt; 100 kg</small></div>
                    <div class="status status-yellow" role="listitem">SEDANG <small>Stok 100 - 500 kg</small></div>
                    <div class="status status-green" role="listitem">TINGGI <small>Stok &gt; 500 kg</small></div>
                </div>
                <!-- Data Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kecamatan</th>
                                <th>Komoditas</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th>Update Terakhir</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $row['kecamatan'] ?></td>
                                        <td><?= $row['nama_komoditas'] ?></td>
                                        <td><?= $row['kategori'] ?></td>
                                        <td><?= number_format($row['stok']) ?> <?= $row['satuan'] ?></td>
                                        <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['tanggal_update'])) ?></td>
                                        <td>
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
                                            <span class="badge <?= $status_class ?>"><?= $status_text ?></span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center;">Tidak ada data monitoring</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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