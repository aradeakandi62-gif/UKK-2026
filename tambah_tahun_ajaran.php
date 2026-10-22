<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

$error = '';
$nama = trim($_POST['nama_tahun_ajaran'] ?? '');

// Deteksi kolom nama tahun ajaran yang tersedia
$kolomNama = null;

$resultKolom = mysqli_query(
    $koneksi,
    "SHOW COLUMNS FROM t_tahun_ajaran"
);

if (!$resultKolom) {
    exit('Gagal membaca struktur tabel: ' . e(mysqli_error($koneksi)));
}

$kolomTersedia = [];

while ($kolom = mysqli_fetch_assoc($resultKolom)) {
    $kolomTersedia[] = $kolom['Field'];
}

foreach (['nama_tahun_ajaran', 'nama', 'tahun_ajaran'] as $calon) {
    if (in_array($calon, $kolomTersedia, true)) {
        $kolomNama = $calon;
        break;
    }
}

if ($kolomNama === null) {
    exit(
        'Kolom nama tahun ajaran tidak ditemukan. ' .
        'Silakan periksa struktur tabel t_tahun_ajaran di phpMyAdmin.'
    );
}

// Simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($nama === '') {
        $error = 'Nama tahun ajaran wajib diisi.';
    } else {
        $sql = "INSERT INTO t_tahun_ajaran (`$kolomNama`) VALUES (?)";

        $stmt = mysqli_prepare($koneksi, $sql);

        if (!$stmt) {
            $error = 'Query gagal: ' . mysqli_error($koneksi);
        } else {
            mysqli_stmt_bind_param($stmt, 's', $nama);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                header('Location: tahun_ajaran.php');
                exit;
            }

            $error = 'Gagal menyimpan data: ' .
                     mysqli_stmt_error($stmt);

            mysqli_stmt_close($stmt);
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
                    <strong>Tambah Tahun Ajaran</strong>
                </div>

                <div class="card-body p-4">

                    <h3 class="mb-2">Tambah Tahun Ajaran</h3>

                    <p class="text-secondary">
                        Tambahkan tahun ajaran baru ke sistem.
                    </p>

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
                            Simpan Tahun Ajaran
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
