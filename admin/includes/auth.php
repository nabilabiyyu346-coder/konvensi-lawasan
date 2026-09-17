<?php
/**
 * Panggil file ini di baris paling atas setiap halaman admin yang
 * perlu login (sebelum output apa pun), supaya bisa redirect.
 */
require_once __DIR__ . '/../../includes/session.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
