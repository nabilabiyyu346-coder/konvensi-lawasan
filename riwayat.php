<?php
require_once 'config/database.php';
require_once 'includes/helpers.php';
require_once 'includes/session.php';
require_once 'includes/customer_auth.php';

wajibLoginPelanggan('riwayat.php');

$pageTitle = 'Riwayat Pembelian';

$stmt = $pdo->prepare("
    SELECT * FROM pesanan
    WHERE pelanggan_id = :pelanggan_id
    ORDER BY dibuat_pada DESC
");
$stmt->execute([':pelanggan_id' => $_SESSION['pelanggan_id']]);
$pesananList = $stmt->fetchAll();

$statusBayarLabel = [
    'menunggu'            => 'Menunggu pembayaran',
    'menunggu_verifikasi' => 'Menunggu verifikasi',
    'lunas'               => 'Lunas',
    'ditolak'             => 'Ditolak',
];

require_once 'includes/header.php';
?>

<main>
  <section class="section">
    <div class="section__head">
      <p class="section__index">Akun</p>
      <h2>Riwayat pembelian</h2>
    </div>

    <?php if (empty($pesananList)): ?>
      <p class="empty-state">Kamu belum pernah belanja. <a href="katalog.php">Lihat katalog produk</a>.</p>
    <?php else: ?>
      <div class="history-list">
        <?php foreach ($pesananList as $p): ?>
          <div class="history-card">
            <a href="konfirmasi.php?id=<?= $p['id'] ?>" class="history-card__link">
              <div class="history-card__main">
                <p class="history-card__id">Pesanan #<?= $p['id'] ?></p>
                <p class="history-card__date"><?= date('d M Y, H:i', strtotime($p['dibuat_pada'])) ?></p>
              </div>
              <div class="history-card__badges">
                <span class="badge-status badge-status--<?= htmlspecialchars($p['status_pembayaran']) ?>">
                  <?= htmlspecialchars($statusBayarLabel[$p['status_pembayaran']] ?? $p['status_pembayaran']) ?>
                </span>
                <span class="badge-status badge-status--<?= htmlspecialchars($p['status']) ?>"><?= htmlspecialchars($p['status']) ?></span>
              </div>
              <p class="history-card__total"><?= rupiah($p['total']) ?></p>
            </a>
            <a href="struk.php?id=<?= $p['id'] ?>" target="_blank" class="history-card__print">Cetak Struk</a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>
