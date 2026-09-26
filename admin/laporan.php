<?php
require_once 'includes/auth.php';
require_once '../config/database.php';
require_once '../includes/helpers.php';

$pageTitle = 'Laporan Penjualan';

$mulai      = $_GET['mulai']       ?? date('Y-m-01'); // awal bulan ini
$selesai    = $_GET['selesai']     ?? date('Y-m-d');  // hari ini
$kategoriId = $_GET['kategori_id'] ?? '';

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY nama")->fetchAll();

// --- Daftar transaksi pada periode (+ kategori jika dipilih) ---
$sql = "
    SELECT DISTINCT p.*
    FROM pesanan p
    JOIN pesanan_item pi ON pi.pesanan_id = p.id
    LEFT JOIN produk pr ON pr.id = pi.produk_id
    WHERE DATE(p.dibuat_pada) BETWEEN :mulai AND :selesai
";
$params = [':mulai' => $mulai, ':selesai' => $selesai];
if ($kategoriId !== '') {
    $sql .= " AND pr.kategori_id = :kategori_id";
    $params[':kategori_id'] = $kategoriId;
}
$sql .= " ORDER BY p.dibuat_pada DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pesananList = $stmt->fetchAll();

$totalPeriode = array_sum(array_column($pesananList, 'total'));
$jumlahTransaksi = count($pesananList);

// --- Produk terlaris pada periode (+ kategori jika dipilih) ---
$sqlTerlaris = "
    SELECT pi.nama_produk, SUM(pi.jumlah) AS total_terjual, SUM(pi.subtotal) AS total_omzet
    FROM pesanan_item pi
    JOIN pesanan p ON p.id = pi.pesanan_id
    LEFT JOIN produk pr ON pr.id = pi.produk_id
    WHERE DATE(p.dibuat_pada) BETWEEN :mulai AND :selesai
";
$paramsTerlaris = [':mulai' => $mulai, ':selesai' => $selesai];
if ($kategoriId !== '') {
    $sqlTerlaris .= " AND pr.kategori_id = :kategori_id";
    $paramsTerlaris[':kategori_id'] = $kategoriId;
}
$sqlTerlaris .= " GROUP BY pi.nama_produk ORDER BY total_terjual DESC LIMIT 5";

$stmtTerlaris = $pdo->prepare($sqlTerlaris);
$stmtTerlaris->execute($paramsTerlaris);
$produkTerlaris = $stmtTerlaris->fetchAll();

$kategoriNamaTerpilih = '';
foreach ($kategoriList as $k) {
    if ((string) $k['id'] === (string) $kategoriId) {
        $kategoriNamaTerpilih = $k['nama'];
    }
}

require_once 'includes/admin_header.php';
?>

<main class="admin-main">
  <div class="admin-main__head no-print">
    <h1>Laporan Penjualan</h1>
    <button onclick="window.print()" class="btn btn--ghost">Cetak Laporan</button>
  </div>

  <!-- Kop laporan, cuma tampil saat dicetak -->
  <div class="print-only print-header" style="margin-bottom:1.5rem;">
    <h1>Batik Lawasan</h1>
    <p>Laporan Penjualan · <?= date('d M Y', strtotime($mulai)) ?> – <?= date('d M Y', strtotime($selesai)) ?>
      <?= $kategoriNamaTerpilih ? '· Kategori: ' . htmlspecialchars($kategoriNamaTerpilih) : '' ?>
    </p>
  </div>

  <form class="report-filter no-print" method="get">
    <div>
      <label for="mulai">Dari tanggal</label>
      <input type="date" id="mulai" name="mulai" value="<?= htmlspecialchars($mulai) ?>">
    </div>
    <div>
      <label for="selesai">Sampai tanggal</label>
      <input type="date" id="selesai" name="selesai" value="<?= htmlspecialchars($selesai) ?>">
    </div>
    <div>
      <label for="kategori_id">Kategori</label>
      <select id="kategori_id" name="kategori_id">
        <option value="">Semua kategori</option>
        <?php foreach ($kategoriList as $k): ?>
          <option value="<?= $k['id'] ?>" <?= (string)$kategoriId === (string)$k['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($k['nama']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn btn--solid">Tampilkan</button>
    <a href="laporan_export.php?mulai=<?= urlencode($mulai) ?>&selesai=<?= urlencode($selesai) ?>&kategori_id=<?= urlencode($kategoriId) ?>" class="btn btn--ghost">Export CSV</a>
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
      <div class="dashboard-panel__head"><h2>Produk Terlaris<?= $kategoriNamaTerpilih ? ' — ' . htmlspecialchars($kategoriNamaTerpilih) : '' ?></h2></div>
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
