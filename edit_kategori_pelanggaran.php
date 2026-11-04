
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Ambil ID kategori
$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: kategori_pelanggaran.php");
    exit;
}

// Ambil data kategori
$stmt = mysqli_prepare($koneksi,
    "SELECT * FROM t_pelanggaran_kategori WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    die("Kategori tidak ditemukan.");
}

$error = '';

// Proses update
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
            UPDATE t_pelanggaran_kategori
            SET
                nama = ?,
                deskripsi = ?,
                status_aktif = ?,
                updated_at = NOW()
            WHERE id = ?
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "ssii",
            $nama,
            $deskripsi,
            $status,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_close($stmt);

            header("Location: kategori_pelanggaran.php");
            exit;

        } else {

            $error = 'Gagal mengubah data: ' .
                mysqli_stmt_error($stmt);

            mysqli_stmt_close($stmt);
        }
    }

    // Pertahankan input jika validasi gagal
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

    <title>Edit Kategori Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">

            <h2 class="mb-1">Edit Kategori Pelanggaran</h2>

            <p class="text-muted">
                Ubah informasi kategori pelanggaran siswa.
            </p>

            <!-- CARD FORM -->
            <div class="card shadow-sm">

                <div class="card-header bg-light">
                    <strong>Form Edit Kategori Pelanggaran</strong>
                </div>

                <div class="card-body p-4">

                    <!-- PESAN ERROR -->
                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
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
                                value="<?= htmlspecialchars($data['nama'] ?? '') ?>"
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
                            ><?= htmlspecialchars($data['deskripsi'] ?? '') ?></textarea>

                        </div>

                        <!-- STATUS -->
                        <div class="mb-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select
                                name="status_aktif"
                                class="form-select"
                            >

                                <option value="1"
                                    <?= ($data['status_aktif'] == 1) ? 'selected' : '' ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= ($data['status_aktif'] == 0) ? 'selected' : '' ?>>
                                    Tidak Aktif
                                </option>

                            </select>

                        </div>

                        <!-- TOMBOL -->
                        <div class="d-flex gap-2">

                            <button type="submit" class="btn btn-primary">
                                Simpan Perubahan
                            </button>

                            <a href="kategori_pelanggaran.php"
                               class="btn btn-secondary">
                                Kembali
                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
