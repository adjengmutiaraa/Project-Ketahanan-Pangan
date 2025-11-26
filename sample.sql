-- Insert admin default (password: admin123)
INSERT INTO users (nama, email, password, role) VALUES 
('Administrator Pusat', 'admin@svasembada.go.id', 'admin123', 'admin_pusat'),
('Admin Kecamatan A', 'kecamatan_a@svasembada.go.id', 'admin123', 'admin_lokal'),
('Admin Kecamatan B', 'kecamatan_b@svasembada.go.id', 'admin123', 'admin_lokal');

-- Insert data komoditas
INSERT INTO komoditas (nama_komoditas, kategori, satuan) VALUES
('Beras Premium', 'beras', 'kg'),
('Beras Medium', 'beras', 'kg'),
('Jagung', 'serealia', 'kg'),
('Bawang Merah', 'sayur', 'kg'),
('Bawang Putih', 'sayur', 'kg'),
('Cabai Merah', 'sayur', 'kg'),
('Cabai Rawit', 'sayur', 'kg'),
('Pisang', 'buah', 'kg'),
('Jeruk', 'buah', 'kg'),
('Daging Sapi', 'protein', 'kg'),
('Daging Ayam', 'protein', 'kg'),
('Telur Ayam', 'protein', 'butir'),
('Minyak Goreng', 'minyak', 'liter'),
('Gula Pasir', 'bumbu', 'kg');

-- Insert data monitoring sample
INSERT INTO monitoring (id_user, id_komoditas, stok, harga, kecamatan, tanggal_update) VALUES
(2, 1, 1500, 12500, 'Kecamatan A', NOW()),
(2, 4, 800, 35000, 'Kecamatan A', NOW()),
(2, 6, 300, 45000, 'Kecamatan A', NOW()),
(3, 1, 1200, 12800, 'Kecamatan B', NOW()),
(3, 4, 600, 36000, 'Kecamatan B', NOW()),
(3, 10, 200, 95000, 'Kecamatan B', NOW());

-- Insert data pengaduan sample
INSERT INTO pengaduan (nama_pengadu, kecamatan, komoditas, isi_laporan, status, tanggal) VALUES
('Budi Santoso', 'Kecamatan A', 'Cabai Merah', 'Harga cabai merah di pasar tradisional melonjak sangat tinggi, dari normal Rp 35.000 menjadi Rp 60.000 per kg', 'pending', NOW()),
('Siti Rahayu', 'Kecamatan B', 'Bawang Merah', 'Stok bawang merah sangat sulit ditemukan di pasar, toko-toko kehabisan persediaan', 'proses', NOW()),
('Ahmad Wijaya', 'Kecamatan A', 'Minyak Goreng', 'Distribusi minyak goreng tidak merata, beberapa warung tidak mendapatkan pasokan', 'selesai', NOW());

-- Insert data informasi pangan
INSERT INTO informasi_pangan (judul, konten, tanggal) VALUES
('Tips Menyimpan Bahan Pangan', 'Pelajari cara menyimpan berbagai jenis bahan pangan dengan benar untuk menjaga kesegaran dan kualitasnya dalam waktu yang lebih lama.', NOW()),
('Memahami Fluktuasi Harga Pangan', 'Faktor-faktor yang mempengaruhi naik turunnya harga bahan pangan dan bagaimana mengantisipasi perubahan harga di pasaran.', NOW());