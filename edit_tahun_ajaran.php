
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Ambil ID tahun ajaran
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: tahun_ajaran.php");
    exit;
}

// Ambil data tahun ajaran
$query = mysqli_query($koneksi, "
    SELECT * FROM t_tahun_ajaran WHERE id = $id
");

if (!$query || mysqli_num_rows($query) == 0) {
    die("Data tahun ajaran tidak ditemukan.");
}

$data = mysqli_fetch_assoc($query);

$error = '';

// Proses update
if (isset($_POST['simpan'])) {

    $nama = trim($_POST['nama'] ?? '');

    if ($nama == '') {
        $error = "Nama tahun ajaran tidak boleh kosong.";
    } else {

        $nama = mysqli_real_escape_string($koneksi, $nama);

        $update = mysqli_query($koneksi, "
            UPDATE t_tahun_ajaran
            SET nama = '$nama'
            WHERE id = $id
        ");

        if ($update) {
            header("Location: tahun_ajaran.php");
            exit;
        } else {
            $error = "Data gagal diperbarui: " . mysqli_error($koneksi);
        }
    }
}

// Role pengguna
$role = strtolower(trim($_SESSION['role'] ?? ''));

$dashboard = ($role === 'guru')
    ? 'dashboard_guru.php'
    : 'dashboard.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Tahun Ajaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

</head>

<body class="bg-light">

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php
    if ($role === 'guru') {
        include 'layout/sidebar_guru.php';
    } else {
        include 'layout/sidebar.php';
    }
    ?>

    <!-- KONTEN UTAMA -->
    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">
          

        <div class="container-fluid">

            <!-- JUDUL -->
            <div class="mb-4">

                <h3 class="fw-bold">
                    Edit Tahun Ajaran
                </h3>

                <p class="text-muted">
                    Ubah data tahun ajaran
                </p>

            </div>

            <!-- PESAN ERROR -->
            <?php if ($error != ''): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>

            <!-- FORM EDIT -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">

                    <strong>Edit Tahun Ajaran</strong>

                </div>

                <div class="card-body">

                    <form method="POST">

                        <div class="mb-3">

                            <label class="form-label">
                                Nama Tahun Ajaran
                            </label>

                            <input type="text"
                                   name="nama"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nama']) ?>"
                                   placeholder="Contoh: 2025/2026"
                                   required>

                        </div>

                        <!-- TOMBOL -->
                        <div class="d-flex gap-2">

                            <button type="submit"
                                    name="simpan"
                                    class="btn btn-primary">

                                Simpan Perubahan

                            </button>

                            <a href="tahun_ajaran.php"
                               class="btn btn-secondary">

                                Kembali

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
