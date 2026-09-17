<?php
require_once 'includes/auth.php';
require_once '../config/database.php';
require_once '../includes/helpers.php';

$pageTitle = 'Kelola Produk';

$produkList = $pdo->query("
    SELECT p.*, k.nama AS kategori_nama
    FROM produk p
    JOIN kategori k ON k.id = p.kategori_id
    ORDER BY p.id DESC
")->fetchAll();

$sukses = $_GET['sukses'] ?? '';

require_once 'includes/admin_header.php';
?>

<main class="admin-main">
  <div class="admin-main__head">
    <h1>Kelola Produk</h1>
    <a href="produk_form.php" class="btn btn--solid">+ Tambah Produk</a>
  </div>

  <?php if ($sukses): ?>
    <p class="form-success"><?= htmlspecialchars($sukses) ?></p>
  <?php endif; ?>

  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Gambar</th>
          <th>Nama</th>
          <th>Kategori</th>
          <th>Harga</th>
          <th>Stok</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($produkList as $p): ?>
          <tr>
            <td><img src="<?= gambarProduk($p['gambar']) ?>" alt="" class="admin-table__thumb"></td>
            <td><?= htmlspecialchars($p['nama']) ?></td>
            <td><?= htmlspecialchars($p['kategori_nama']) ?></td>
            <td><?= rupiah($p['harga']) ?></td>
            <td><?= (int) $p['stok'] ?></td>
            <td class="admin-table__actions">
              <a href="produk_form.php?id=<?= $p['id'] ?>">Edit</a>
              <form method="post" action="produk_hapus.php" onsubmit="return confirm('Hapus produk ini? Tindakan tidak bisa dibatalkan.');">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="link-remove">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($produkList)): ?>
          <tr><td colspan="6" class="empty-state">Belum ada produk. Klik "+ Tambah Produk" untuk mulai.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<?php require_once 'includes/admin_footer.php'; ?>
