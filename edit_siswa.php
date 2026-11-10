
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$id = (int) ($_GET['id'] ?? 0);
$error = '';

if ($id <= 0) {
    header("Location: data_siswa.php");
    exit;
}

// Ambil data siswa
$stmt = mysqli_prepare($koneksi, "
    SELECT *
    FROM t_siswa
    WHERE id = ?
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    die("Data siswa tidak ditemukan.");
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nis = trim($_POST['nis'] ?? '');
    $nisn = trim($_POST['nisn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $status_aktif = (int) ($_POST['status_aktif'] ?? 1);

    if (
        $nis === '' ||
        $nisn === '' ||
        $nama === '' ||
        !in_array($jenis_kelamin, ['L', 'P'], true) ||
        $tanggal_lahir === '' ||
        !in_array($status_aktif, [0, 1], true)
    ) {
        $error = "Semua data wajib diisi dengan benar.";
    } else {

        $stmt = mysqli_prepare($koneksi, "
            UPDATE t_siswa SET
                nis = ?,
                nisn = ?,
                nama = ?,
                jenis_kelamin = ?,
                tanggal_lahir = ?,
                alamat = ?,
                status_aktif = ?
            WHERE id = ?
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssii",
            $nis,
            $nisn,
            $nama,
            $jenis_kelamin,
            $tanggal_lahir,
            $alamat,
            $status_aktif,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_close($stmt);

            header("Location: data_siswa.php");
            exit;

        } else {

            $error = "Gagal mengubah data: " . mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // Menampilkan kembali input jika terjadi kesalahan
    $data['nis'] = $nis;
    $data['nisn'] = $nisn;
    $data['nama'] = $nama;
    $data['jenis_kelamin'] = $jenis_kelamin;
    $data['tanggal_lahir'] = $tanggal_lahir;
    $data['alamat'] = $alamat;
    $data['status_aktif'] = $status_aktif;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Siswa</title>

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

            <h2 class="mb-1">Edit Siswa</h2>

            <p class="text-muted mb-4">
                Ubah data siswa.
            </p>

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">
                    <strong>Form Edit Siswa</strong>
                </div>

                <div class="card-body">

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST"
                          action="edit_siswa.php?id=<?= $id ?>">

                        <!-- NIS -->
                        <div class="mb-3">
                            <label class="form-label">NIS</label>

                            <input type="text"
                                   name="nis"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nis'] ?? '') ?>"
                                   required>
                        </div>

                        <!-- NISN -->
                        <div class="mb-3">
                            <label class="form-label">NISN</label>

                            <input type="text"
                                   name="nisn"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nisn'] ?? '') ?>"
                                   required>
                        </div>

                        <!-- NAMA -->
                        <div class="mb-3">
                            <label class="form-label">Nama Siswa</label>

                            <input type="text"
                                   name="nama"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nama'] ?? '') ?>"
                                   required>
                        </div>

                        <!-- JENIS KELAMIN -->
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>

                            <select name="jenis_kelamin"
                                    class="form-select"
                                    required>

                                <option value="L"
                                    <?= ($data['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>
                                    Laki-laki
                                </option>

                                <option value="P"
                                    <?= ($data['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>
                                    Perempuan
                                </option>

                            </select>
                        </div>

                        <!-- TANGGAL LAHIR -->
                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>

                            <input type="date"
                                   name="tanggal_lahir"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['tanggal_lahir'] ?? '') ?>"
                                   required>
                        </div>

                        <!-- ALAMAT -->
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>

                            <textarea name="alamat"
                                      class="form-control"
                                      rows="3"><?= htmlspecialchars($data['alamat'] ?? '') ?></textarea>
                        </div>

                        <!-- STATUS -->
                        <div class="mb-3">
                            <label class="form-label">Status</label>

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

                            <a href="data_siswa.php"
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
