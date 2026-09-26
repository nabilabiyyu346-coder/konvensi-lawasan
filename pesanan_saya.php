<?php
require_once 'config/database.php';
require_once 'includes/helpers.php';
require_once 'includes/session.php';
require_once 'includes/customer_auth.php';

// Halaman ini yang jadi "jaring pengaman": kalau customer sempat pindah
// app buat transfer/QRIS lalu balik lagi tapi linknya sudah hilang,
// cukup login lagi dan semua pesanan yang belum lunas ada di sini.
wajibLoginPelanggan('pesanan_saya.php');

$stmt = $pdo->prepare("
    SELECT * FROM pesanan
    WHERE pelanggan_id = :id
    ORDER BY dibuat_pada DESC
");
$stmt->execute([':id' => $_SESSION['pelanggan_id']]);
$semuaPesanan = $stmt->fetchAll();

$pageTitle = 'Pesanan Saya';
require_once 'includes/header.php';
?>

<main>
  <section class="section">
    <p class="section__index">Akun</p>
    <h1>Pesanan Saya</h1>

    <?php if (empty($semuaPesanan)): ?>
      <p class="empty-state">Kamu belum punya pesanan. <a href="katalog.php">Lihat katalog</a> untuk mulai belanja.</p>
    <?php else: ?>
      <ul class="checkout__items" style="margin-top:1.5rem;">
        <?php foreach ($semuaPesanan as $p): ?>
          <li style="flex-direction:column; align-items:flex-start; gap:.4rem; padding:1rem 0;">
            <div style="display:flex; justify-content:space-between; width:100%; gap:1rem; flex-wrap:wrap;">
              <span>
                <strong>Pesanan #<?= (int) $p['id'] ?></strong>
                &middot; <?= date('d M Y H:i', strtotime($p['dibuat_pada'])) ?>
              </span>
              <span class="badge-status badge-status--<?= htmlspecialchars($p['status_pembayaran']) ?>">
                <?= str_replace('_', ' ', $p['status_pembayaran']) ?>
              </span>
            </div>
            <div style="display:flex; justify-content:space-between; width:100%; align-items:center; gap:1rem;">
              <span><?= rupiah($p['total']) ?></span>
              <?php if (in_array($p['status_pembayaran'], ['menunggu', 'ditolak'], true) && $p['metode_pembayaran'] === 'transfer_bank'): ?>
                <a href="konfirmasi.php?id=<?= (int) $p['id'] ?>" class="btn btn--small btn--solid">Lanjutkan Pembayaran</a>
              <?php elseif ($p['status_pembayaran'] === 'lunas'): ?>
                <a href="invoice.php?id=<?= (int) $p['id'] ?>" class="btn btn--small btn--solid">Lihat Invoice</a>
              <?php else: ?>
                <a href="konfirmasi.php?id=<?= (int) $p['id'] ?>" class="btn btn--small btn--ghost">Lihat Detail</a>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>
