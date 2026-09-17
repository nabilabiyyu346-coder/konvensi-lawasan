<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: produk.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    // pesanan_item menyimpan snapshot nama/harga, jadi aman dihapus
    // tanpa merusak riwayat transaksi (kolom produk_id di situ
    // hanya referensi, tidak dipakai FK ON DELETE CASCADE ke produk).
    $stmt = $pdo->prepare("DELETE FROM produk WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header('Location: produk.php?sukses=' . urlencode('Produk berhasil dihapus.'));
exit;
