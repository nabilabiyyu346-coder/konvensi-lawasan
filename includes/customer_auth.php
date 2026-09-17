<?php
/**
 * Helper untuk cek status login pelanggan (bukan admin).
 * Panggil session.php dulu sebelum pakai fungsi-fungsi ini.
 */

function pelangganLoggedIn(): bool
{
    return !empty($_SESSION['pelanggan_id']);
}

function pelangganNama(): string
{
    return $_SESSION['pelanggan_nama'] ?? '';
}

/**
 * Panggil di baris paling atas halaman yang wajib login (checkout).
 * $tujuanSetelahLogin: halaman yang dituju setelah berhasil login.
 */
function wajibLoginPelanggan(string $tujuanSetelahLogin): void
{
    if (!pelangganLoggedIn()) {
        header('Location: login.php?kembali_ke=' . urlencode($tujuanSetelahLogin));
        exit;
    }
}
