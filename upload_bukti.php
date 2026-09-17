<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/customer_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

wajibLoginPelanggan('index.php');

$pesananId = (int) ($_POST['pesanan_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM pesanan WHERE id = :id");
$stmt->execute([':id' => $pesananId]);
$pesanan = $stmt->fetch();

// Keamanan: pastikan pesanan ini benar milik pelanggan yang sedang login
if (!$pesanan || (int) $pesanan['pelanggan_id'] !== (int) $_SESSION['pelanggan_id']) {
    header('Location: index.php');
    exit;
}

if ($pesanan['metode_pembayaran'] !== 'transfer_bank') {
    header('Location: konfirmasi.php?id=' . $pesananId);
    exit;
}

$errors = [];
$file = $_FILES['bukti'] ?? null;

$allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$maxSize = 3 * 1024 * 1024; // 3MB

if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
    $errors[] = 'Gagal mengunggah file. Coba lagi.';
} elseif (!isset($allowedTypes[$file['type']])) {
    $errors[] = 'Format file harus JPG, PNG, atau WEBP.';
} elseif ($file['size'] > $maxSize) {
    $errors[] = 'Ukuran file maksimal 3MB.';
}

if (!empty($errors)) {
    header('Location: konfirmasi.php?id=' . $pesananId . '&error=' . urlencode(implode(' ', $errors)));
    exit;
}

$ext = $allowedTypes[$file['type']];
$filename = 'pesanan-' . $pesananId . '-' . time() . '.' . $ext;
$destination = __DIR__ . '/assets/uploads/bukti/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    header('Location: konfirmasi.php?id=' . $pesananId . '&error=' . urlencode('Gagal menyimpan file di server.'));
    exit;
}

// Hapus bukti lama kalau ada (mis. setelah upload ulang karena ditolak)
if ($pesanan['bukti_bayar']) {
    $old = __DIR__ . '/assets/uploads/bukti/' . $pesanan['bukti_bayar'];
    if (is_file($old)) {
        unlink($old);
    }
}

$update = $pdo->prepare("
    UPDATE pesanan
    SET bukti_bayar = :bukti, status_pembayaran = 'menunggu_verifikasi'
    WHERE id = :id
");
$update->execute([':bukti' => $filename, ':id' => $pesananId]);

header('Location: konfirmasi.php?id=' . $pesananId);
exit;
