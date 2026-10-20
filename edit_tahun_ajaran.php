
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit('ID tahun ajaran tidak valid.');
}

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_tahun_ajaran WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    exit('Data tahun ajaran tidak ditemukan.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_tahun_ajaran'] ?? '');

    if ($nama === '') {
        $error = 'Nama tahun ajaran wajib diisi.';
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_tahun_ajaran SET nama_tahun_ajaran = ? WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, 'si', $nama, $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: tahun_ajaran.php');
            exit;
        }

        $error = 'Gagal mengubah data: ' . mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Tahun Ajaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3>Edit Tahun Ajaran</h3>

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="nama_tahun_ajaran" class="form-label">
                                Nama Tahun Ajaran
                            </label>
                            <input type="text"
                                   name="nama_tahun_ajaran"
                                   id="nama_tahun_ajaran"
                                   class="form-control"
                                   value="<?= htmlspecialchars(
                                       $_POST['nama_tahun_ajaran']
                                       ?? $data['nama_tahun_ajaran']
                                   ); ?>"
                                   required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                        <a href="tahun_ajaran.php" class="btn btn-secondary">
                            Kembali
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
