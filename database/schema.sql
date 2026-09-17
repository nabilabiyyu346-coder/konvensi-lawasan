-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 17, 2026 at 06:23 AM
-- Server version: 8.4.3
-- PHP Version: 8.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `batik`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dibuat_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `dibuat_pada`) VALUES
(1, 'admin', '$2b$10$KQPm7ZA3tihI/8zME.i9GeOLHXcH9caowOzTMGqcnme1C6ZpV4njK', '2026-09-16 07:41:13');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int NOT NULL,
  `nama` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama`, `slug`) VALUES
(1, 'Kain Batik', 'kain'),
(2, 'Baju Batik', 'baju'),
(3, 'Aksesoris', 'aksesoris');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int NOT NULL,
  `nama` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telepon` varchar(30) DEFAULT NULL,
  `alamat` text,
  `dibuat_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama`, `email`, `password`, `telepon`, `alamat`, `dibuat_pada`) VALUES
(1, 'Purnomo', 'nomo12@mail.com', '$2y$12$IBya3F6Nrn.pJ6d3VydvbO6v1LOwnWMK9834jZCepjGimtkGzOInC', '087652348777', NULL, '2026-09-16 07:53:24');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int NOT NULL,
  `pelanggan_id` int DEFAULT NULL,
  `nama_pemesan` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telepon` varchar(30) NOT NULL,
  `alamat` text NOT NULL,
  `catatan` text,
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('baru','diproses','selesai') DEFAULT 'baru',
  `metode_pembayaran` enum('transfer_bank','cod') NOT NULL DEFAULT 'transfer_bank',
  `status_pembayaran` enum('menunggu','menunggu_verifikasi','lunas','ditolak') NOT NULL DEFAULT 'menunggu',
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `dibuat_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id`, `pelanggan_id`, `nama_pemesan`, `email`, `telepon`, `alamat`, `catatan`, `total`, `status`, `metode_pembayaran`, `status_pembayaran`, `bukti_bayar`, `dibuat_pada`) VALUES
(1, 1, 'Purnomo', 'nomo12@mail.com', '087652348777', 'JL. DR CIPTO NO 32', '', 585000.00, 'baru', 'transfer_bank', 'lunas', 'pesanan-1-1789545265.jpg', '2026-09-16 07:53:59'),
(2, 1, 'Purnomo', 'nomo12@mail.com', '087652348777', 'JL. DR CIPTO NO 76', '', 660000.00, 'baru', 'transfer_bank', 'menunggu', NULL, '2026-09-16 10:33:04'),
(3, 1, 'Purnomo', 'nomo12@mail.com', '087652348777', 'JL. DR CIPTO NO 76', '', 1040000.00, 'baru', 'transfer_bank', 'menunggu_verifikasi', 'pesanan-3-1789554881.jpg', '2026-09-16 10:34:26'),
(4, 1, 'Purnomo', 'nomo12@mail.com', '087652348777', 'JL. DR CIPTO NO 76', '', 330000.00, 'baru', 'transfer_bank', 'lunas', 'pesanan-4-1789562178.png', '2026-09-16 12:35:43');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan_item`
--

CREATE TABLE `pesanan_item` (
  `id` int NOT NULL,
  `pesanan_id` int NOT NULL,
  `produk_id` int DEFAULT NULL,
  `nama_produk` varchar(120) NOT NULL,
  `harga_satuan` decimal(12,2) NOT NULL,
  `jumlah` int NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesanan_item`
--

INSERT INTO `pesanan_item` (`id`, `pesanan_id`, `produk_id`, `nama_produk`, `harga_satuan`, `jumlah`, `subtotal`) VALUES
(1, 1, 2, 'Kain Batik Cap Kawung', 275000.00, 1, 275000.00),
(2, 1, 4, 'Kain Batik Mega Mendung', 310000.00, 1, 310000.00),
(3, 2, 2, 'Kain Batik Cap Kawung', 275000.00, 1, 275000.00),
(4, 2, 6, 'Dress Batik Sekar Jagad', 385000.00, 1, 385000.00),
(5, 3, 3, 'Kain Batik Tulis Parang', 520000.00, 2, 1040000.00),
(6, 4, 5, 'Kemeja Batik Lengan Panjang Truntum', 235000.00, 1, 235000.00),
(7, 4, 10, 'Totebag Batik Kawung', 95000.00, 1, 95000.00);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int NOT NULL,
  `kategori_id` int NOT NULL,
  `nama` varchar(120) NOT NULL,
  `motif` varchar(80) NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `deskripsi` text,
  `gambar` varchar(255) DEFAULT NULL,
  `dibuat_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `kategori_id`, `nama`, `motif`, `harga`, `stok`, `deskripsi`, `gambar`, `dibuat_pada`) VALUES
