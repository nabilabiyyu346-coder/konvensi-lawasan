<?php
require_once 'includes/session.php';

// Hapus SEMUA data session (termasuk keranjang), bukan cuma status
// login — supaya kalau user lain login di browser/komputer yang
// sama, dia tidak mewarisi keranjang milik user sebelumnya.
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}

session_destroy();

header('Location: index.php');
exit;
