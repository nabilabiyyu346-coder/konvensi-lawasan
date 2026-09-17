<?php
require_once 'includes/auth.php';
require_once '../config/database.php';
require_once '../includes/helpers.php';

$pageTitle = 'Dashboard';

$totalProduk    = (int) $pdo->query("SELECT COUNT(*) AS n FROM produk")->fetch()['n'];
$totalKategori  = (int) $pdo->query("SELECT COUNT(*) AS n FROM kategori")->fetch()['n'];
$totalPesanan   = (int) $pdo->query("SELECT COUNT(*) AS n FROM pesanan")->fetch()['n'];
$totalPendapatan = (float) $pdo->query("SELECT COALESCE(SUM(total),0) AS n FROM pesanan")->fetch()['n'];

$pesananTerbaru = $pdo->query("
    SELECT * FROM pesanan ORDER BY dibuat_pada DESC LIMIT 5
")->fetchAll();

$stokMenipis = $pdo->query("
    SELECT * FROM produk WHERE stok <= 5 ORDER BY stok ASC LIMIT 5
")->fetchAll();

require_once 'includes/admin_header.php';
?>

<main class="admin-main">
  <h1>Dashboard</h1>

  <div class="stat-grid">
    <div class="stat-card">
      <p class="stat-card__label">Total Produk</p>
      <p class="stat-card__value"><?= $totalProduk ?></p>
    </div>
    <div class="stat-card">
      <p class="stat-card__label">Total Kategori</p>
      <p class="stat-card__value"><?= $totalKategori ?></p>
    </div>
    <div class="stat-card">
      <p class="stat-card__label">Total Pesanan</p>
      <p class="stat-card__value"><?= $totalPesanan ?></p>
    </div>
    <div class="stat-card stat-card--accent">
      <p class="stat-card__label">Total Pendapatan</p>
      <p class="stat-card__value"><?= rupiah($totalPendapatan) ?></p>
    </div>
  </div>

  <div class="dashboard-grid">
    <section class="dashboard-panel">
      <div class="dashboard-panel__head">
        <h2>Pesanan Terbaru</h2>
        <a href="pesanan.php">Lihat semua &rarr;</a>
      </div>
      <?php if (empty($pesananTerbaru)): ?>
        <p class="empty-state">Belum ada pesanan masuk.</p>
      <?php else: ?>
        <table class="admin-table">
          <thead><tr><th>#</th><th>Pemesan</th><th>Total</th><th>Tanggal</th></tr></thead>
          <tbody>
            <?php foreach ($pesananTerbaru as $p): ?>
              <tr>
                <td>#<?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['nama_pemesan']) ?></td>
                <td><?= rupiah($p['total']) ?></td>
                <td><?= date('d M Y H:i', strtotime($p['dibuat_pada'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>

    <section class="dashboard-panel">
      <div class="dashboard-panel__head">
        <h2>Stok Menipis</h2>
        <a href="produk.php">Kelola produk &rarr;</a>
      </div>
      <?php if (empty($stokMenipis)): ?>
        <p class="empty-state">Semua stok produk masih aman.</p>
      <?php else: ?>
        <table class="admin-table">
          <thead><tr><th>Produk</th><th>Stok</th></tr></thead>
          <tbody>
            <?php foreach ($stokMenipis as $p): ?>
              <tr>
                <td><a href="produk_form.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?></a></td>
                <td><span class="badge-warning"><?= (int) $p['stok'] ?> pcs</span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>
  </div>
</main>

<?php require_once 'includes/admin_footer.php'; ?>
