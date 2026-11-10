
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$current_page = basename($_SERVER['PHP_SELF']);

$error = '';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: data_guru.php');
    exit;
}

// Ambil data guru
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, nip, nama, email, status_aktif
     FROM t_guru
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$guru = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$guru) {
    die("Data guru tidak ditemukan.");
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nip = trim($_POST['nip'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $status = (int) ($_POST['status_aktif'] ?? 1);

    if ($nip === '' || $nama === '' || $email === '') {
        $error = "Semua kolom wajib diisi.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";

    } elseif (!in_array($status, [0, 1], true)) {
        $error = "Status tidak valid.";

    } else {

        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_guru
             SET nip = ?,
                 nama = ?,
                 email = ?,
                 status_aktif = ?,
                 updated_at = NOW()
             WHERE id = ?"
        );

        if (!$stmt) {
            $error = "Gagal menyiapkan query: " . mysqli_error($koneksi);

        } else {

            mysqli_stmt_bind_param(
                $stmt,
                "sssii",
                $nip,
                $nama,
                $email,
                $status,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                header('Location: data_guru.php');
                exit;

            } else {

                $error = "Gagal mengubah data: " . mysqli_stmt_error($stmt);

                mysqli_stmt_close($stmt);
            }
        }
    }

    // Menampilkan kembali input jika terjadi kesalahan
    $guru['nip'] = $nip;
    $guru['nama'] = $nama;
    $guru['email'] = $email;
    $guru['status_aktif'] = $status;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Guru</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include 'layout/sidebar.php'; ?>

    <!-- KONTEN UTAMA -->
    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <div class="container-fluid">

            <!-- JUDUL -->
            <div class="mb-4">
                <h2 class="fw-bold">Edit Guru</h2>
                <p class="text-muted mb-0">
                    Ubah informasi data guru.
                </p>
            </div>

            <!-- FORM EDIT -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">
                    <strong>Form Edit Guru</strong>
                </div>

                <div class="card-body p-4">

                    <!-- PESAN ERROR -->
                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST"
                          action="edit_guru.php?id=<?= $id ?>">

                        <!-- NIP -->
                        <div class="mb-3">
                            <label class="form-label">NIP</label>

                            <input type="text"
                                   name="nip"
                                   class="form-control"
                                   value="<?= htmlspecialchars($guru['nip'] ?? '') ?>"
                                   required>
                        </div>

                        <!-- NAMA GURU -->
                        <div class="mb-3">
                            <label class="form-label">Nama Guru</label>

                            <input type="text"
                                   name="nama"
                                   class="form-control"
                                   value="<?= htmlspecialchars($guru['nama'] ?? '') ?>"
                                   required>
                        </div>

                        <!-- EMAIL -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>

                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="<?= htmlspecialchars($guru['email'] ?? '') ?>"
                                   required>
                        </div>

                        <!-- STATUS -->
                        <div class="mb-4">
                            <label class="form-label">Status</label>

                            <select name="status_aktif"
                                    class="form-select"
                                    required>

                                <option value="1"
                                    <?= $guru['status_aktif'] == 1 ? 'selected' : '' ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= $guru['status_aktif'] == 0 ? 'selected' : '' ?>>
                                    Tidak Aktif
                                </option>

                            </select>
                        </div>

                        <!-- TOMBOL -->
                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>

                        <a href="data_guru.php" class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>
            </div>

        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
