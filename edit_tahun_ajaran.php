<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Validasi ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit('ID tahun ajaran tidak valid.');
}

// Ambil data tahun ajaran
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

// Deteksi nama kolom berdasarkan struktur tabel
$kolomNama = null;

foreach (['nama_tahun_ajaran', 'nama', 'tahun_ajaran'] as $kolom) {
    if (array_key_exists($kolom, $data)) {
        $kolomNama = $kolom;
        break;
    }
}

if ($kolomNama === null) {
    exit(
        'Kolom nama tahun ajaran tidak ditemukan. ' .
        'Periksa struktur tabel t_tahun_ajaran di phpMyAdmin.'
    );
}

// Fungsi keamanan output
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

$error = '';
$nama = $data[$kolomNama];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_tahun_ajaran'] ?? '');

    if ($nama === '') {
        $error = 'Nama tahun ajaran wajib diisi.';
    } else {
        $sql = "UPDATE t_tahun_ajaran
                SET `$kolomNama` = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $nama, $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            header('Location: tahun_ajaran.php');
            exit;
        }

        $error = 'Gagal mengubah data: ' .
                 mysqli_stmt_error($stmt);

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

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-header">
                    <strong>Edit Tahun Ajaran</strong>
                </div>

                <div class="card-body p-4">

                    <h3 class="mb-4">Edit Tahun Ajaran</h3>

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger">
                            <?= e($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label
                                for="nama_tahun_ajaran"
                                class="form-label"
                            >
                                Nama Tahun Ajaran
                            </label>

                            <input
                                type="text"
                                name="nama_tahun_ajaran"
                                id="nama_tahun_ajaran"
                                class="form-control"
                                placeholder="Contoh: 2026/2027"
                                value="<?= e($nama); ?>"
                                required
                            >
                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Simpan Perubahan
                        </button>

                        <a
                            href="tahun_ajaran.php"
                            class="btn btn-secondary"
                        >
                            Kembali
                        </a>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>
