
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$id = (int) ($_GET['id'] ?? 0);
$pesan = '';

if ($id <= 0) {
    header("Location: data_kelas.php");
    exit;
}

// Ambil data kelas
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_kelas WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    die("Data kelas tidak ditemukan.");
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama'] ?? '');
    $tingkat = trim($_POST['tingkat'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $status_aktif = (int) ($_POST['status_aktif'] ?? 1);

    if (
        $nama === '' ||
        !in_array($tingkat, ['X', 'XI', 'XII'], true) ||
        $jurusan === '' ||
        !in_array($status_aktif, [0, 1], true)
    ) {
        $pesan = "Lengkapi semua data dengan benar.";
    } else {

        $update = mysqli_prepare(
            $koneksi,
            "UPDATE t_kelas
             SET nama = ?,
                 tingkat = ?,
                 jurusan = ?,
                 status_aktif = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $update,
            "sssii",
            $nama,
            $tingkat,
            $jurusan,
            $status_aktif,
            $id
        );

        if (mysqli_stmt_execute($update)) {

            mysqli_stmt_close($update);

            header("Location: data_kelas.php");
            exit;

        } else {

            $pesan = "Gagal mengubah data: " .
                     mysqli_stmt_error($update);

            mysqli_stmt_close($update);
        }
    }

    // Pertahankan data jika terjadi kesalahan
    $data['nama'] = $nama;
    $data['tingkat'] = $tingkat;
    $data['jurusan'] = $jurusan;
    $data['status_aktif'] = $status_aktif;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Kelas</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="d-flex">

    <!-- SIDEBAR LENGKAP -->
    <?php include 'layout/sidebar.php'; ?>

    <!-- KONTEN UTAMA -->
    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <div class="container-fluid">

            <h2 class="mb-1">Edit Kelas</h2>

            <p class="text-muted mb-4">
                Ubah data kelas.
            </p>

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">
                    <strong>Form Edit Kelas</strong>
                </div>

                <div class="card-body">

                    <?php if ($pesan !== ''): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST"
                          action="edit_kelas.php?id=<?= $id ?>">

                        <!-- NAMA KELAS -->
                        <div class="mb-3">

                            <label class="form-label">
                                Nama Kelas
                            </label>

                            <input type="text"
                                   name="nama"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nama'] ?? '') ?>"
                                   required>

                        </div>

                        <!-- TINGKAT -->
                        <div class="mb-3">

                            <label class="form-label">
                                Tingkat
                            </label>

                            <select name="tingkat"
                                    class="form-select"
                                    required>

                                <option value="X"
                                    <?= ($data['tingkat'] ?? '') === 'X' ? 'selected' : '' ?>>
                                    X
                                </option>

                                <option value="XI"
                                    <?= ($data['tingkat'] ?? '') === 'XI' ? 'selected' : '' ?>>
                                    XI
                                </option>

                                <option value="XII"
                                    <?= ($data['tingkat'] ?? '') === 'XII' ? 'selected' : '' ?>>
                                    XII
                                </option>

                            </select>

                        </div>

                        <!-- JURUSAN -->
                        <div class="mb-3">

                            <label class="form-label">
                                Jurusan
                            </label>

                            <input type="text"
                                   name="jurusan"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['jurusan'] ?? '') ?>"
                                   required>

                        </div>

                        <!-- STATUS -->
                        <div class="mb-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select name="status_aktif"
                                    class="form-select"
                                    required>

                                <option value="1"
                                    <?= (int)$data['status_aktif'] === 1 ? 'selected' : '' ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= (int)$data['status_aktif'] === 0 ? 'selected' : '' ?>>
                                    Tidak Aktif
                                </option>

                            </select>

                        </div>

                        <!-- TOMBOL -->
                        <div class="d-flex gap-2">

                            <button type="submit"
                                    class="btn btn-primary">
                                Simpan Perubahan
                            </button>

                            <a href="data_kelas.php"
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

</body>
</html>
