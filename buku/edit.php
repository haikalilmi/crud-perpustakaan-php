<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/functions.php';

$id = $_GET['id'] ?? post('id_buku');
if ($id === '') {
    set_flash('warning', 'Pilih buku yang akan diedit.');
    redirect('buku/');
}

$stmt = $pdo->prepare('SELECT * FROM buku WHERE id_buku = ?');
$stmt->execute([$id]);
$buku = $stmt->fetch();
if (!$buku) {
    set_flash('warning', 'Buku tidak ditemukan.');
    redirect('buku/');
}

$data = $buku;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['judul', 'pengarang', 'penerbit', 'tahun_terbit', 'stok'] as $kunci) {
        $data[$kunci] = post($kunci);
    }
    $errors = validasi_buku($data, false);

    if (!$errors) {
        try {
            $stmt = $pdo->prepare(
                'UPDATE buku SET judul = ?, pengarang = ?, penerbit = ?, tahun_terbit = ?, stok = ? WHERE id_buku = ?'
            );
            $stmt->execute([
                $data['judul'],
                $data['pengarang'],
                $data['penerbit'],
                $data['tahun_terbit'] === '' ? null : (int) $data['tahun_terbit'],
                (int) $data['stok'],
                $id,
            ]);
            set_flash('success', 'Data buku berhasil diperbarui.');
            redirect('buku/');
        } catch (PDOException $e) {
            $errors[] = 'Gagal memperbarui: ' . $e->getMessage();
        }
    }
}

$judul = 'Edit Buku';
require __DIR__ . '/../includes/header.php';
?>
<h1 class="h4 mb-3">Edit Buku</h1>
<form method="post">
    <?php $mode = 'edit'; require __DIR__ . '/_form.php'; ?>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
