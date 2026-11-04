
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status = (int) ($_POST['status_aktif'] ?? 1);

    if ($nama === '') {

        $error = 'Nama kategori wajib diisi.';

    } elseif (!in_array($status, [0, 1], true)) {

        $error = 'Status tidak valid.';

    } else {

        $stmt = mysqli_prepare($koneksi, "
            INSERT INTO t_pelanggaran_kategori
            (nama, deskripsi, status_aktif, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $nama,
                $deskripsi,
                $status
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

    <title>Tambah Kategori Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <h2 class="mb-1">Tambah Kategori Pelanggaran</h2>

            <p class="text-muted mb-3">
                Tambahkan kategori pelanggaran siswa ke dalam sistem.
            </p>

            <!-- FORM TAMBAH KATEGORI -->
            <div class="card shadow-sm">

                <div class="card-header bg-light fw-bold">
                    Form Kategori Pelanggaran
                </div>

                <div class="card-body">

                    <?php if ($error !== ''): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST">

                        <!-- NAMA KATEGORI -->
                        <div class="mb-3">

                            <label class="form-label">
                                Nama Kategori
                            </label>

                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                placeholder="Masukkan nama kategori"
                                value="<?= htmlspecialchars($_POST['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >

                        </div>

                        <!-- DESKRIPSI -->
                        <div class="mb-3">

                            <label class="form-label">
                                Deskripsi
                            </label>

                            <textarea
                                name="deskripsi"
                                class="form-control"
                                rows="4"
                                placeholder="Masukkan deskripsi kategori"
                            ><?= htmlspecialchars($_POST['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

                        </div>

                        <!-- STATUS -->
                        <div class="mb-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select name="status_aktif" class="form-select">

                                <option value="1"
                                    <?= (($_POST['status_aktif'] ?? '1') == '1') ? 'selected' : '' ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= (($_POST['status_aktif'] ?? '') == '0') ? 'selected' : '' ?>>
                                    Tidak Aktif
                                </option>

                            </select>

                        </div>

                        <!-- TOMBOL -->
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>

                        <a href="kategori_pelanggaran.php"
                           class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>

            </div>

        </main>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
