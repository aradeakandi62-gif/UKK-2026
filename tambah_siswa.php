
<?php
include 'config/koneksi.php';

$error = '';

if (isset($_POST['simpan'])) {

    $nis = trim($_POST['nis'] ?? '');
    $nisn = trim($_POST['nisn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $status_aktif = (int) ($_POST['status_aktif'] ?? 1);

    // Validasi NIS
    if (!ctype_digit($nis) || strlen($nis) != 15) {
        $error = "NIS harus tepat 15 digit angka!";
    }

    // Validasi NISN
    elseif (!ctype_digit($nisn) || strlen($nisn) != 10) {
        $error = "NISN harus tepat 10 digit angka!";
    }

    elseif ($nama == '' || $jenis_kelamin == '' || $tanggal_lahir == '') {
        $error = "Semua data wajib diisi!";
    }

    else {

        $stmt = mysqli_prepare($koneksi, "
            INSERT INTO t_siswa
            (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssi",
            $nis,
            $nisn,
            $nama,
            $jenis_kelamin,
            $tanggal_lahir,
            $alamat,
            $status_aktif
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: data_siswa.php");
            exit;
        } else {
            $error = "Data gagal ditambahkan: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tambah Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="layout-wrapper">


    <!-- SIDEBAR -->
    <?php include 'layout/sidebar.php'; ?>

    <!-- KONTEN UTAMA -->
    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <h2>Tambah siswa</h2>
        <p class="text-secondary">Tambahkan data siswa baru.</p>

        <!-- PESAN ERROR -->
        <?php if ($error != ''): ?>

            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <!-- FORM TAMBAH SISWA -->
        <div class="card">

            <div class="card-header">
                <strong>Form Tambah Siswa</strong>
            </div>

            <div class="card-body">

                <form method="POST" action="">

                    <!-- NIS -->
                    <div class="mb-3">

                        <label class="form-label">NIS (15 Digit)</label>

                        <input type="text"
                               name="nis"
                               class="form-control"
                               placeholder="Masukkan 15 digit NIS"
                               maxlength="15"
                               minlength="15"
                               pattern="[0-9]{15}"
                               inputmode="numeric"
                               value="<?= htmlspecialchars($_POST['nis'] ?? '') ?>"
                               required>

                        <small class="text-muted">
                            NIS harus tepat 15 digit angka.
                        </small>

                    </div>

                    <!-- NISN -->
                    <div class="mb-3">

                        <label class="form-label">NISN (10 Digit)</label>

                        <input type="text"
                               name="nisn"
                               class="form-control"
                               placeholder="Masukkan 10 digit NISN"
                               maxlength="10"
                               minlength="10"
                               pattern="[0-9]{10}"
                               inputmode="numeric"
                               value="<?= htmlspecialchars($_POST['nisn'] ?? '') ?>"
                               required>

                        <small class="text-muted">
                            NISN harus tepat 10 digit angka.
                        </small>

                    </div>

                    <!-- NAMA SISWA -->
                    <div class="mb-3">

                        <label class="form-label">Nama Siswa</label>

                        <input type="text"
                               name="nama"
                               class="form-control"
                               placeholder="Masukkan nama siswa"
                               value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                               required>

                    </div>

                    <!-- JENIS KELAMIN -->
                    <div class="mb-3">

                        <label class="form-label">Jenis Kelamin</label>

                        <select name="jenis_kelamin"
                                class="form-select"
                                required>

                            <option value="">-- Pilih Jenis Kelamin --</option>

                            <option value="L">Laki-laki</option>

                            <option value="P">Perempuan</option>

                        </select>

                    </div>

                    <!-- TANGGAL LAHIR -->
                    <div class="mb-3">

                        <label class="form-label">Tanggal Lahir</label>

                        <input type="date"
                               name="tanggal_lahir"
                               class="form-control"
                               value="<?= htmlspecialchars($_POST['tanggal_lahir'] ?? '') ?>"
                               required>

                    </div>

                    <!-- ALAMAT -->
                    <div class="mb-3">

                        <label class="form-label">Alamat</label>

                        <textarea name="alamat"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Masukkan alamat siswa"><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>

                    </div>

                    <!-- STATUS -->
                    <div class="mb-4">

                        <label class="form-label">Status</label>

                        <select name="status_aktif"
                                class="form-select"
                                required>

                            <option value="1">Aktif</option>

                            <option value="0">Tidak Aktif</option>

                        </select>

                    </div>

                    <!-- TOMBOL SIMPAN DAN KEMBALI -->
                    <div class="d-flex gap-2">

                        <button type="submit"
                                name="simpan"
                                class="btn btn-primary">

                            Simpan Siswa

                        </button>

                        <a href="data_siswa.php"
                           class="btn btn-secondary">

                            Kembali

                        </a>

                    </div>

                </form>

            </div>
        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
