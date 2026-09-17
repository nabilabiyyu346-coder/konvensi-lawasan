<?php
require_once 'includes/auth.php';
require_once '../config/database.php';
require_once '../includes/helpers.php';

$id = (int) ($_GET['id'] ?? 0);
$produk = [
    'id' => 0, 'kategori_id' => '', 'nama' => '', 'motif' => '',
    'harga' => '', 'stok' => '', 'deskripsi' => '', 'gambar' => '',
];

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $found = $stmt->fetch();
    if ($found) {
        $produk = $found;
    }
}

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY nama")->fetchAll();
$pageTitle = $id > 0 ? 'Edit Produk' : 'Tambah Produk';
$error = $_GET['error'] ?? '';

require_once 'includes/admin_header.php';
?>

<main class="admin-main admin-main--narrow">
  <a href="produk.php" class="back-link">&larr; Kembali ke daftar produk</a>
  <h1><?= $id > 0 ? 'Edit Produk' : 'Tambah Produk' ?></h1>

  <?php if ($error): ?>
    <p class="form-error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form class="admin-form" method="post" action="produk_simpan.php">
    <input type="hidden" name="id" value="<?= $produk['id'] ?>">

    <label for="nama">Nama produk</label>
    <input type="text" id="nama" name="nama" required value="<?= htmlspecialchars($produk['nama']) ?>">

    <div class="admin-form__row">
      <div>
        <label for="kategori_id">Kategori</label>
        <select id="kategori_id" name="kategori_id" required>
          <option value="">— Pilih kategori —</option>
          <?php foreach ($kategoriList as $k): ?>
            <option value="<?= $k['id'] ?>" <?= (int)$produk['kategori_id'] === (int)$k['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($k['nama']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="motif">Motif</label>
        <input type="text" id="motif" name="motif" required value="<?= htmlspecialchars($produk['motif']) ?>">
      </div>
    </div>

    <div class="admin-form__row">
      <div>
        <label for="harga">Harga (Rp)</label>
        <input type="number" id="harga" name="harga" min="0" step="1000" required value="<?= htmlspecialchars($produk['harga']) ?>">
      </div>
      <div>
        <label for="stok">Stok</label>
        <input type="number" id="stok" name="stok" min="0" required value="<?= htmlspecialchars($produk['stok']) ?>">
      </div>
    </div>

    <label for="gambar">Nama file gambar</label>
    <input type="text" id="gambar" name="gambar" placeholder="contoh: kawung.jpg" value="<?= htmlspecialchars($produk['gambar']) ?>">
    <p class="admin-form__hint">File harus sudah ada di folder <code>assets/images/produk/</code>. Kosongkan untuk pakai gambar default.</p>

    <label for="deskripsi">Deskripsi</label>
    <textarea id="deskripsi" name="deskripsi" rows="4" required><?= htmlspecialchars($produk['deskripsi']) ?></textarea>

    <button type="submit" class="btn btn--solid">Simpan Produk</button>
  </form>
</main>

<?php require_once 'includes/admin_footer.php'; ?>
