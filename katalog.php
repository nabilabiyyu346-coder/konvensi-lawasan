<?php
require_once 'config/database.php';
require_once 'includes/helpers.php';

$pageTitle = 'Katalog';

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY id")->fetchAll();

$slug  = $_GET['kategori'] ?? '';
$cari  = trim($_GET['cari'] ?? '');

$sql = "
    SELECT p.*, k.nama AS kategori_nama, k.slug AS kategori_slug
    FROM produk p
    JOIN kategori k ON k.id = p.kategori_id
    WHERE 1=1
";
$params = [];

if ($slug !== '') {
    $sql .= " AND k.slug = :slug";
    $params[':slug'] = $slug;
}
if ($cari !== '') {
    $sql .= " AND (p.nama LIKE :cari OR p.motif LIKE :cari)";
    $params[':cari'] = '%' . $cari . '%';
}
$sql .= " ORDER BY p.nama";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produkList = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<main>
  <section class="section section--tight">
    <div class="section__head">
      <p class="section__index">Katalog</p>
      <h2>Semua produk</h2>
    </div>

    <form class="filter-bar" method="get">
      <div class="filter-bar__cats">
        <a href="katalog.php" class="chip-link <?= $slug === '' ? 'is-active' : '' ?>">Semua</a>
        <?php foreach ($kategoriList as $k): ?>
          <a href="katalog.php?kategori=<?= urlencode($k['slug']) ?>"
             class="chip-link <?= $slug === $k['slug'] ? 'is-active' : '' ?>">
            <?= htmlspecialchars($k['nama']) ?>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="filter-bar__search">
        <input type="hidden" name="kategori" value="<?= htmlspecialchars($slug) ?>">
        <input type="text" name="cari" placeholder="Cari nama atau motif…" value="<?= htmlspecialchars($cari) ?>">
        <button type="submit" class="btn btn--ghost">Cari</button>
      </div>
    </form>

    <?php if (empty($produkList)): ?>
      <p class="empty-state">Tidak ada produk yang cocok. Coba kata kunci atau kategori lain.</p>
    <?php else: ?>
      <div class="product-grid">
        <?php foreach ($produkList as $p): ?>
          <a href="produk.php?id=<?= $p['id'] ?>" class="product-card">
            <div class="product-card__media">
              <img src="<?= gambarProduk($p['gambar']) ?>" alt="Motif <?= htmlspecialchars($p['motif']) ?>" loading="lazy">
              <span><?= htmlspecialchars($p['motif']) ?></span>
            </div>
            <p class="product-card__kategori"><?= htmlspecialchars($p['kategori_nama']) ?></p>
            <h3><?= htmlspecialchars($p['nama']) ?></h3>
            <p class="product-card__harga"><?= rupiah($p['harga']) ?></p>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>
