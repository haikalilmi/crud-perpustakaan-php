<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('peminjaman/');
}

$id = post('id_pinjam');

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('SELECT * FROM peminjaman WHERE id_pinjam = ? FOR UPDATE');
    $stmt->execute([$id]);
    $pinjam = $stmt->fetch();

    if (!$pinjam) {
        $pdo->rollBack();
        set_flash('warning', 'Transaksi tidak ditemukan.');
        redirect('peminjaman/');
    }

    if ($pinjam['status'] !== 'dipinjam') {
        $pdo->rollBack();
        set_flash('warning', 'Buku pada transaksi ini sudah dikembalikan.');
        redirect('peminjaman/');
    }

    $stmt = $pdo->prepare("UPDATE peminjaman SET status = 'kembali', tgl_kembali = CURDATE() WHERE id_pinjam = ?");
    $stmt->execute([$id]);

    $stmt = $pdo->prepare('UPDATE buku SET stok = stok + 1 WHERE id_buku = ?');
    $stmt->execute([$pinjam['id_buku']]);

    $pdo->commit();
    set_flash('success', 'Buku berhasil dikembalikan.');
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    set_flash('danger', 'Gagal memproses pengembalian: ' . $e->getMessage());
}

redirect('peminjaman/');
