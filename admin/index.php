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

// ------------------------------------------------------------
// Data grafik penjualan, dengan filter periode.
// 30 hari  -> dikelompokkan per HARI  (30 batang)
// 3/6/12 bulan -> dikelompokkan per BULAN (3-12 batang)
// ------------------------------------------------------------
$periode = $_GET['periode'] ?? '30hari';
$periodeValid = ['30hari', '3bulan', '6bulan', '1tahun'];
if (!in_array($periode, $periodeValid, true)) {
    $periode = '30hari';
}

$labelChart = [];
$dataChart = [];

if ($periode === '30hari') {
    $mulaiChart = date('Y-m-d', strtotime('-29 days'));
    $stmtChart = $pdo->prepare("
        SELECT DATE(dibuat_pada) AS kunci, SUM(total) AS total
        FROM pesanan
        WHERE DATE(dibuat_pada) >= :mulai
        GROUP BY DATE(dibuat_pada)
    ");
    $stmtChart->execute([':mulai' => $mulaiChart]);
    $peta = [];
    foreach ($stmtChart->fetchAll() as $r) {
        $peta[$r['kunci']] = (float) $r['total'];
    }
    for ($i = 29; $i >= 0; $i--) {
        $tgl = date('Y-m-d', strtotime("-$i days"));
        $labelChart[] = date('d/m', strtotime($tgl));
        $dataChart[] = $peta[$tgl] ?? 0;
    }
} else {
    $jumlahBulan = ['3bulan' => 3, '6bulan' => 6, '1tahun' => 12][$periode];
    $mulaiChart = date('Y-m-01', strtotime('-' . ($jumlahBulan - 1) . ' months'));
    $stmtChart = $pdo->prepare("
        SELECT DATE_FORMAT(dibuat_pada, '%Y-%m') AS kunci, SUM(total) AS total
        FROM pesanan
        WHERE dibuat_pada >= :mulai
        GROUP BY DATE_FORMAT(dibuat_pada, '%Y-%m')
    ");
    $stmtChart->execute([':mulai' => $mulaiChart]);
    $peta = [];
    foreach ($stmtChart->fetchAll() as $r) {
        $peta[$r['kunci']] = (float) $r['total'];
    }
    for ($i = $jumlahBulan - 1; $i >= 0; $i--) {
        $kunci = date('Y-m', strtotime("-$i months"));
        $labelChart[] = date('M Y', strtotime($kunci . '-01'));
        $dataChart[] = $peta[$kunci] ?? 0;
    }
}

// --- Data grafik: penjualan per kategori (mengikuti periode yang sama) ---
$stmtKategori = $pdo->prepare("
    SELECT k.nama, SUM(pi.subtotal) AS total
    FROM pesanan_item pi
    JOIN produk pr ON pr.id = pi.produk_id
    JOIN kategori k ON k.id = pr.kategori_id
    JOIN pesanan p ON p.id = pi.pesanan_id
    WHERE p.dibuat_pada >= :mulai
    GROUP BY k.nama
    ORDER BY total DESC
");
$stmtKategori->execute([':mulai' => $mulaiChart]);
$kategoriChart = $stmtKategori->fetchAll();

$labelPeriode = [
    '30hari' => '30 Hari Terakhir',
    '3bulan' => '3 Bulan Terakhir',
    '6bulan' => '6 Bulan Terakhir',
    '1tahun' => '1 Tahun Terakhir',
][$periode];

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

  <div class="filter-chips">
    <a href="?periode=30hari" class="chip-link <?= $periode === '30hari' ? 'is-active' : '' ?>">30 Hari</a>
    <a href="?periode=3bulan" class="chip-link <?= $periode === '3bulan' ? 'is-active' : '' ?>">3 Bulan</a>
    <a href="?periode=6bulan" class="chip-link <?= $periode === '6bulan' ? 'is-active' : '' ?>">6 Bulan</a>
    <a href="?periode=1tahun" class="chip-link <?= $periode === '1tahun' ? 'is-active' : '' ?>">1 Tahun</a>
  </div>

  <div class="chart-grid">
    <section class="dashboard-panel">
      <div class="dashboard-panel__head"><h2>Grafik Penjualan — <?= $labelPeriode ?></h2></div>
      <canvas id="chartPenjualan" height="220" role="img" aria-label="Grafik batang total penjualan"></canvas>
    </section>
    <section class="dashboard-panel">
      <div class="dashboard-panel__head"><h2>Penjualan per Kategori — <?= $labelPeriode ?></h2></div>
      <?php if (empty($kategoriChart)): ?>
        <p class="empty-state">Belum ada data penjualan pada periode ini.</p>
      <?php else: ?>
        <canvas id="chartKategori" height="220" role="img" aria-label="Grafik lingkaran proporsi penjualan per kategori"></canvas>
      <?php endif; ?>
    </section>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartPenjualan'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($labelChart) ?>,
    datasets: [{
      label: 'Penjualan (Rp)',
      data: <?= json_encode($dataChart) ?>,
      backgroundColor: '#B8622E',
      borderRadius: 4,
      maxBarThickness: 42
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { callback: (v) => 'Rp ' + Number(v).toLocaleString('id-ID') }
      }
    }
  }
});

<?php if (!empty($kategoriChart)): ?>
new Chart(document.getElementById('chartKategori'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode(array_column($kategoriChart, 'nama')) ?>,
    datasets: [{
      data: <?= json_encode(array_map('floatval', array_column($kategoriChart, 'total'))) ?>,
      backgroundColor: ['#B8622E', '#52735F', '#3A3F5C', '#B8863B', '#1C2138'],
      borderWidth: 2,
      borderColor: '#FBF9F5'
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          boxWidth: 12,
          font: { size: 11 },
          generateLabels: (chart) => {
            const data = chart.data.datasets[0].data;
            const total = data.reduce((a, b) => a + b, 0);
            return chart.data.labels.map((label, i) => {
              const pct = total ? ((data[i] / total) * 100).toFixed(1) : 0;
              return {
                text: `${label} — ${pct}%`,
                fillStyle: chart.data.datasets[0].backgroundColor[i],
                strokeStyle: chart.data.datasets[0].borderColor,
                index: i
              };
            });
          }
        }
      },
      tooltip: {
        callbacks: {
          label: (context) => {
            const value = context.parsed;
            const total = context.dataset.data.reduce((a, b) => a + b, 0);
            const pct = total ? ((value / total) * 100).toFixed(1) : 0;
            const rupiah = 'Rp ' + Number(value).toLocaleString('id-ID');
            return `${context.label}: ${rupiah} (${pct}%)`;
          }
        }
      }
    }
  }
});
<?php endif; ?>
</script>

<?php require_once 'includes/admin_footer.php'; ?>
