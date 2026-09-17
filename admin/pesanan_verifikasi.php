<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: pesanan.php');
    exit;
}

$id   = (int) ($_POST['id'] ?? 0);
$aksi = $_POST['aksi'] ?? '';

if ($id > 0 && in_array($aksi, ['lunas', 'ditolak'], true)) {
    $stmt = $pdo->prepare("UPDATE pesanan SET status_pembayaran = :status WHERE id = :id");
    $stmt->execute([':status' => $aksi, ':id' => $id]);
}

$pesan = $aksi === 'lunas' ? 'Pembayaran berhasil diverifikasi.' : 'Bukti pembayaran ditolak.';
header('Location: pesanan.php?sukses=' . urlencode($pesan));
exit;
