<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/functions.php';

$errors = [];
$data = ['id_buku' => '', 'judul' => '', 'pengarang' => '', 'penerbit' => '', 'tahun_terbit' => '', 'stok' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($data as $kunci => $nilai) {
        $data[$kunci] = post($kunci);
    }
    $errors = validasi_buku($data);

    if (!$errors) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO buku (id_buku, judul, pengarang, penerbit, tahun_terbit, stok) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $data['id_buku'],
                $data['judul'],
                $data['pengarang'],
                $data['penerbit'],
                $data['tahun_terbit'] === '' ? null : (int) $data['tahun_terbit'],
                (int) $data['stok'],
            ]);
            set_flash('success', 'Buku berhasil ditambahkan.');
            redirect('buku/');
        } catch (PDOException $e) {
            $errors[] = ($e->errorInfo[1] ?? 0) === 1062
                ? 'ID buku sudah dipakai.'
                : 'Gagal menyimpan: ' . $e->getMessage();
        }
    }
}

$judul = 'Tambah Buku';
require __DIR__ . '/../includes/header.php';
?>
<h1 class="h4 mb-3">Tambah Buku</h1>
<form method="post">
    <?php require __DIR__ . '/_form.php'; ?>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
