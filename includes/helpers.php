<?php
/**
 * Fungsi bantu yang dipakai di beberapa halaman.
 */

function rupiah(float $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function slugify(string $text): string
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $text), '-'));
}

/**
 * Path gambar produk. Kalau kolom `gambar` di database kosong,
 * jatuhkan ke gambar generik supaya tidak ada <img> yang rusak.
 */
function gambarProduk(?string $gambar): string
{
    $file = $gambar ?: 'default.svg';
    return 'assets/images/produk/' . $file;
}

/**
 * Ambil isi keranjang dari session, DIVALIDASI ulang terhadap
 * database. Kalau ada produk_id di keranjang yang ternyata sudah
 * dihapus (atau tidak valid), entri itu otomatis dibuang dari
 * session supaya badge jumlah keranjang & halaman keranjang selalu
 * sinkron satu sama lain.
 *
 * @return array{items: array, total: float}
 */
function getCartItems(PDO $pdo): array
{
    if (empty($_SESSION['cart'])) {
        return ['items' => [], 'total' => 0];
    }

    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll();

    $validIds = array_column($rows, 'id');

    // Buang entri keranjang yang produknya sudah tidak ada di database
    foreach (array_keys($_SESSION['cart']) as $id) {
        if (!in_array((int) $id, $validIds, true)) {
            unset($_SESSION['cart'][$id]);
        }
    }

    $items = [];
    $total = 0;
    foreach ($rows as $p) {
        $jumlah = (int) ($_SESSION['cart'][$p['id']] ?? 0);
        if ($jumlah <= 0) {
            continue;
        }
        $subtotal = $p['harga'] * $jumlah;
        $total += $subtotal;
        $items[] = ['produk' => $p, 'jumlah' => $jumlah, 'subtotal' => $subtotal];
    }

    return ['items' => $items, 'total' => $total];
}

/**
 * Jumlah item di keranjang (untuk badge di nav), sudah divalidasi.
 */
function jumlahKeranjang(PDO $pdo): int
{
    $cart = getCartItems($pdo);
    return array_sum(array_column($cart['items'], 'jumlah'));
}
