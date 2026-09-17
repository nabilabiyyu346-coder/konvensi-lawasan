-- ============================================================
-- Skema Basis Data: Toko Batik Nusantara
-- Import file ini lewat phpMyAdmin, atau jalankan:
--   mysql -u root -p < database/schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS toko_batik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE toko_batik;

-- Hapus tabel lama dulu supaya file ini aman dijalankan ulang
-- (misalnya setelah update struktur pesanan/gambar produk).
DROP TABLE IF EXISTS pesanan_item;
DROP TABLE IF EXISTS pesanan;
DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS pelanggan;

-- ------------------------------------------------------------
-- Tabel: kategori
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS kategori (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(50) NOT NULL,
  slug VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO kategori (nama, slug) VALUES
  ('Kain Batik', 'kain'),
  ('Baju Batik', 'baju'),
  ('Aksesoris', 'aksesoris');

-- ------------------------------------------------------------
-- Tabel: produk
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS produk (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kategori_id INT NOT NULL,
  nama VARCHAR(120) NOT NULL,
  motif VARCHAR(80) NOT NULL,
  harga DECIMAL(12,2) NOT NULL,
  stok INT NOT NULL DEFAULT 0,
  deskripsi TEXT,
  gambar VARCHAR(255) DEFAULT NULL,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (kategori_id) REFERENCES kategori(id)
);

INSERT INTO produk (kategori_id, nama, motif, harga, stok, deskripsi, gambar) VALUES
  (1, 'Kain Batik Tulis Sogan', 'Sogan',       450000, 12, 'Kain batik tulis dengan pewarna alami soga, warna cokelat khas keraton, cocok untuk acara formal.', 'sogan.jpg'),
  (1, 'Kain Batik Cap Kawung', 'Kawung',       275000, 20, 'Kain batik cap motif kawung klasik, bahan katun primis yang adem dipakai harian.', 'kawung.jpg'),
  (1, 'Kain Batik Tulis Parang', 'Parang',     520000, 8,  'Motif parang tulis tangan, melambangkan kesinambungan dan kekuatan, bahan katun halus.', 'parang.jpg'),
  (1, 'Kain Batik Mega Mendung', 'Mega Mendung', 310000, 15, 'Motif awan khas Cirebon dengan gradasi warna biru yang lembut.', 'mega-mendung.jpg'),
  (2, 'Kemeja Batik Lengan Panjang Truntum', 'Truntum', 235000, 25, 'Kemeja batik pria lengan panjang, motif truntum, cocok untuk kerja maupun acara resmi.', 'truntum.jpg'),
  (2, 'Dress Batik Sekar Jagad', 'Sekar Jagad', 385000, 10, 'Dress wanita motif sekar jagad, potongan modern dengan sentuhan tradisional.', 'sekar-jagad.jpg'),
  (2, 'Kemeja Batik Lengan Pendek Lasem', 'Lasem', 195000, 30, 'Kemeja santai lengan pendek motif Lasem dengan warna-warna cerah khas pesisir.', 'lasem.jpg'),
  (2, 'Blouse Batik Sido Mukti', 'Sido Mukti',  265000, 18, 'Blouse wanita motif Sido Mukti, melambangkan harapan hidup sejahtera.', 'sido-mukti.jpg'),
  (3, 'Selendang Batik Prada', 'Prada',        180000, 14, 'Selendang dengan aksen prada (emas), cocok untuk pelengkap busana pesta.', 'prada.jpg'),
  (3, 'Totebag Batik Kawung', 'Kawung',        95000,  40, 'Totebag kanvas dengan cetakan motif kawung, ringan dan tahan lama untuk sehari-hari.', 'kawung.jpg'),
  (3, 'Masker Batik Truntum (isi 3)', 'Truntum', 45000, 60, 'Masker kain dua lapis motif truntum, nyaman dipakai untuk aktivitas luar ruangan.', 'truntum.jpg');

-- ------------------------------------------------------------
-- Tabel: admin (untuk login panel admin)
-- Akun default: username "admin", password "admin123"
-- GANTI password ini setelah login pertama kali di lingkungan asli.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admin (username, password) VALUES
  ('admin', '$2b$10$KQPm7ZA3tihI/8zME.i9GeOLHXcH9caowOzTMGqcnme1C6ZpV4njK');

-- ------------------------------------------------------------
-- Tabel: pelanggan (akun customer, terpisah dari admin)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pelanggan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  telepon VARCHAR(30),
  alamat TEXT,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Tabel: pesanan (header transaksi — hasil checkout keranjang)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pelanggan_id INT NULL,
  nama_pemesan VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL,
  telepon VARCHAR(30) NOT NULL,
  alamat TEXT NOT NULL,
  catatan TEXT,
  total DECIMAL(12,2) NOT NULL DEFAULT 0,
  status ENUM('baru','diproses','selesai') DEFAULT 'baru',
  metode_pembayaran ENUM('transfer_bank','cod') NOT NULL DEFAULT 'transfer_bank',
  status_pembayaran ENUM('menunggu','menunggu_verifikasi','lunas','ditolak') NOT NULL DEFAULT 'menunggu',
  bukti_bayar VARCHAR(255) DEFAULT NULL,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE SET NULL
);

-- ------------------------------------------------------------
-- Tabel: pesanan_item (rincian produk per transaksi)
-- Harga & nama produk disalin (snapshot) saat checkout, supaya
-- riwayat pesanan tidak berubah walau produk diedit/dihapus nanti.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pesanan_item (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pesanan_id INT NOT NULL,
  produk_id INT NULL,
  nama_produk VARCHAR(120) NOT NULL,
  harga_satuan DECIMAL(12,2) NOT NULL,
  jumlah INT NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
  FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE SET NULL
);
