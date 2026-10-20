<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: jenis_pelanggaran.php');
    exit;
}

$error = '';

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_pelanggaran WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    die('Data jenis pelanggaran tidak ditemukan.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori_id = (int) ($_POST['pelanggaran_kategori_id'] ?? 0);
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $poin = (int) ($_POST['poin'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if ($kategori_id <= 0 || $kode === '' || $nama === '' || $poin < 0) {
        $error = 'Lengkapi data dengan benar!';
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_pelanggaran
             SET pelanggaran_kategori_id = ?,
                 kode = ?, nama = ?, poin = ?, deskripsi = ?,
                 updated_at = CURRENT_TIMESTAMP
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt, "issisi",
            $kategori_id, $kode, $nama, $poin, $deskripsi, $id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: jenis_pelanggaran.php');
            exit;
        }

        $error = 'Gagal mengubah data: ' . mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
    }
}

$kategori = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_pelanggaran_kategori
     WHERE status_aktif = 1 ORDER BY nama ASC"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Jenis Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <h2>Edit Jenis Pelanggaran</h2>
    <p>Ubah data jenis pelanggaran siswa.</p>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">Form Edit Jenis Pelanggaran</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="pelanggaran_kategori_id"
                            class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($k = mysqli_fetch_assoc($kategori)): ?>
                            <option value="<?= (int)$k['id'] ?>"
                                <?= (int)$k['id'] === (int)$data['pelanggaran_kategori_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['nama']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kode</label>
                    <input type="text" name="kode" class="form-control"
                           value="<?= htmlspecialchars($data['kode']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Pelanggaran</label>
                    <input type="text" name="nama" class="form-control"
                           value="<?= htmlspecialchars($data['nama']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Poin</label>
                    <input type="number" name="poin" min="0"
                           class="form-control"
                           value="<?= (int)$data['poin'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control"
                              rows="3"><?= htmlspecialchars($data['deskripsi'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="jenis_pelanggaran.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>