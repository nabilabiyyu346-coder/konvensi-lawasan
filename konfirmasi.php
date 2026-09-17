<?php
require_once 'config/database.php';
require_once 'includes/helpers.php';
require_once 'includes/session.php';
require_once 'includes/customer_auth.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM pesanan WHERE id = :id");
$stmt->execute([':id' => $id]);
$pesanan = $stmt->fetch();

if (!$pesanan) {
    header('Location: index.php');
    exit;
}

$stmtItems = $pdo->prepare("SELECT * FROM pesanan_item WHERE pesanan_id = :id");
$stmtItems->execute([':id' => $id]);
$items = $stmtItems->fetchAll();

$pageTitle = 'Pesanan diterima';
require_once 'includes/header.php';
?>

<main>
  <section class="section confirmation">
    <p class="section__index">Terkirim</p>
    <h1>Terima kasih, <?= htmlspecialchars($pesanan['nama_pemesan']) ?>.</h1>
    <p>Pesanan #<?= htmlspecialchars($pesanan['id']) ?> sudah kami terima. Tim kami akan menghubungi kamu lewat WhatsApp ke <?= htmlspecialchars($pesanan['telepon']) ?> untuk konfirmasi pengiriman.</p>

    <ul class="checkout__items">
      <?php foreach ($items as $item): ?>
        <li>
          <span><?= htmlspecialchars($item['nama_produk']) ?> &times; <?= $item['jumlah'] ?></span>
          <span><?= rupiah($item['subtotal']) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="cart__summary-row">
      <span>Total</span>
      <strong><?= rupiah($pesanan['total']) ?></strong>
    </div>

    <?php if ($error = ($_GET['error'] ?? '')): ?>
      <p class="form-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($pesanan['metode_pembayaran'] === 'cod'): ?>
      <div class="payment-panel">
        <p class="payment-panel__status badge-status badge-status--baru">Bayar di tempat</p>
        <p>Siapkan uang pas sejumlah <strong><?= rupiah($pesanan['total']) ?></strong> saat kurir mengantarkan pesananmu.</p>
      </div>

    <?php elseif ($pesanan['status_pembayaran'] === 'lunas'): ?>
      <div class="payment-panel payment-panel--success">
        <p class="payment-panel__status badge-status badge-status--selesai">Pembayaran lunas</p>
        <p>Pembayaranmu sudah kami verifikasi. Pesanan akan segera diproses.</p>
      </div>

    <?php elseif ($pesanan['status_pembayaran'] === 'menunggu_verifikasi'): ?>
      <div class="payment-panel">
        <p class="payment-panel__status badge-status badge-status--diproses">Menunggu verifikasi</p>
        <p>Bukti transfer sudah kami terima dan sedang diperiksa tim kami. Biasanya diverifikasi dalam 1x24 jam.</p>
        <?php if ($pesanan['bukti_bayar']): ?>
          <img src="assets/uploads/bukti/<?= htmlspecialchars($pesanan['bukti_bayar']) ?>" alt="Bukti transfer" class="payment-panel__proof">
        <?php endif; ?>
      </div>

    <?php elseif ($pesanan['status_pembayaran'] === 'ditolak'): ?>
      <div class="payment-panel payment-panel--error">
        <p class="payment-panel__status badge-status badge-status--ditolak">Bukti ditolak</p>
        <p>Bukti transfer sebelumnya belum bisa kami verifikasi (kemungkinan tidak jelas/tidak sesuai). Silakan unggah ulang.</p>
        <?php require 'includes/upload_bukti_form.php'; ?>
      </div>

    <?php else: /* menunggu */ ?>
      <div class="payment-panel">
        <p class="payment-panel__status badge-status badge-status--baru">Menunggu pembayaran</p>
        <div class="payment-panel__bank">
          <p><strong>Bank Central Batik</strong> — 1234 5678 9099</p>
          <p>a.n. Batik Nusantara</p>
        </div>
        <p>Transfer sejumlah <strong><?= rupiah($pesanan['total']) ?></strong>, lalu unggah bukti transfer di bawah ini.</p>
        <?php require 'includes/upload_bukti_form.php'; ?>
      </div>
    <?php endif; ?>

    <a href="katalog.php" class="btn btn--ghost" style="margin-top:2rem;">Lihat produk lainnya</a>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>
