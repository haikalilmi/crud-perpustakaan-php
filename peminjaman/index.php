<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/functions.php';

$status = $_GET['status'] ?? 'semua';
$sql = "SELECT p.id_pinjam, a.nama, b.judul, p.tgl_pinjam, p.tgl_kembali, p.status,
        CASE WHEN p.status = 'dipinjam'
             THEN GREATEST(DATEDIFF(CURDATE(), p.tgl_pinjam) - 7, 0)
             ELSE 0 END AS hari_terlambat
        FROM peminjaman p
        INNER JOIN anggota a ON a.id_anggota = p.id_anggota
        INNER JOIN buku b ON b.id_buku = p.id_buku";
$params = [];

if ($status === 'dipinjam' || $status === 'kembali') {
    $sql .= ' WHERE p.status = ?';
    $params[] = $status;
} elseif ($status === 'terlambat') {
    $sql .= " WHERE p.status = 'dipinjam' AND DATEDIFF(CURDATE(), p.tgl_pinjam) > 7";
}
$sql .= ' ORDER BY p.tgl_pinjam DESC, p.id_pinjam DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$daftar = $stmt->fetchAll();

$judul = 'Data Peminjaman';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Data Peminjaman</h1>
    <a class="btn btn-success" href="<?= url('peminjaman/tambah.php') ?>">+ Pinjam Buku</a>
</div>
<form class="row g-2 mb-3" method="get">
    <div class="col-auto">
        <select class="form-select" name="status">
            <?php foreach (['semua' => 'Semua', 'dipinjam' => 'Dipinjam', 'terlambat' => 'Terlambat', 'kembali' => 'Kembali'] as $nilai => $label): ?>
                <option value="<?= $nilai ?>" <?= $status === $nilai ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-primary">Cari</button></div>
</form>
<div class="table-responsive">
    <table class="table table-striped table-bordered bg-white">
        <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Anggota</th>
            <th>Judul Buku</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($daftar as $p): ?>
            <?php
            if ($p['status'] === 'kembali') {
                $badge = 'success';
                $label = 'kembali';
            } elseif ((int) $p['hari_terlambat'] > 0) {
                $badge = 'danger';
                $label = 'terlambat ' . $p['hari_terlambat'] . ' hari';
            } else {
                $badge = 'warning';
                $label = 'dipinjam';
            }
            ?>
            <tr>
                <td><?= e($p['id_pinjam']) ?></td>
                <td><?= e($p['nama']) ?></td>
                <td><?= e($p['judul']) ?></td>
                <td><?= e($p['tgl_pinjam']) ?></td>
                <td><?= e($p['tgl_kembali'] ?: '-') ?></td>
                <td><span class="badge text-bg-<?= $badge ?>"><?= e($label) ?></span></td>
                <td class="text-nowrap">
                    <?php if ($p['status'] === 'dipinjam'): ?>
                        <form class="d-inline" method="post" action="<?= url('peminjaman/kembalikan.php') ?>">
                            <input type="hidden" name="id_pinjam" value="<?= e($p['id_pinjam']) ?>">
                            <button class="btn btn-sm btn-primary">Kembalikan</button>
                        </form>
                    <?php endif; ?>
                    <form class="d-inline" method="post" action="<?= url('peminjaman/hapus.php') ?>" onsubmit="return confirm('Hapus transaksi ini?')">
                        <input type="hidden" name="id_pinjam" value="<?= e($p['id_pinjam']) ?>">
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
