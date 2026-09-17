<?php
require_once 'config/database.php';
require_once 'includes/helpers.php';

$pageTitle = 'Beranda';

$unggulan = $pdo->query("
    SELECT p.*, k.nama AS kategori_nama
    FROM produk p
    JOIN kategori k ON k.id = p.kategori_id
    ORDER BY RAND()
    LIMIT 4
")->fetchAll();

// Motif unik untuk swatch kain interaktif di hero
$swatchRows = $pdo->query("
    SELECT motif, MIN(gambar) AS gambar
    FROM produk
    GROUP BY motif
    ORDER BY motif
")->fetchAll();

require_once 'includes/header.php';
?>

<header class="hero">
  <div class="hero__pattern" aria-hidden="true"></div>
  <div class="hero__text">
    <p class="hero__eyebrow">Batik tulis &amp; cap · dikerjakan tangan</p>
    <h1>Batik yang menyimpan<br>cerita, bukan sekadar motif.</h1>
    <p class="hero__lede">Kain, baju, dan aksesoris batik dari perajin lokal — dari canting dan malam, sampai ke lemari kamu.</p>
    <a href="katalog.php" class="btn btn--solid">Lihat katalog</a>
  </div>

  <div class="hero__swatch">
    <div class="kain-card" id="kainCard">
      <img src="<?= gambarProduk($swatchRows[0]['gambar'] ?? null) ?>" alt="Kain batik motif <?= htmlspecialchars($swatchRows[0]['motif'] ?? '') ?>" id="kainImg" class="kain-card__img">
      <div class="kain-card__sheen"></div>
      <span class="kain-card__label" id="kainLabel"><?= htmlspecialchars($swatchRows[0]['motif'] ?? '') ?></span>
    </div>
    <div class="kain-dots" id="kainDots">
      <?php foreach ($swatchRows as $i => $s): ?>
        <button type="button"
                class="kain-dot <?= $i === 0 ? 'is-active' : '' ?>"
                data-src="<?= gambarProduk($s['gambar']) ?>"
                data-motif="<?= htmlspecialchars($s['motif']) ?>"
                aria-label="Lihat motif <?= htmlspecialchars($s['motif']) ?>"></button>
      <?php endforeach; ?>
    </div>
    <p class="kain-hint">Gerakkan kursor di atas kain, atau pilih motif lain di bawah</p>
  </div>
</header>

<main>
  <section class="section">
    <div class="section__head">
      <p class="section__index">Pilihan</p>
      <h2>Produk unggulan</h2>
    </div>
    <div class="product-grid">
      <?php foreach ($unggulan as $p): ?>
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
  </section>

  <section class="section section--muted">
    <div class="section__head">
      <p class="section__index">Proses</p>
      <h2>Dari malam ke motif</h2>
    </div>
    <div class="steps">
      <div class="steps__item">
        <p class="steps__num">01</p>
        <h3>Nyanting</h3>
        <p>Motif digambar dengan malam panas memakai canting, satu goresan pada satu waktu.</p>
      </div>
      <div class="steps__item">
        <p class="steps__num">02</p>
        <h3>Pewarnaan</h3>
        <p>Kain dicelup berulang kali dengan pewarna alami maupun sintetis sesuai motif yang diinginkan.</p>
      </div>
      <div class="steps__item">
        <p class="steps__num">03</p>
        <h3>Pelorodan</h3>
        <p>Malam direbus lepas dari kain, memunculkan motif yang sudah terbentuk di bawahnya.</p>
      </div>
    </div>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>
