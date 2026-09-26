<?php
/**
 * Koneksi basis data — pakai PDO supaya bisa prepared statements
 * (aman dari SQL Injection).
 *
 * Sesuaikan DB_USER / DB_PASS jika kredensial MySQL kamu berbeda
 * dari default XAMPP (root, tanpa password).
 */

// Semua tampilan waktu & pencatatan waktu transaksi memakai WIB (Asia/Jakarta),
// bukan jam bawaan server hosting.
date_default_timezone_set('Asia/Jakarta');

define('DB_HOST', 'sql211.infinityfree.com');
define('DB_NAME', 'if0_42851281_batik');
define('DB_USER', 'if0_42851281');
define('DB_PASS', 'ramadh4n17');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    // MySQL sendiri punya jam terpisah dari PHP; paksa sesi koneksi ini
    // ikut WIB (+07:00) supaya NOW()/CURRENT_TIMESTAMP() konsisten juga.
    $pdo->exec("SET time_zone = '+07:00'");
} catch (PDOException $e) {
    die('Koneksi basis data gagal. Pastikan MySQL aktif dan database "if0_42851281_batik" sudah diimport dari database/schema.sql. Detail: ' . $e->getMessage());
}
