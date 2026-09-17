<?php
require_once 'includes/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: keranjang.php');
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$aksi     = $_POST['aksi'] ?? '';
$produkId = (int) ($_POST['produk_id'] ?? 0);
$jumlah   = max(1, (int) ($_POST['jumlah'] ?? 1));

switch ($aksi) {
    case 'tambah':
        if ($produkId > 0) {
            $_SESSION['cart'][$produkId] = ($_SESSION['cart'][$produkId] ?? 0) + $jumlah;
        }
        break;

    case 'update':
        if ($produkId > 0 && isset($_SESSION['cart'][$produkId])) {
            $_SESSION['cart'][$produkId] = $jumlah;
        }
        break;

    case 'hapus':
        unset($_SESSION['cart'][$produkId]);
        break;
}

// Kembali ke halaman asal (mis. dari produk.php), atau ke keranjang.php default
$tujuan = $_POST['kembali_ke'] ?? 'keranjang.php';
header('Location: ' . $tujuan);
exit;
