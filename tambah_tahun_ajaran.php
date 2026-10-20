
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_tahun_ajaran'] ?? '');

    if ($nama === '') {
        $error = 'Nama tahun ajaran wajib diisi.';
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO t_tahun_ajaran (nama_tahun_ajaran) VALUES (?)"
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $nama);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header('Location: tahun_ajaran.php');
                exit;
            }

            $error = 'Gagal menyimpan: ' . mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
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
    <title>Tambah Tahun Ajaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3>Tambah Tahun Ajaran</h3>
                    <p class="text-muted">Tambahkan tahun ajaran baru ke sistem.</p>

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
                                   placeholder="Contoh: 2026/2027 Ganjil"
                                   required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Tahun Ajaran
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
