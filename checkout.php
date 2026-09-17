<?php
require_once 'config/database.php';
require_once 'includes/helpers.php';
require_once 'includes/session.php';
require_once 'includes/customer_auth.php';

wajibLoginPelanggan('checkout.php');

$pageTitle = 'Checkout';

// Ambil data akun untuk prefill form (nama, email, telepon, alamat)
$stmtPelanggan = $pdo->prepare("SELECT * FROM pelanggan WHERE id = :id");
$stmtPelanggan->execute([':id' => $_SESSION['pelanggan_id']]);
$pelanggan = $stmtPelanggan->fetch();

$cart = getCartItems($pdo);
$items = $cart['items'];
$total = $cart['total'];

if (empty($items)) {
    header('Location: keranjang.php');
    exit;
}

$errors = $_GET['error'] ?? '';

require_once 'includes/header.php';
?>

<main>
  <section class="section">
    <a href="keranjang.php" class="back-link">&larr; Kembali ke keranjang</a>

    <div class="section__head">
      <p class="section__index">Checkout</p>
      <h2>Selesaikan pesanan</h2>
    </div>

    <?php if ($errors): ?>
      <p class="form-error"><?= htmlspecialchars($errors) ?></p>
    <?php endif; ?>

    <div class="checkout">
      <form class="checkout__form" method="post" action="proses_checkout.php" novalidate>
        <label for="nama_pemesan">Nama lengkap</label>
        <input type="text" id="nama_pemesan" name="nama_pemesan" required
               value="<?= htmlspecialchars($_GET['nama_pemesan'] ?? $pelanggan['nama'] ?? '') ?>">

        <div class="checkout__row">
          <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($_GET['email'] ?? $pelanggan['email'] ?? '') ?>">
          </div>
          <div>
            <label for="telepon">No. WhatsApp</label>
            <input type="tel" id="telepon" name="telepon" required
                   value="<?= htmlspecialchars($_GET['telepon'] ?? $pelanggan['telepon'] ?? '') ?>">
          </div>
        </div>

        <label for="alamat">Alamat pengiriman</label>
        <textarea id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($_GET['alamat'] ?? $pelanggan['alamat'] ?? '') ?></textarea>

        <label>Metode Pembayaran</label>
        <div class="payment-options">
          <label class="payment-option">
            <input type="radio" name="metode_pembayaran" value="transfer_bank" checked>
            <span>
              <strong>Transfer Bank</strong>
              <small>Bayar via transfer, unggah bukti setelah pesan</small>
            </span>
          </label>
          <label class="payment-option">
            <input type="radio" name="metode_pembayaran" value="cod">
            <span>
              <strong>Bayar di Tempat (COD)</strong>
              <small>Bayar tunai saat barang diterima</small>
            </span>
          </label>
        </div>

        <label for="catatan">Catatan (opsional)</label>
        <textarea id="catatan" name="catatan" rows="2" placeholder="Misal: ukuran, warna, waktu pengiriman"></textarea>

        <button type="submit" class="btn btn--solid">Buat pesanan</button>
      </form>

      <aside class="cart__summary">
        <h3>Ringkasan pesanan</h3>
        <ul class="checkout__items">
          <?php foreach ($items as $item): $p = $item['produk']; ?>
            <li>
              <span><?= htmlspecialchars($p['nama']) ?> &times; <?= $item['jumlah'] ?></span>
              <span><?= rupiah($item['subtotal']) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
        <div class="cart__summary-row">
          <span>Total</span>
          <strong><?= rupiah($total) ?></strong>
        </div>
      </aside>
    </div>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>
