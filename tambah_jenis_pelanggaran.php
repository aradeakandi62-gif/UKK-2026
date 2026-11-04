
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$error = '';

// Mengambil data kategori pelanggaran aktif
$kategori = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_pelanggaran_kategori
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

if (!$kategori) {
    die("Gagal mengambil data kategori: " . mysqli_error($koneksi));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $kategori_id = (int) ($_POST['pelanggaran_kategori_id'] ?? 0);
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $poin = (int) ($_POST['poin'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if ($kategori_id <= 0 || $kode === '' || $nama === '' || $poin < 0) {

        $error = 'Lengkapi data dengan benar!';

    } else {

        // Memastikan kategori yang dipilih benar-benar aktif
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_pelanggaran_kategori
             WHERE id = ? AND status_aktif = 1"
        );

        if (!$cek) {
            die("Query pengecekan gagal: " . mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param($cek, "i", $kategori_id);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        $kategori_valid = mysqli_stmt_num_rows($cek) > 0;

        mysqli_stmt_close($cek);

        if (!$kategori_valid) {

            $error = 'Kategori pelanggaran tidak ditemukan atau tidak aktif.';

        } else {

            // Menyimpan jenis pelanggaran
            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO t_pelanggaran
                (
                    pelanggaran_kategori_id,
                    kode,
                    nama,
                    poin,
                    deskripsi,
                    status_aktif
                )
                VALUES (?, ?, ?, ?, ?, 1)"
            );

            if ($stmt) {

                mysqli_stmt_bind_param(
                    $stmt,
                    "issis",
                    $kategori_id,
                    $kode,
                    $nama,
                    $poin,
                    $deskripsi
                );

                if (mysqli_stmt_execute($stmt)) {

                    mysqli_stmt_close($stmt);

                    header('Location: jenis_pelanggaran.php');
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

    // Mengulang pengambilan data kategori setelah POST
    mysqli_data_seek($kategori, 0);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tambah Jenis Pelanggaran</title>

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

            <h2 class="mb-1">Tambah Jenis Pelanggaran</h2>

            <p class="text-muted mb-3">
                Tambahkan data jenis pelanggaran siswa.
            </p>

            <!-- FORM TAMBAH JENIS PELANGGARAN -->
            <div class="card shadow-sm">

                <div class="card-header bg-light fw-bold">
                    Form Tambah Jenis Pelanggaran
                </div>

                <div class="card-body">

                    <?php if ($error !== ''): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST" action="tambah_jenis_pelanggaran.php">

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

                                <option value="">-- Pilih Kategori --</option>

                                <?php while ($row = mysqli_fetch_assoc($kategori)): ?>

                                    <option
                                        value="<?= (int) $row['id'] ?>"
                                        <?= (($_POST['pelanggaran_kategori_id'] ?? '') == $row['id']) ? 'selected' : '' ?>
                                    >

                                        <?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?>

                                    </option>

                                <?php endwhile; ?>

                            </select>

                        </div>

                        <!-- KODE PELANGGARAN -->
                        <div class="mb-3">

                            <label class="form-label">
                                Kode Pelanggaran
                            </label>

                            <input
                                type="text"
                                name="kode"
                                class="form-control"
                                placeholder="Contoh: PLG001"
                                value="<?= htmlspecialchars($_POST['kode'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >

                        </div>

                        <!-- NAMA PELANGGARAN -->
                        <div class="mb-3">

                            <label class="form-label">
                                Nama Pelanggaran
                            </label>

                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                placeholder="Masukkan nama pelanggaran"
                                value="<?= htmlspecialchars($_POST['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >

                        </div>

                        <!-- POIN -->
                        <div class="mb-3">

                            <label class="form-label">
                                Poin
                            </label>

                            <input
                                type="number"
                                name="poin"
                                min="0"
                                class="form-control"
                                placeholder="Masukkan jumlah poin"
                                value="<?= htmlspecialchars($_POST['poin'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
                            ><?= htmlspecialchars($_POST['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

                        </div>

                        <!-- TOMBOL -->
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>

                        <a href="jenis_pelanggaran.php"
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
