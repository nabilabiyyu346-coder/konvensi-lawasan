<?php
/**
 * Bootstrap session — di-include di awal setiap halaman yang
 * butuh $_SESSION (keranjang belanja & login admin).
 */
if (session_status() === PHP_SESSION_NONE) {
    // Default PHP cuma menganggap sesi basi setelah ±24 menit tanpa
    // aktivitas. Itu cukup untuk bikin customer "terlempar" logout saat
    // mereka pindah ke app m-banking/QRIS lalu balik lagi. Panjangkan
    // jadi 3 jam supaya sesi & keranjang tetap hidup selama itu.
    $umurSesi = 60 * 60 * 3; // 3 jam
    session_set_cookie_params($umurSesi);
    ini_set('session.gc_maxlifetime', $umurSesi);
    session_start();
}
