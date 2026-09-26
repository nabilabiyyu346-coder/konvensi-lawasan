<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

$mulai      = $_GET['mulai']       ?? date('Y-m-01');
$selesai    = $_GET['selesai']     ?? date('Y-m-d');
$kategoriId = $_GET['kategori_id'] ?? '';

$sql = "
    SELECT DISTINCT p.* FROM pesanan p
    JOIN pesanan_item pi ON pi.pesanan_id = p.id
    LEFT JOIN produk pr ON pr.id = pi.produk_id
    WHERE DATE(p.dibuat_pada) BETWEEN :mulai AND :selesai
";
$params = [':mulai' => $mulai, ':selesai' => $selesai];
if ($kategoriId !== '') {
    $sql .= " AND pr.kategori_id = :kategori_id";
    $params[':kategori_id'] = $kategoriId;
}
$sql .= " ORDER BY p.dibuat_pada ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pesananList = $stmt->fetchAll();

$filename = 'laporan-penjualan_' . $mulai . '_sampai_' . $selesai . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');

// BOM supaya karakter dibaca benar kalau dibuka di Excel
fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($out, [
    'ID Pesanan', 'Nama Pemesan', 'Email', 'Telepon', 'Alamat',
    'Total (Rp)', 'Metode Pembayaran', 'Status Pembayaran', 'Status Pesanan', 'Tanggal',
]);

foreach ($pesananList as $p) {
    fputcsv($out, [
        $p['id'],
        $p['nama_pemesan'],
        $p['email'],
        $p['telepon'],
        $p['alamat'],
        number_format((float) $p['total'], 0, ',', '.'),
        $p['metode_pembayaran'] === 'cod' ? 'COD' : 'Transfer Bank',
        str_replace('_', ' ', $p['status_pembayaran']),
        $p['status'],
        $p['dibuat_pada'],
    ]);
}

$totalPeriode = array_sum(array_column($pesananList, 'total'));
fputcsv($out, []);
fputcsv($out, ['', '', '', '', 'TOTAL', number_format($totalPeriode, 0, ',', '.'), '', '', '', '']);

fclose($out);
exit;
