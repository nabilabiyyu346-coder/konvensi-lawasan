<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: kategori.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $cek = $pdo->prepare("SELECT COUNT(*) AS jumlah FROM produk WHERE kategori_id = :id");
    $cek->execute([':id' => $id]);
    $jumlahProduk = (int) $cek->fetch()['jumlah'];

    if ($jumlahProduk > 0) {
        header('Location: kategori.php?error=' . urlencode("Tidak bisa hapus — masih ada $jumlahProduk produk di kategori ini. Pindahkan atau hapus produknya dulu."));
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM kategori WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header('Location: kategori.php?sukses=' . urlencode('Kategori berhasil dihapus.'));
exit;
