<?php
require_once 'config/database.php';
require_once 'includes/helpers.php';
require_once 'includes/session.php';
require_once 'includes/customer_auth.php';

wajibLoginPelanggan('checkout.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

$cartCheck = getCartItems($pdo);
if (empty($cartCheck['items'])) {
    header('Location: keranjang.php');
    exit;
}

$nama    = trim($_POST['nama_pemesan'] ?? '');
$email   = trim($_POST['email'] ?? '');
$telepon = trim($_POST['telepon'] ?? '');
$alamat  = trim($_POST['alamat'] ?? '');
$catatan = trim($_POST['catatan'] ?? '');
$metode  = $_POST['metode_pembayaran'] ?? 'transfer_bank';
if (!in_array($metode, ['transfer_bank', 'cod'], true)) {
    $metode = 'transfer_bank';
}

$errors = [];
if ($nama === '')                                $errors[] = 'Nama wajib diisi.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))  $errors[] = 'Email tidak valid.';
if ($telepon === '')                             $errors[] = 'Nomor WhatsApp wajib diisi.';
if ($alamat === '')                              $errors[] = 'Alamat wajib diisi.';

if (!empty($errors)) {
    $query = http_build_query([
        'error'        => implode(' ', $errors),
        'nama_pemesan' => $nama,
        'email'        => $email,
        'telepon'      => $telepon,
        'alamat'       => $alamat,
    ]);
    header('Location: checkout.php?' . $query);
    exit;
}

// Data produk sudah diambil & divalidasi lewat getCartItems() di atas
$cartItems = $cartCheck['items'];

try {
    $pdo->beginTransaction();

    $total = 0;
    $itemsToInsert = [];

    foreach ($cartItems as $entry) {
        $p = $entry['produk'];
        $jumlah = $entry['jumlah'];
        $subtotal = $p['harga'] * $jumlah;
        $total += $subtotal;

        $itemsToInsert[] = [
            'produk_id'    => $p['id'],
            'nama_produk'  => $p['nama'],
            'harga_satuan' => $p['harga'],
            'jumlah'       => $jumlah,
            'subtotal'     => $subtotal,
        ];
    }

    $stmtPesanan = $pdo->prepare("
        INSERT INTO pesanan (pelanggan_id, nama_pemesan, email, telepon, alamat, catatan, total, metode_pembayaran, status_pembayaran)
        VALUES (:pelanggan_id, :nama, :email, :telepon, :alamat, :catatan, :total, :metode, :status_pembayaran)
    ");
    $stmtPesanan->execute([
        ':pelanggan_id'      => $_SESSION['pelanggan_id'],
        ':nama'              => $nama,
        ':email'             => $email,
        ':telepon'           => $telepon,
        ':alamat'            => $alamat,
        ':catatan'           => $catatan,
        ':total'             => $total,
        ':metode'            => $metode,
        ':status_pembayaran' => 'menunggu',
    ]);
    $pesananId = $pdo->lastInsertId();

    $stmtItem = $pdo->prepare("
        INSERT INTO pesanan_item (pesanan_id, produk_id, nama_produk, harga_satuan, jumlah, subtotal)
        VALUES (:pesanan_id, :produk_id, :nama_produk, :harga_satuan, :jumlah, :subtotal)
    ");
    $stmtStok = $pdo->prepare("
        UPDATE produk SET stok = GREATEST(0, stok - :jumlah) WHERE id = :id
    ");

    foreach ($itemsToInsert as $item) {
        $stmtItem->execute([
            ':pesanan_id'   => $pesananId,
            ':produk_id'    => $item['produk_id'],
            ':nama_produk'  => $item['nama_produk'],
            ':harga_satuan' => $item['harga_satuan'],
            ':jumlah'       => $item['jumlah'],
            ':subtotal'     => $item['subtotal'],
        ]);
        $stmtStok->execute([':jumlah' => $item['jumlah'], ':id' => $item['produk_id']]);
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    die('Gagal memproses pesanan. Silakan coba lagi. Detail: ' . $e->getMessage());
}

// Keranjang sudah jadi pesanan, kosongkan
unset($_SESSION['cart']);

header('Location: konfirmasi.php?id=' . $pesananId);
exit;
