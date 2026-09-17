<?php
/**
 * Bootstrap session — di-include di awal setiap halaman yang
 * butuh $_SESSION (keranjang belanja & login admin).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
