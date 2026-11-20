<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($nilai): string
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function set_flash(string $tipe, string $pesan): void
{
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function get_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function post(string $kunci, string $default = ''): string
{
    return trim((string) ($_POST[$kunci] ?? $default));
}

function validasi_buku(array $data, bool $cekId = true): array
{
    $error = [];
    if ($cekId && !preg_match('/^B\d{3}$/', $data['id_buku'])) {
        $error[] = 'ID buku harus berformat B diikuti 3 angka, contoh B008.';
    }
    if ($data['judul'] === '') {
        $error[] = 'Judul wajib diisi.';
    }
    if ($data['pengarang'] === '') {
        $error[] = 'Pengarang wajib diisi.';
    }
    if ($data['tahun_terbit'] !== '' && !ctype_digit($data['tahun_terbit'])) {
        $error[] = 'Tahun terbit harus berupa angka.';
    }
    if ($data['stok'] === '' || !ctype_digit($data['stok'])) {
        $error[] = 'Stok harus berupa angka 0 atau lebih.';
    }
    return $error;
}
