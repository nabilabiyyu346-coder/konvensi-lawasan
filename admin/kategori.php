<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'Kelola Kategori';

$kategoriList = $pdo->query("
    SELECT k.*, COUNT(p.id) AS jumlah_produk
    FROM kategori k
    LEFT JOIN produk p ON p.kategori_id = k.id
    GROUP BY k.id
    ORDER BY k.nama
")->fetchAll();

$sukses = $_GET['sukses'] ?? '';
$error  = $_GET['error'] ?? '';

require_once 'includes/admin_header.php';
?>

<main class="admin-main">
  <div class="admin-main__head">
    <h1>Kelola Kategori</h1>
    <a href="kategori_form.php" class="btn btn--solid">+ Tambah Kategori</a>
  </div>

  <?php if ($sukses): ?><p class="form-success"><?= htmlspecialchars($sukses) ?></p><?php endif; ?>
  <?php if ($error): ?><p class="form-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Slug</th>
          <th>Jumlah Produk</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($kategoriList as $k): ?>
          <tr>
            <td><?= htmlspecialchars($k['nama']) ?></td>
            <td><code><?= htmlspecialchars($k['slug']) ?></code></td>
            <td><?= (int) $k['jumlah_produk'] ?></td>
            <td class="admin-table__actions">
              <a href="kategori_form.php?id=<?= $k['id'] ?>">Edit</a>
              <form method="post" action="kategori_hapus.php" onsubmit="return confirm('Hapus kategori ini?');">
                <input type="hidden" name="id" value="<?= $k['id'] ?>">
                <button type="submit" class="link-remove">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($kategoriList)): ?>
          <tr><td colspan="4" class="empty-state">Belum ada kategori.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<?php require_once 'includes/admin_footer.php'; ?>
