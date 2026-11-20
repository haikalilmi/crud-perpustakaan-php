<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/functions.php';

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = $pdo->prepare('SELECT * FROM buku WHERE judul LIKE ? OR pengarang LIKE ? ORDER BY id_buku');
    $stmt->execute(['%' . $q . '%', '%' . $q . '%']);
} else {
    $stmt = $pdo->query('SELECT * FROM buku ORDER BY id_buku');
}
$daftar = $stmt->fetchAll();

$judul = 'Data Buku';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Data Buku</h1>
    <a class="btn btn-success" href="<?= url('buku/tambah.php') ?>">+ Tambah Buku</a>
</div>
<form class="row g-2 mb-3" method="get">
    <div class="col-sm-6 col-md-4">
        <input class="form-control" type="search" name="q" value="<?= e($q) ?>" placeholder="Cari judul/pengarang...">
    </div>
    <div class="col-auto"><button class="btn btn-outline-primary">Cari</button></div>
</form>
<div class="table-responsive">
    <table class="table table-striped table-bordered bg-white">
        <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Judul</th>
            <th>Pengarang</th>
            <th>Penerbit</th>
            <th>Tahun</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($daftar as $b): ?>
            <tr>
                <td><?= e($b['id_buku']) ?></td>
                <td><?= e($b['judul']) ?></td>
                <td><?= e($b['pengarang']) ?></td>
                <td><?= e($b['penerbit']) ?></td>
                <td><?= e($b['tahun_terbit']) ?></td>
                <td><?= e($b['stok']) ?></td>
                <td class="text-nowrap">
                    <a class="btn btn-sm btn-warning" href="<?= url('buku/edit.php?id=' . urlencode($b['id_buku'])) ?>">Edit</a>
                    <form class="d-inline" method="post" action="<?= url('buku/hapus.php') ?>" onsubmit="return confirm('Hapus buku ini?')">
                        <input type="hidden" name="id_buku" value="<?= e($b['id_buku']) ?>">
                        <button class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$daftar): ?>
            <tr><td colspan="7" class="text-center text-muted">Data tidak ditemukan.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
