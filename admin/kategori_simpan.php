<?php
require_once 'includes/auth.php';
require_once '../config/database.php';
require_once '../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: kategori.php');
    exit;
}

$id   = (int) ($_POST['id'] ?? 0);
$nama = trim($_POST['nama'] ?? '');

if ($nama === '') {
    header('Location: kategori_form.php?' . http_build_query(['id' => $id, 'error' => 'Nama kategori wajib diisi.']));
    exit;
}

$slug = slugify($nama);

// Pastikan slug unik (kecuali terhadap dirinya sendiri saat edit)
$stmt = $pdo->prepare("SELECT id FROM kategori WHERE slug = :slug AND id != :id");
$stmt->execute([':slug' => $slug, ':id' => $id]);
if ($stmt->fetch()) {
    header('Location: kategori_form.php?' . http_build_query(['id' => $id, 'error' => 'Kategori dengan nama serupa sudah ada.']));
    exit;
}

if ($id > 0) {
    $stmt = $pdo->prepare("UPDATE kategori SET nama = :nama, slug = :slug WHERE id = :id");
    $stmt->execute([':nama' => $nama, ':slug' => $slug, ':id' => $id]);
    $pesan = 'Kategori berhasil diperbarui.';
} else {
    $stmt = $pdo->prepare("INSERT INTO kategori (nama, slug) VALUES (:nama, :slug)");
    $stmt->execute([':nama' => $nama, ':slug' => $slug]);
    $pesan = 'Kategori berhasil ditambahkan.';
}

header('Location: kategori.php?sukses=' . urlencode($pesan));
exit;
