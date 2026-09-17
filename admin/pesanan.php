<?php
require_once 'includes/auth.php';
require_once '../config/database.php';
require_once '../includes/helpers.php';

$pageTitle = 'Transaksi';

$filterStatus = $_GET['status'] ?? '';
$sql = "SELECT * FROM pesanan";
$params = [];
if (in_array($filterStatus, ['menunggu','menunggu_verifikasi','lunas','ditolak'], true)) {
    $sql .= " WHERE status_pembayaran = :status";
    $params[':status'] = $filterStatus;
}
$sql .= " ORDER BY dibuat_pada DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pesananList = $stmt->fetchAll();

$sukses = $_GET['sukses'] ?? '';

require_once 'includes/admin_header.php';
?>

<main class="admin-main">
  <div class="admin-main__head">
    <h1>Transaksi</h1>
    <a href="laporan.php" class="btn btn--ghost">Lihat Laporan &amp; Export &rarr;</a>
  </div>

  <?php if ($sukses): ?><p class="form-success"><?= htmlspecialchars($sukses) ?></p><?php endif; ?>

  <div class="filter-chips">
    <a href="pesanan.php" class="chip-link <?= $filterStatus === '' ? 'is-active' : '' ?>">Semua</a>
    <a href="pesanan.php?status=menunggu" class="chip-link <?= $filterStatus === 'menunggu' ? 'is-active' : '' ?>">Menunggu Bayar</a>
    <a href="pesanan.php?status=menunggu_verifikasi" class="chip-link <?= $filterStatus === 'menunggu_verifikasi' ? 'is-active' : '' ?>">Perlu Verifikasi</a>
    <a href="pesanan.php?status=lunas" class="chip-link <?= $filterStatus === 'lunas' ? 'is-active' : '' ?>">Lunas</a>
    <a href="pesanan.php?status=ditolak" class="chip-link <?= $filterStatus === 'ditolak' ? 'is-active' : '' ?>">Ditolak</a>
  </div>

  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th><th>Pemesan</th><th>Total</th><th>Metode</th><th>Bukti</th><th>Status Bayar</th><th>Tanggal</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pesananList as $p): ?>
          <tr>
            <td>#<?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['nama_pemesan']) ?></td>
            <td><?= rupiah($p['total']) ?></td>
            <td><?= $p['metode_pembayaran'] === 'cod' ? 'COD' : 'Transfer Bank' ?></td>
            <td>
              <?php if ($p['bukti_bayar']): ?>
                <a href="../assets/uploads/bukti/<?= htmlspecialchars($p['bukti_bayar']) ?>" target="_blank">Lihat</a>
              <?php else: ?>
                <span class="empty-state" style="padding:0;">—</span>
              <?php endif; ?>
            </td>
            <td><span class="badge-status badge-status--<?= htmlspecialchars($p['status_pembayaran']) ?>"><?= str_replace('_', ' ', $p['status_pembayaran']) ?></span></td>
            <td><?= date('d M Y H:i', strtotime($p['dibuat_pada'])) ?></td>
            <td class="admin-table__actions">
              <?php if ($p['status_pembayaran'] === 'menunggu_verifikasi'): ?>
                <form method="post" action="pesanan_verifikasi.php" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <input type="hidden" name="aksi" value="lunas">
                  <button type="submit" class="link-verify">Verifikasi</button>
                </form>
                <form method="post" action="pesanan_verifikasi.php" style="display:inline;" onsubmit="return confirm('Tolak bukti pembayaran ini?');">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <input type="hidden" name="aksi" value="ditolak">
                  <button type="submit" class="link-remove">Tolak</button>
                </form>
              <?php elseif ($p['metode_pembayaran'] === 'cod' && $p['status_pembayaran'] !== 'lunas'): ?>
                <form method="post" action="pesanan_verifikasi.php" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <input type="hidden" name="aksi" value="lunas">
                  <button type="submit" class="link-verify">Tandai Lunas</button>
                </form>
              <?php else: ?>
                <span class="empty-state" style="padding:0;">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($pesananList)): ?>
          <tr><td colspan="8" class="empty-state">Tidak ada transaksi.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<?php require_once 'includes/admin_footer.php'; ?>
