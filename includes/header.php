<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/customer_auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

/**
 * $pageTitle harus diset di file pemanggil sebelum include ini.
 */
$current = basename($_SERVER['PHP_SELF']);
$jumlahKeranjang = jumlahKeranjang($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Konvensi Lawasan') ?> — Konvensi Lawasan</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="nav">
  <a href="index.php" class="nav__brand">Konvensi<span>.</span>Lawasan</a>
  <button class="nav__toggle" id="navToggle" aria-label="Buka menu">
    <span></span><span></span><span></span>
  </button>
  <ul class="nav__links" id="navLinks">
    <li><a href="index.php"   class="<?= $current === 'index.php' ? 'is-active' : '' ?>">Beranda</a></li>
    <li><a href="katalog.php" class="<?= $current === 'katalog.php' ? 'is-active' : '' ?>">Katalog</a></li>
    <li><a href="tentang.php" class="<?= $current === 'tentang.php' ? 'is-active' : '' ?>">Tentang</a></li>
    <li><a href="kontak.php"  class="<?= $current === 'kontak.php' ? 'is-active' : '' ?>">Kontak</a></li>
    <li>
      <a href="keranjang.php" class="nav__cart <?= $current === 'keranjang.php' ? 'is-active' : '' ?>">
        Keranjang
        <?php if ($jumlahKeranjang > 0): ?>
          <span class="nav__cart-badge"><?= $jumlahKeranjang ?></span>
        <?php endif; ?>
      </a>
    </li>
    <li>
      <?php if (pelangganLoggedIn()): ?>
        <a href="logout.php" class="nav__account">Halo, <?= htmlspecialchars(explode(' ', pelangganNama())[0]) ?> · Logout</a>
      <?php else: ?>
        <a href="login.php" class="nav__account">Login</a>
      <?php endif; ?>
    </li>
  </ul>
</nav>