(1, 1, 'Kain Batik Tulis Sogan', 'Sogan', 450000.00, 12, 'Kain batik tulis dengan pewarna alami soga, warna cokelat khas keraton, cocok untuk acara formal.', 'sogan.jpg', '2026-09-16 07:41:13'),
(2, 1, 'Kain Batik Cap Kawung', 'Kawung', 275000.00, 18, 'Kain batik cap motif kawung klasik, bahan katun primis yang adem dipakai harian.', 'kawung.jpg', '2026-09-16 07:41:13'),
(3, 1, 'Kain Batik Tulis Parang', 'Parang', 520000.00, 6, 'Motif parang tulis tangan, melambangkan kesinambungan dan kekuatan, bahan katun halus.', 'parang.jpg', '2026-09-16 07:41:13'),
(4, 1, 'Kain Batik Mega Mendung', 'Mega Mendung', 310000.00, 14, 'Motif awan khas Cirebon dengan gradasi warna biru yang lembut.', 'mega-mendung.jpg', '2026-09-16 07:41:13'),
(5, 2, 'Kemeja Batik Lengan Panjang Truntum', 'Truntum', 235000.00, 24, 'Kemeja batik pria lengan panjang, motif truntum, cocok untuk kerja maupun acara resmi.', 'truntum.jpg', '2026-09-16 07:41:13'),
(6, 2, 'Dress Batik Sekar Jagad', 'Sekar Jagad', 385000.00, 9, 'Dress wanita motif sekar jagad, potongan modern dengan sentuhan tradisional.', 'sekar-jagad.jpg', '2026-09-16 07:41:13'),
(7, 2, 'Kemeja Batik Lengan Pendek Lasem', 'Lasem', 195000.00, 30, 'Kemeja santai lengan pendek motif Lasem dengan warna-warna cerah khas pesisir.', 'lasem.jpg', '2026-09-16 07:41:13'),
(8, 2, 'Blouse Batik Sido Mukti', 'Sido Mukti', 265000.00, 18, 'Blouse wanita motif Sido Mukti, melambangkan harapan hidup sejahtera.', 'sido-mukti.jpg', '2026-09-16 07:41:13'),
(9, 3, 'Selendang Batik Prada', 'Prada', 180000.00, 14, 'Selendang dengan aksen prada (emas), cocok untuk pelengkap busana pesta.', 'prada.jpg', '2026-09-16 07:41:13'),
(10, 3, 'Totebag Batik Kawung', 'Kawung', 95000.00, 40, 'Totebag kanvas dengan cetakan motif kawung, ringan dan tahan lama untuk sehari-hari.', 'kawung.jpg', '2026-09-16 07:41:13'),
(11, 3, 'Masker Batik Truntum (isi 3)', 'Truntum', 45000.00, 60, 'Masker kain dua lapis motif truntum, nyaman dipakai untuk aktivitas luar ruangan.', 'truntum.jpg', '2026-09-16 07:41:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelanggan_id` (`pelanggan_id`);

--
-- Indexes for table `pesanan_item`
--
ALTER TABLE `pesanan_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesanan_id` (`pesanan_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pesanan_item`
--
ALTER TABLE `pesanan_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pesanan_item`
--
ALTER TABLE `pesanan_item`
  ADD CONSTRAINT `pesanan_item_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesanan_item_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
