-- Buat database
CREATE DATABASE svasembada;
USE svasembada;

-- Tabel untuk user/admin
CREATE TABLE users (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin_pusat', 'admin_lokal') NOT NULL,
    kecamatan VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel komoditas
CREATE TABLE komoditas (
    id_komoditas INT PRIMARY KEY AUTO_INCREMENT,
    nama_komoditas VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    satuan VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel monitoring stok dan harga
CREATE TABLE monitoring (
    id_monitoring INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    id_komoditas INT NOT NULL,
    stok DECIMAL(10,2) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    kecamatan VARCHAR(100) NOT NULL,
    tanggal_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user),
    FOREIGN KEY (id_komoditas) REFERENCES komoditas(id_komoditas)
);

-- Tabel pengaduan masyarakat
CREATE TABLE pengaduan (
    id_pengaduan INT PRIMARY KEY AUTO_INCREMENT,
    nama_pengadu VARCHAR(100) NOT NULL,
    kecamatan VARCHAR(100) NOT NULL,
    komoditas VARCHAR(100),
    isi_laporan TEXT NOT NULL,
    status ENUM('pending', 'proses', 'selesai') DEFAULT 'pending',
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel informasi pangan (untuk halaman informasi.php)
CREATE TABLE informasi_pangan (
    id_informasi INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(200) NOT NULL,
    konten TEXT NOT NULL,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);