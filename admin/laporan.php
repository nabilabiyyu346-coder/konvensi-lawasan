<?php
require_once 'includes/auth.php';
require_once '../config/database.php';
require_once '../includes/helpers.php';

$pageTitle = 'Laporan Penjualan';

$mulai   = $_GET['mulai']   ?? date('Y-m-01'); // awal bulan ini
$selesai = $_GET['selesai'] ?? date('Y-m-d');  // hari ini

$stmt = $pdo->prepare("
    SELECT * FROM pesanan
    WHERE DATE(dibuat_pada) BETWEEN :mulai AND :selesai
    ORDER BY dibuat_pada DESC
");
$stmt->execute([':mulai' => $mulai, ':selesai' => $selesai]);
$pesananList = $stmt->fetchAll();

$totalPeriode = array_sum(array_column($pesananList, 'total'));
$jumlahTransaksi = count($pesananList);

$stmtTerlaris = $pdo->prepare("
    SELECT pi.nama_produk, SUM(pi.jumlah) AS total_terjual, SUM(pi.subtotal) AS total_omzet
    FROM pesanan_item pi
    JOIN pesanan p ON p.id = pi.pesanan_id
    WHERE DATE(p.dibuat_pada) BETWEEN :mulai AND :selesai
    GROUP BY pi.nama_produk
    ORDER BY total_terjual DESC
    LIMIT 5
");
$stmtTerlaris->execute([':mulai' => $mulai, ':selesai' => $selesai]);
$produkTerlaris = $stmtTerlaris->fetchAll();

require_once 'includes/admin_header.php';
?>

<main class="admin-main">
  <h1>Laporan Penjualan</h1>

  <form class="report-filter" method="get">
    <div>
      <label for="mulai">Dari tanggal</label>
      <input type="date" id="mulai" name="mulai" value="<?= htmlspecialchars($mulai) ?>">
    </div>
    <div>
      <label for="selesai">Sampai tanggal</label>
      <input type="date" id="selesai" name="selesai" value="<?= htmlspecialchars($selesai) ?>">
    </div>
    <button type="submit" class="btn btn--solid">Tampilkan</button>
    <a href="laporan_export.php?mulai=<?= urlencode($mulai) ?>&selesai=<?= urlencode($selesai) ?>" class="btn btn--ghost">Export CSV</a>
  </form>

  <div class="stat-grid stat-grid--report">
    <div class="stat-card">
      <p class="stat-card__label">Jumlah Transaksi</p>
      <p class="stat-card__value"><?= $jumlahTransaksi ?></p>
    </div>
    <div class="stat-card stat-card--accent">
      <p class="stat-card__label">Total Penjualan</p>
      <p class="stat-card__value"><?= rupiah($totalPeriode) ?></p>
    </div>
  </div>

  <div class="dashboard-grid">
    <section class="dashboard-panel">
      <div class="dashboard-panel__head"><h2>Daftar Transaksi</h2></div>
      <?php if (empty($pesananList)): ?>
        <p class="empty-state">Tidak ada transaksi pada rentang tanggal ini.</p>
      <?php else: ?>
        <table class="admin-table">
          <thead><tr><th>#</th><th>Pemesan</th><th>Total</th><th>Bayar</th><th>Status</th><th>Tanggal</th></tr></thead>
          <tbody>
            <?php foreach ($pesananList as $p): ?>
              <tr>
                <td>#<?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['nama_pemesan']) ?></td>
                <td><?= rupiah($p['total']) ?></td>
                <td><span class="badge-status badge-status--<?= htmlspecialchars($p['status_pembayaran']) ?>"><?= str_replace('_', ' ', $p['status_pembayaran']) ?></span></td>
                <td><span class="badge-status badge-status--<?= htmlspecialchars($p['status']) ?>"><?= htmlspecialchars($p['status']) ?></span></td>
                <td><?= date('d M Y H:i', strtotime($p['dibuat_pada'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>

    <section class="dashboard-panel">
      <div class="dashboard-panel__head"><h2>Produk Terlaris</h2></div>
      <?php if (empty($produkTerlaris)): ?>
        <p class="empty-state">Belum ada data penjualan pada periode ini.</p>
      <?php else: ?>
        <table class="admin-table">
          <thead><tr><th>Produk</th><th>Terjual</th><th>Omzet</th></tr></thead>
          <tbody>
            <?php foreach ($produkTerlaris as $t): ?>
              <tr>
                <td><?= htmlspecialchars($t['nama_produk']) ?></td>
                <td><?= (int) $t['total_terjual'] ?> pcs</td>
                <td><?= rupiah($t['total_omzet']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>
  </div>
</main>

<?php require_once 'includes/admin_footer.php'; ?>
