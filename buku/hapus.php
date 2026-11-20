<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('buku/');
}

try {
    $stmt = $pdo->prepare('DELETE FROM buku WHERE id_buku = ?');
    $stmt->execute([post('id_buku')]);
    if ($stmt->rowCount()) {
        set_flash('success', 'Buku berhasil dihapus.');
    } else {
        set_flash('warning', 'Buku tidak ditemukan.');
    }
} catch (PDOException $e) {
    if (($e->errorInfo[1] ?? 0) === 1451) {
        set_flash('danger', 'Buku tidak dapat dihapus karena sudah memiliki riwayat peminjaman.');
    } else {
        set_flash('danger', 'Gagal menghapus: ' . $e->getMessage());
    }
}

redirect('buku/');
