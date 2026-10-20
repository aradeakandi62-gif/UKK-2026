
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status = (int)($_POST['status_aktif'] ?? 1);

    if ($nama === '') {
        $error = 'Nama kategori wajib diisi.';
    } else {
        $stmt = mysqli_prepare($koneksi, "
            INSERT INTO t_pelanggaran_kategori
            (nama, deskripsi, status_aktif, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt, "ssi", $nama, $deskripsi, $status
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: kategori_pelanggaran.php");
                exit;
            } else {
                $error = 'Gagal menyimpan: ' . mysqli_stmt_error($stmt);
                mysqli_stmt_close($stmt);
            }
        } else {
            $error = 'Query gagal: ' . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h4>Tambah Kategori Pelanggaran</h4>
        </div>
        <div class="card-body">

            <?php if ($error !== '') { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php } ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="nama" class="form-control"
                           required
                           value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control"
                              rows="3"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status_aktif" class="form-select">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>
                <a href="kategori_pelanggaran.php"
                   class="btn btn-secondary">Kembali</a>
            </form>

        </div>
    </div>
</div>
</body>
</html>
