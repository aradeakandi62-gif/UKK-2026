
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi,
    "SELECT * FROM t_pelanggaran_kategori WHERE id = ?");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    die("Kategori tidak ditemukan.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status = (int)($_POST['status_aktif'] ?? 1);

    if ($nama === '') {
        $error = 'Nama kategori wajib diisi.';
    } else {
        $stmt = mysqli_prepare($koneksi, "
            UPDATE t_pelanggaran_kategori
            SET nama = ?, deskripsi = ?, status_aktif = ?, updated_at = NOW()
            WHERE id = ?
        ");

        mysqli_stmt_bind_param(
            $stmt, "ssii", $nama, $deskripsi, $status, $id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: kategori_pelanggaran.php");
            exit;
        } else {
            $error = 'Gagal mengubah data: ' . mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    $data['nama'] = $nama;
    $data['deskripsi'] = $deskripsi;
    $data['status_aktif'] = $status;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h4>Edit Kategori Pelanggaran</h4>
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
                           value="<?= htmlspecialchars($data['nama'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control"
                              rows="3"><?= htmlspecialchars($data['deskripsi'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status_aktif" class="form-select">
                        <option value="1" <?= $data['status_aktif'] == 1 ? 'selected' : '' ?>>
                            Aktif
                        </option>
                        <option value="0" <?= $data['status_aktif'] == 0 ? 'selected' : '' ?>>
                            Tidak Aktif
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="kategori_pelanggaran.php"
                   class="btn btn-secondary">Kembali</a>
            </form>

        </div>
    </div>
</div>
</body>
</html>
