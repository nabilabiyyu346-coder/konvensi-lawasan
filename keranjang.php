<?php
require_once 'config/database.php';
require_once 'includes/helpers.php';
require_once 'includes/session.php';

$pageTitle = 'Keranjang';
$cart = getCartItems($pdo);
$items = $cart['items'];
$total = $cart['total'];

require_once 'includes/header.php';
?>

<main>
  <section class="section">
    <div class="section__head">
      <p class="section__index">Keranjang</p>
      <h2>Keranjang belanja</h2>
    </div>

    <?php if (empty($items)): ?>
      <p class="empty-state">Keranjang kamu masih kosong. <a href="katalog.php">Lihat katalog produk</a>.</p>
    <?php else: ?>
      <div class="cart">
        <div class="cart__list">
          <?php foreach ($items as $item): $p = $item['produk']; ?>
            <div class="cart__row">
              <img src="<?= gambarProduk($p['gambar']) ?>" alt="Motif <?= htmlspecialchars($p['motif']) ?>" class="cart__thumb">
              <div class="cart__info">
                <h3><a href="produk.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?></a></h3>
                <p class="cart__harga"><?= rupiah($p['harga']) ?> / pcs</p>
              </div>

              <form method="post" action="keranjang_aksi.php" class="cart__qty-form">
                <input type="hidden" name="aksi" value="update">
                <input type="hidden" name="produk_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="kembali_ke" value="keranjang.php">
                <label class="sr-only" for="jumlah-<?= $p['id'] ?>">Jumlah</label>
                <input type="number" id="jumlah-<?= $p['id'] ?>" name="jumlah" min="1" max="<?= max(1, (int)$p['stok']) ?>" value="<?= $item['jumlah'] ?>">
                <button type="submit" class="btn btn--ghost btn--small">Ubah</button>
              </form>

              <p class="cart__subtotal"><?= rupiah($item['subtotal']) ?></p>

              <form method="post" action="keranjang_aksi.php" class="cart__remove-form">
                <input type="hidden" name="aksi" value="hapus">
                <input type="hidden" name="produk_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="kembali_ke" value="keranjang.php">
                <button type="submit" class="link-remove" aria-label="Hapus dari keranjang">Hapus</button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>

        <aside class="cart__summary">
          <h3>Ringkasan</h3>
          <div class="cart__summary-row">
            <span>Total</span>
            <strong><?= rupiah($total) ?></strong>
          </div>
          <a href="checkout.php" class="btn btn--solid btn--full">Lanjut ke Checkout</a>
          <a href="katalog.php" class="back-link">&larr; Tambah produk lain</a>
        </aside>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>
