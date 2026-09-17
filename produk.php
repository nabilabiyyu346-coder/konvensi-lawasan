<?php
require_once 'config/database.php';
require_once 'includes/helpers.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT p.*, k.nama AS kategori_nama
    FROM produk p
    JOIN kategori k ON k.id = p.kategori_id
    WHERE p.id = :id
");
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch();

if (!$produk) {
    http_response_code(404);
    $pageTitle = 'Produk tidak ditemukan';
    require_once 'includes/header.php';
    echo '<main><section class="section"><p class="empty-state">Produk tidak ditemukan. <a href="katalog.php">Kembali ke katalog</a>.</p></section></main>';
    require_once 'includes/footer.php';
    exit;
}

$pageTitle = $produk['nama'];
require_once 'includes/header.php';
?>

<main>
  <section class="section">
    <a href="katalog.php" class="back-link">&larr; Kembali ke katalog</a>

    <div class="detail">
      <div class="detail__media">
        <img src="<?= gambarProduk($produk['gambar']) ?>" alt="Motif <?= htmlspecialchars($produk['motif']) ?>">
        <span><?= htmlspecialchars($produk['motif']) ?></span>
      </div>

      <div class="detail__body">
        <p class="detail__kategori"><?= htmlspecialchars($produk['kategori_nama']) ?> · Motif <?= htmlspecialchars($produk['motif']) ?></p>
        <h1><?= htmlspecialchars($produk['nama']) ?></h1>
        <p class="detail__harga"><?= rupiah($produk['harga']) ?></p>
        <p class="detail__desc"><?= nl2br(htmlspecialchars($produk['deskripsi'])) ?></p>
        <p class="detail__stok">
          <?= $produk['stok'] > 0
              ? 'Stok tersedia: ' . (int)$produk['stok'] . ' pcs'
              : 'Stok habis — belum bisa ditambahkan ke keranjang' ?>
        </p>

        <form class="order-form" method="post" action="keranjang_aksi.php">
          <input type="hidden" name="aksi" value="tambah">
          <input type="hidden" name="produk_id" value="<?= $produk['id'] ?>">
          <input type="hidden" name="kembali_ke" value="produk.php?id=<?= $produk['id'] ?>">

          <label for="jumlah">Jumlah</label>
          <input type="number" id="jumlah" name="jumlah" min="1" max="<?= max(1, (int)$produk['stok']) ?>" value="1" <?= $produk['stok'] <= 0 ? 'disabled' : '' ?>>

          <button type="submit" class="btn btn--solid" <?= $produk['stok'] <= 0 ? 'disabled' : '' ?>>
            <?= $produk['stok'] > 0 ? 'Tambah ke Keranjang' : 'Stok Habis' ?>
          </button>
        </form>
      </div>
    </div>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>
