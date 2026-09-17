<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
$kategori = ['id' => 0, 'nama' => ''];

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM kategori WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $found = $stmt->fetch();
    if ($found) {
        $kategori = $found;
    }
}

$pageTitle = $id > 0 ? 'Edit Kategori' : 'Tambah Kategori';
$error = $_GET['error'] ?? '';

require_once 'includes/admin_header.php';
?>

<main class="admin-main admin-main--narrow">
  <a href="kategori.php" class="back-link">&larr; Kembali ke daftar kategori</a>
  <h1><?= $id > 0 ? 'Edit Kategori' : 'Tambah Kategori' ?></h1>

  <?php if ($error): ?>
    <p class="form-error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form class="admin-form" method="post" action="kategori_simpan.php">
    <input type="hidden" name="id" value="<?= $kategori['id'] ?>">

    <label for="nama">Nama kategori</label>
    <input type="text" id="nama" name="nama" required value="<?= htmlspecialchars($kategori['nama']) ?>">
    <p class="admin-form__hint">Slug URL dibuat otomatis dari nama (mis. "Kain Batik" &rarr; "kain-batik").</p>

    <button type="submit" class="btn btn--solid">Simpan Kategori</button>
  </form>
</main>

<?php require_once 'includes/admin_footer.php'; ?>
