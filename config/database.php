<?php
/**
 * Koneksi basis data — pakai PDO supaya bisa prepared statements
 * (aman dari SQL Injection).
 *
 * Sesuaikan DB_USER / DB_PASS jika kredensial MySQL kamu berbeda
 * dari default XAMPP (root, tanpa password).
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'if0_42851281_batik');
define('DB_USER', 'root');
define('DB_PASS', '');

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
} catch (PDOException $e) {
    die('Koneksi basis data gagal. Pastikan MySQL aktif dan database "if0_42851281_batik" sudah diimport dari database/schema.sql. Detail: ' . $e->getMessage());
}
