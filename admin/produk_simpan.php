<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: produk.php');
    exit;
}

$id          = (int) ($_POST['id'] ?? 0);
$nama        = trim($_POST['nama'] ?? '');
$kategoriId  = (int) ($_POST['kategori_id'] ?? 0);
$motif       = trim($_POST['motif'] ?? '');
$harga       = (float) ($_POST['harga'] ?? 0);
$stok        = (int) ($_POST['stok'] ?? 0);
$gambar      = trim($_POST['gambar'] ?? '') ?: null;
// Kalau berupa tautan, harus http(s) yang valid dan muat di kolom (255 karakter)
if ($gambar !== null && preg_match('#^[a-z][a-z0-9+.-]*://#i', $gambar)) {
    if (!filter_var($gambar, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $gambar) || strlen($gambar) > 255) {
        $gambar = null;
    }
}
$deskripsi   = trim($_POST['deskripsi'] ?? '');

$errors = [];
if ($nama === '')        $errors[] = 'Nama produk wajib diisi.';
if ($kategoriId <= 0)    $errors[] = 'Kategori wajib dipilih.';
if ($motif === '')       $errors[] = 'Motif wajib diisi.';
if ($harga <= 0)         $errors[] = 'Harga harus lebih dari 0.';
if ($stok < 0)           $errors[] = 'Stok tidak boleh negatif.';
if ($deskripsi === '')   $errors[] = 'Deskripsi wajib diisi.';

if (!empty($errors)) {
    $query = http_build_query(['id' => $id, 'error' => implode(' ', $errors)]);
    header('Location: produk_form.php?' . $query);
    exit;
}

if ($id > 0) {
    $stmt = $pdo->prepare("
        UPDATE produk
        SET nama = :nama, kategori_id = :kategori_id, motif = :motif,
            harga = :harga, stok = :stok, gambar = :gambar, deskripsi = :deskripsi
        WHERE id = :id
    ");
    $stmt->execute([
        ':nama' => $nama, ':kategori_id' => $kategoriId, ':motif' => $motif,
        ':harga' => $harga, ':stok' => $stok, ':gambar' => $gambar,
        ':deskripsi' => $deskripsi, ':id' => $id,
    ]);
    $pesan = 'Produk berhasil diperbarui.';
} else {
    $stmt = $pdo->prepare("
        INSERT INTO produk (kategori_id, nama, motif, harga, stok, deskripsi, gambar)
        VALUES (:kategori_id, :nama, :motif, :harga, :stok, :deskripsi, :gambar)
    ");
    $stmt->execute([
        ':kategori_id' => $kategoriId, ':nama' => $nama, ':motif' => $motif,
        ':harga' => $harga, ':stok' => $stok, ':deskripsi' => $deskripsi, ':gambar' => $gambar,
    ]);
    $pesan = 'Produk berhasil ditambahkan.';
}

header('Location: produk.php?sukses=' . urlencode($pesan));
exit;
