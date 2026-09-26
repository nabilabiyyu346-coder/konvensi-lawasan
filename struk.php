<?php
require_once 'config/database.php';
require_once 'includes/helpers.php';
require_once 'includes/session.php';
require_once 'includes/customer_auth.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM pesanan WHERE id = :id");
$stmt->execute([':id' => $id]);
$pesanan = $stmt->fetch();

// Keamanan: hanya pemilik pesanan yang boleh lihat/cetak struknya
if (!$pesanan || !pelangganLoggedIn() || (int) $pesanan['pelanggan_id'] !== (int) $_SESSION['pelanggan_id']) {
    header('Location: riwayat.php');
    exit;
}

$stmtItems = $pdo->prepare("SELECT * FROM pesanan_item WHERE pesanan_id = :id");
$stmtItems->execute([':id' => $id]);
$items = $stmtItems->fetchAll();

$statusBayarLabel = [
    'menunggu'            => 'Menunggu pembayaran',
    'menunggu_verifikasi' => 'Menunggu verifikasi',
    'lunas'               => 'Lunas',
    'ditolak'             => 'Ditolak',
][$pesanan['status_pembayaran']] ?? $pesanan['status_pembayaran'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="light">
<title>Struk Pesanan #<?= $pesanan['id'] ?> — Batik Lawasan</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
<style>
  .receipt-toolbar{ max-width:640px; margin:1.5rem auto 0; padding:0 1.5rem; display:flex; justify-content:space-between; align-items:center; }
  .receipt{
    max-width:640px; margin:1.5rem auto 3rem; background:var(--white); border:1px solid var(--paper-line);
    border-radius:8px; padding:2.2rem; font-size:.94rem;
  }
  .receipt__head{ text-align:center; margin-bottom:1.6rem; padding-bottom:1.4rem; border-bottom:2px dashed var(--paper-line); }
  .receipt__head h1{ font-size:1.3rem; margin-bottom:.2rem; }
  .receipt__head p{ color:var(--muted); font-size:.82rem; margin:0; }
  .receipt__meta{ display:grid; grid-template-columns:1fr 1fr; gap:.6rem 1rem; margin-bottom:1.4rem; font-size:.88rem; }
  .receipt__meta dt{ font-family:var(--font-mono); font-size:.72rem; color:var(--muted); }
  .receipt__meta dd{ margin:.15rem 0 0; }
  .receipt table{ width:100%; border-collapse:collapse; margin-bottom:1.2rem; }
  .receipt th{ text-align:left; font-family:var(--font-mono); font-size:.72rem; color:var(--muted); padding:.5rem 0; border-bottom:1px solid var(--paper-line); }
  .receipt td{ padding:.55rem 0; border-bottom:1px solid var(--paper-line); font-size:.9rem; }
  .receipt td:last-child, .receipt th:last-child{ text-align:right; }
  .receipt__total{ display:flex; justify-content:space-between; padding-top:1rem; font-size:1.05rem; font-weight:600; }
  .receipt__total span:last-child{ color:var(--rust); }
  .receipt__footer{ text-align:center; margin-top:1.8rem; padding-top:1.2rem; border-top:2px dashed var(--paper-line); color:var(--muted); font-size:.82rem; }

  @media print{
    .nav, .receipt-toolbar, .footer{ display:none !important; }
    body{ background:#fff; }
    .receipt{ box-shadow:none; border:none; margin:0; max-width:none; }
  }
</style>
</head>
<body>

<div class="receipt-toolbar">
  <a href="riwayat.php" class="back-link">&larr; Kembali ke Riwayat</a>
  <button onclick="window.print()" class="btn btn--solid btn--small">Cetak / Simpan PDF</button>
</div>

<div class="receipt">
  <div class="receipt__head">
    <h1>Batik Lawasan</h1>
    <p>Struk Pembelian</p>
  </div>

  <dl class="receipt__meta">
    <div><dt>No. Pesanan</dt><dd>#<?= $pesanan['id'] ?></dd></div>
    <div><dt>Tanggal</dt><dd><?= date('d M Y H:i', strtotime($pesanan['dibuat_pada'])) ?></dd></div>
    <div><dt>Nama Pemesan</dt><dd><?= htmlspecialchars($pesanan['nama_pemesan']) ?></dd></div>
    <div><dt>Telepon</dt><dd><?= htmlspecialchars($pesanan['telepon']) ?></dd></div>
    <div><dt>Metode Bayar</dt><dd><?= $pesanan['metode_pembayaran'] === 'cod' ? 'COD' : 'Transfer Bank' ?></dd></div>
    <div><dt>Status Bayar</dt><dd><?= htmlspecialchars($statusBayarLabel) ?></dd></div>
  </dl>

  <table>
    <thead>
      <tr><th>Produk</th><th>Jml</th><th>Harga</th><th>Subtotal</th></tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['nama_produk']) ?></td>
          <td><?= $item['jumlah'] ?></td>
          <td><?= rupiah($item['harga_satuan']) ?></td>
          <td><?= rupiah($item['subtotal']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="receipt__total">
    <span>Total</span>
    <span><?= rupiah($pesanan['total']) ?></span>
  </div>

  <div class="receipt__footer">
    <p>Terima kasih telah berbelanja di Batik Lawasan.</p>
  </div>
</div>

</body>
</html>
