<?php
$mode = $mode ?? 'tambah';
?>
<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="id_buku">ID Buku</label>
        <input class="form-control" id="id_buku" name="id_buku" value="<?= e($data['id_buku']) ?>"
               <?= $mode === 'edit' ? 'readonly' : '' ?> required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="judul">Judul</label>
        <input class="form-control" id="judul" name="judul" value="<?= e($data['judul']) ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="pengarang">Pengarang</label>
        <input class="form-control" id="pengarang" name="pengarang" value="<?= e($data['pengarang']) ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="penerbit">Penerbit</label>
        <input class="form-control" id="penerbit" name="penerbit" value="<?= e($data['penerbit']) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="tahun_terbit">Tahun Terbit</label>
        <input class="form-control" id="tahun_terbit" name="tahun_terbit" type="number" min="0" value="<?= e($data['tahun_terbit']) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="stok">Stok</label>
        <input class="form-control" id="stok" name="stok" type="number" min="0" value="<?= e($data['stok']) ?>" required>
    </div>
</div>
<div class="mt-4">
    <button class="btn btn-primary">Simpan</button>
    <a class="btn btn-secondary" href="<?= url('buku/') ?>">Batal</a>
</div>
