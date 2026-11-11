
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$error = '';

if (isset($_POST['simpan'])) {

    // Ambil input
    $nama = trim($_POST['nama'] ?? '');

    if ($nama == '') {

        $error = "Nama tahun ajaran tidak boleh kosong.";

    } else {

        $nama = mysqli_real_escape_string($koneksi, $nama);

        // Cek apakah tahun ajaran sudah ada
        $cek = mysqli_query($koneksi, "
            SELECT id FROM t_tahun_ajaran
            WHERE nama = '$nama'
        ");

        if (!$cek) {

            $error = "Gagal memeriksa data: " . mysqli_error($koneksi);

        } elseif (mysqli_num_rows($cek) > 0) {

            $error = "Tahun ajaran tersebut sudah tersedia.";

        } else {

            // SIMPAN KE TABEL TAHUN AJARAN
            $query = mysqli_query($koneksi, "
                INSERT INTO t_tahun_ajaran (nama)
                VALUES ('$nama')
            ");

            if ($query) {

                header("Location: tahun_ajaran.php");
                exit;

            } else {

                $error = "Data gagal ditambahkan: " . mysqli_error($koneksi);

            }
        }
    }
}

// Role pengguna
$role = strtolower(trim($_SESSION['role'] ?? ''));
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tambah Tahun Ajaran</title>

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

    <!-- KONTEN -->
    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <div class="container-fluid">

            <h3 class="fw-bold">Tambah Tahun Ajaran</h3>

            <p class="text-muted">
                Tambahkan data tahun ajaran baru
            </p>

            <!-- PESAN ERROR -->
            <?php if ($error != ''): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>

            <!-- FORM -->
            <div class="card shadow-sm">

                <div class="card-header">
                    <strong>Form Tambah Tahun Ajaran</strong>
                </div>

                <div class="card-body">

                    <form method="POST">

                        <div class="mb-3">

                            <label class="form-label">
                                Nama Tahun Ajaran
                            </label>

                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                placeholder="Contoh: 2026/2027"
                                value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                                required
                            >

                        </div>

                        <!-- TOMBOL -->
                        <div class="d-flex gap-2">

                            <button type="submit"
                                    name="simpan"
                                    class="btn btn-primary">

                                Simpan Tahun Ajaran

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
