
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Ambil ID
$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: jenis_pelanggaran.php');
    exit;
}

$error = '';

// Ambil data jenis pelanggaran
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_pelanggaran WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    die('Data jenis pelanggaran tidak ditemukan.');
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $kategori_id = (int) ($_POST['pelanggaran_kategori_id'] ?? 0);
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $poin = (int) ($_POST['poin'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if ($kategori_id <= 0 || $kode === '' || $nama === '' || $poin < 0) {

        $error = 'Lengkapi data dengan benar!';

    } else {

        $stmt = mysqli_prepare($koneksi, "
            UPDATE t_pelanggaran
            SET
                pelanggaran_kategori_id = ?,
                kode = ?,
                nama = ?,
                poin = ?,
                deskripsi = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "issisi",
            $kategori_id,
            $kode,
            $nama,
            $poin,
            $deskripsi,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_close($stmt);

            header('Location: jenis_pelanggaran.php');
            exit;

        } else {

            $error = 'Gagal mengubah data: ' .
                mysqli_stmt_error($stmt);

            mysqli_stmt_close($stmt);
        }
    }

    // Simpan input jika terjadi kesalahan
    $data['pelanggaran_kategori_id'] = $kategori_id;
    $data['kode'] = $kode;
    $data['nama'] = $nama;
    $data['poin'] = $poin;
    $data['deskripsi'] = $deskripsi;
}

// Ambil kategori aktif
$kategori = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_pelanggaran_kategori
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

if (!$kategori) {
    die('Gagal mengambil kategori: ' . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Jenis Pelanggaran</title>

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

            <h2 class="mb-1">Edit Jenis Pelanggaran</h2>

            <p class="text-muted">
                Ubah data jenis pelanggaran siswa.
            </p>

            <!-- CARD FORM -->
            <div class="card shadow-sm">

                <div class="card-header bg-light">
                    <strong>Form Edit Jenis Pelanggaran</strong>
                </div>

                <div class="card-body p-4">

                    <!-- PESAN ERROR -->
                    <?php if ($error !== ''): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST">

                        <!-- KATEGORI -->
                        <div class="mb-3">

                            <label class="form-label">
                                Kategori Pelanggaran
                            </label>

                            <select
                                name="pelanggaran_kategori_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    -- Pilih Kategori --
                                </option>

                                <?php while ($k = mysqli_fetch_assoc($kategori)): ?>

                                    <option
                                        value="<?= (int) $k['id'] ?>"
                                        <?= ((int) $k['id'] === (int) $data['pelanggaran_kategori_id']) ? 'selected' : '' ?>
                                    >

                                        <?= htmlspecialchars($k['nama']) ?>

                                    </option>

                                <?php endwhile; ?>

                            </select>

                        </div>

                        <!-- KODE -->
                        <div class="mb-3">

                            <label class="form-label">
                                Kode Pelanggaran
                            </label>

                            <input
                                type="text"
                                name="kode"
                                class="form-control"
                                placeholder="Masukkan kode pelanggaran"
                                value="<?= htmlspecialchars($data['kode']) ?>"
                                required
                            >

                        </div>

                        <!-- NAMA -->
                        <div class="mb-3">

                            <label class="form-label">
                                Nama Pelanggaran
                            </label>

                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                placeholder="Masukkan nama pelanggaran"
                                value="<?= htmlspecialchars($data['nama']) ?>"
                                required
                            >

                        </div>

                        <!-- POIN -->
                        <div class="mb-3">

                            <label class="form-label">
                                Poin Pelanggaran
                            </label>

                            <input
                                type="number"
                                name="poin"
                                min="0"
                                class="form-control"
                                placeholder="Masukkan jumlah poin"
                                value="<?= (int) $data['poin'] ?>"
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
                                placeholder="Masukkan deskripsi pelanggaran"
                            ><?= htmlspecialchars($data['deskripsi'] ?? '') ?></textarea>

                        </div>

                        <!-- TOMBOL -->
                        <div class="d-flex gap-2">

                            <button type="submit" class="btn btn-primary">
                                Simpan Perubahan
                            </button>

                            <a href="jenis_pelanggaran.php"
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
