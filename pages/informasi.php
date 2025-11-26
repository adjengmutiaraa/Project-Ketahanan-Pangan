<?php 
session_start();
include '../config/koneksi.php';

// Query untuk mendapatkan informasi pangan
$query = "SELECT * FROM informasi_pangan ORDER BY tanggal DESC LIMIT 10";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Pangan - SVASEMBADA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/style.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Set Font Family Global */
        body {
            font-family: 'Poppins', sans-serif;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        /* Ubah cursor jadi pointer agar terlihat bisa diklik */
        .news-card {
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-decoration: none; /* Hilangkan garis bawah pada link */
            color: #333;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            border: 1px solid #eee;
            cursor: pointer; /* Penanda bisa diklik */
        }
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            /* Efek hover border menggunakan warna kuning emas */
            border-color: #FED16A; 
        }
        .card-image-wrapper {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }
        .card-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .date-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            /* Menggunakan warna Hijau Tua palette #386641 */
            background-color: #386641; 
            color: #FFF4A4; /* Teks warna Kuning Muda */
            padding: 8px 15px;
            text-align: center;
            border-top-left-radius: 12px;
        }
        .date-day { display: block; font-size: 1.4rem; font-weight: 800; line-height: 1; }
        .date-month { display: block; font-size: 0.75rem; text-transform: uppercase; font-weight: 500; }
        .card-content { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        
        .card-content h3 { 
            margin: 0 0 12px 0; 
            font-size: 1.15rem; 
            /* Judul menggunakan warna Oranye #F87A01 agar kontras */
            color: #F87A01; 
            font-weight: 700; 
        }
        
        .card-content p { font-size: 0.95rem; color: #666; margin: 0; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

        /* Override Header Background */
        header {
            background-color: #386641 !important; /* Hijau Tua */
        }
        
        .nav-menu li a.active {
            color:#F87A01 !important; /* Kuning Emas untuk menu aktif */
        }
        
        h1, h2, h3 {
            font-family: 'Poppins', sans-serif;
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
                    <h2 style="color: #386641;">Informasi & Edukasi Pangan</h2>
                    <p>Artikel dan informasi terkini seputar ketahanan pangan dan tips konsumsi sehat</p>
                </div>

                <div class="news-grid">
                    
                    <a href="https://www.ibudigital.com/10-cara-menyimpan-bahan-makanan-yang-baik-dan-benar/" class="news-card">
                        <div class="card-image-wrapper">
                            <img src="https://images.unsplash.com/photo-1606787366850-de6330128bfc?q=80&w=600&auto=format&fit=crop" alt="Penyimpanan">
                            <div class="date-badge">
                                <span class="date-day">10</span>
                                <span class="date-month">Mei</span>
                                <span class="date-year">2025</span>
                            </div>
                        </div>
                        <div class="card-content">
                            <h3>Tips Menyimpan Bahan Pangan</h3>
                            <p>Pelajari cara menyimpan berbagai jenis bahan pangan dengan benar untuk menjaga kesegaran dan kualitasnya dalam waktu yang lebih lama.</p>
                        </div>
                    </a>

                    <a href="https://sainstekno.net/2024/12/31/harga-komoditas-pangan-faktor-faktor-yang-mempengaruhi-dan-dampaknya/" class="news-card">
                        <div class="card-image-wrapper">
                            <img src="https://img.inews.co.id/media/822/files/inews_new/2021/04/08/pangan.jpg" alt="Harga Pangan">
                            <div class="date-badge">
                                <span class="date-day">31</span>
                                <span class="date-month">Des</span>
                                <span class="date-year">2024</span>
                            </div>
                        </div>
                        <div class="card-content">
                            <h3>Memahami Fluktuasi Harga Pangan</h3>
                            <p>Faktor-faktor yang mempengaruhi naik turunnya harga bahan pangan dan bagaimana mengantisipasi perubahan harga di pasaran.</p>
                        </div>
                    </a>

                    <a href="https://bangkalan.pikiran-rakyat.com/kuliner/pr-2748742785/kepentingan-makanan-lokal-mengapa-konsumsi-makanan-lokal-semakin-didorong?page=all" class="news-card">
                        <div class="card-image-wrapper">
                            <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=600&auto=format&fit=crop" alt="Pangan Lokal">
                            <div class="date-badge">
                                <span class="date-day">26</span>
                                <span class="date-month">NOV</span>
                                <span class="date-year">2025</span>
                            </div>
                        </div>
                        <div class="card-content">
                            <h3>Manfaat Konsumsi Pangan Lokal</h3>
                            <p>Keuntungan mengonsumsi bahan pangan yang diproduksi secara lokal untuk kesehatan, ekonomi, dan lingkungan.</p>
                        </div>
                    </a>

                    <a href="https://bbppkupang.bppsdmp.pertanian.go.id/blog/teknologi-pengawetan-pangan-membentengi-masa-depan-ketersediaan-makanan#:~:text=Artikel%20ini%20akan%20membahas%20berbagai%20teknologi%20pengawetan%20pangan,dan%20inovasi%20terkini%20dalam%20memperpanjang%20masa%20simpan%20pangan." class="news-card">
                        <div class="card-image-wrapper">
                            <img src="https://images.unsplash.com/photo-1606914501449-5a96b6ce24ca?q=80&w=600&auto=format&fit=crop" alt="Teknologi Pangan">
                            <div class="date-badge">
                                <span class="date-day">28</span>
                                <span class="date-month">Feb</span>
                                <span class="date-year">2024</span>
                            </div>
                        </div>
                        <div class="card-content">
                            <h3>Teknologi Pengawetan Pangan Modern</h3>
                            <p>Inovasi terbaru dalam teknologi pengawetan pangan yang dapat membantu mengurangi food waste dan memperpanjang masa simpan.</p>
                        </div>
                    </a>

                    <a href="https://sainstekno.net/2024/12/31/harga-komoditas-pangan-faktor-faktor-yang-mempengaruhi-dan-dampaknya/" class="news-card">
                        <div class="card-image-wrapper">
                            <img src="https://images.unsplash.com/photo-1578916171728-46686eac8d58?q=80&w=600&auto=format&fit=crop" alt="Belanja Cerdas">
                            <div class="date-badge">
                                <span class="date-day">31</span>
                                <span class="date-month">des</span>
                                <span class="date-year">2024</span>
                            </div>
                        </div>
                        <div class="card-content">
                            <h3>Panduan Berbelanja Pangan Cerdas</h3>
                            <p>Cara berbelanja bahan pangan yang efisien dan ekonomis tanpa mengorbankan kualitas dan gizi keluarga.</p>
                        </div>
                    </a>

                    <a href="https://www.ciptadesa.com/budidaya-pekarangan-pangan-lestari/" class="news-card">
                        <div class="card-image-wrapper">
                            <img src="https://cdn0-production-images-kly.akamaized.net/nQzsKxhJFhzZkvyppcyoD1tGf_Q=/1200x675/smart/filters:quality(75):strip_icc():format(jpeg)/kly-media-production/medias/807258/original/025663100_1423221324-buah_dan_sayur.jpg" alt="Budidaya">
                            <div class="date-badge">
                                <span class="date-day">16</span>
                                <span class="date-month">Okt</span>
                                <span class="date-year">2025</span>
                            </div>
                        </div>
                        <div class="card-content">
                            <h3>Budidaya Pangan di Pekarangan</h3>
                            <p>Panduan praktis untuk memanfaatkan pekarangan rumah untuk budidaya sayuran dan tanaman pangan lainnya.</p>
                        </div>
                    </a>
                    
                </div>
                <div class="card" style="margin-top: 40px;">
                    <div class="card-header">
                        <h3 style="color: #386641;">Tren Harga Komoditas Utama</h3>
                    </div>
                    <div style="padding: 20px; text-align: center;">
                      <div style="padding: 20px;">
                        
                        <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #eee; height: 400px; position: relative;">
                            <canvas id="grafikHarga"></canvas>
                        </div>

                        <div style="display: flex; justify-content: center; gap: 20px; margin-top: 20px; flex-wrap: wrap;">
                            <div style="text-align: center;">
                                <div style="font-weight: bold; color: #386641;">Beras</div>
                                <div style="font-size: 0.9rem; color: #666;">+2.3%</div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-weight: bold; color: #F87A01;">Cabai</div>
                                <div style="font-size: 0.9rem; color: #666;">-5.7%</div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-weight: bold; color: #FED16A;">Minyak</div>
                                <div style="font-size: 0.9rem; color: #666;">+1.2%</div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-weight: bold; color: #386641;">Gula</div>
                                <div style="font-size: 0.9rem; color: #666;">+0.5%</div>
                            </div>
                        </div>

                    </div>
                                </div>
                            </div>
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
    <script>
        const ctx = document.getElementById('grafikHarga').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'line', // Jenis grafik garis
            data: {
                labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                datasets: [
                    {
                        label: 'Beras',
                        data: [12000, 13000, 13500, 15000],
                        borderColor: '#386641', // Hijau Tua
                        backgroundColor: 'rgba(56, 102, 65, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Cabai',
                        data: [50000, 48000, 45000, 42000],
                        borderColor: '#F87A01', // Oranye
                        backgroundColor: 'rgba(248, 122, 1, 0.1)',
                        tension: 0.3,
                        fill: false
                    },
                    {
                        label: 'Minyak',
                        data: [14000, 15000, 14200, 18500],
                        borderColor: '#FED16A', // Kuning Emas
                        tension: 0.3,
                        fill: false
                    },
                    {
                        label: 'Gula',
                        data: [13000, 13500, 13600, 14500],
                        borderColor: '#FFF4A4', // Kuning Muda (Mungkin agak tipis, tapi sesuai request)
                        // Alternatif: pakai #green

                        borderColor: '#88f59eff', 
                        tension: 0.3,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                family: 'Poppins' // Font legend
                            }
                        }
                    },
                    tooltip: {
                        titleFont: { family: 'Poppins' },
                        bodyFont: { family: 'Poppins' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            font: { family: 'Poppins' }, // Font sumbu Y
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: { family: 'Poppins' } // Font sumbu X
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>