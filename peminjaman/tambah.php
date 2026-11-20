<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/functions.php';

$errors = [];
$idAnggota = '';
$idBuku = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idAnggota = post('id_anggota');
    $idBuku = post('id_buku');

    if ($idAnggota === '' || $idBuku === '') {
        $errors[] = 'Anggota dan buku wajib dipilih.';
    }

    if (!$errors) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('SELECT stok FROM buku WHERE id_buku = ? FOR UPDATE');
            $stmt->execute([$idBuku]);
            $buku = $stmt->fetch();

            if (!$buku) {
                $pdo->rollBack();
                $errors[] = 'Buku tidak ditemukan.';
            } elseif ((int) $buku['stok'] < 1) {
                $pdo->rollBack();
                $errors[] = 'Stok buku habis, tidak dapat dipinjam.';
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO peminjaman (id_anggota, id_buku, tgl_pinjam, status) VALUES (?, ?, CURDATE(), 'dipinjam')"
                );
                $stmt->execute([$idAnggota, $idBuku]);

                $stmt = $pdo->prepare('UPDATE buku SET stok = stok - 1 WHERE id_buku = ?');
                $stmt->execute([$idBuku]);

                $pdo->commit();
                set_flash('success', 'Peminjaman berhasil dicatat.');
                redirect('peminjaman/');
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = ($e->errorInfo[1] ?? 0) === 1452
                ? 'Anggota atau buku tidak terdaftar.'
                : 'Gagal mencatat peminjaman: ' . $e->getMessage();
        }
    }
}

$anggota = $pdo->query('SELECT id_anggota, nama FROM anggota ORDER BY nama')->fetchAll();
$buku = $pdo->query('SELECT id_buku, judul, stok FROM buku WHERE stok > 0 ORDER BY judul')->fetchAll();

$judul = 'Pinjam Buku';
require __DIR__ . '/../includes/header.php';
?>
<h1 class="h4 mb-3">Pinjam Buku</h1>
<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<form method="post" class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="id_anggota">Anggota</label>
        <select class="form-select" id="id_anggota" name="id_anggota" required>
            <option value="">-- Pilih anggota --</option>
            <?php foreach ($anggota as $a): ?>
                <option value="<?= e($a['id_anggota']) ?>" <?= $idAnggota === $a['id_anggota'] ? 'selected' : '' ?>>
                    <?= e($a['id_anggota'] . ' - ' . $a['nama']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="id_buku">Buku</label>
        <select class="form-select" id="id_buku" name="id_buku" required>
            <option value="">-- Pilih buku --</option>
            <?php foreach ($buku as $b): ?>
                <option value="<?= e($b['id_buku']) ?>" <?= $idBuku === $b['id_buku'] ? 'selected' : '' ?>>
                    <?= e($b['id_buku'] . ' - ' . $b['judul'] . ' (stok ' . $b['stok'] . ')') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-12">
        <button class="btn btn-primary">Pinjam</button>
        <a class="btn btn-secondary" href="<?= url('peminjaman/') ?>">Batal</a>
    </div>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
