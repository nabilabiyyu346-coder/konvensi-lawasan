<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — Admin Batik Nusantara</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-body">

<nav class="admin-nav">
  <a href="index.php" class="nav__brand">Batik<span>.</span>Admin</a>
  <ul class="admin-nav__links">
    <li><a href="index.php" class="<?= $current === 'index.php' ? 'is-active' : '' ?>">Dashboard</a></li>
    <li><a href="produk.php" class="<?= in_array($current, ['produk.php','produk_form.php']) ? 'is-active' : '' ?>">Produk</a></li>
    <li><a href="kategori.php" class="<?= in_array($current, ['kategori.php','kategori_form.php']) ? 'is-active' : '' ?>">Kategori</a></li>
    <li><a href="pesanan.php" class="<?= $current === 'pesanan.php' ? 'is-active' : '' ?>">Transaksi</a></li>
    <li><a href="laporan.php" class="<?= $current === 'laporan.php' ? 'is-active' : '' ?>">Laporan</a></li>
    <li><a href="../index.php" target="_blank">Lihat Toko &#8599;</a></li>
    <li><a href="logout.php" class="admin-nav__logout">Logout (<?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?>)</a></li>
  </ul>
</nav>
