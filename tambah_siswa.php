<?php
include 'config/koneksi.php';

$error = '';

if (isset($_POST['simpan'])) {

    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nisn = mysqli_real_escape_string($koneksi, $_POST['nisn']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $status_aktif = (int) $_POST['status_aktif'];

    $query = mysqli_query($koneksi, "INSERT INTO t_siswa
        (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif)
        VALUES
        ('$nis', '$nisn', '$nama', '$jenis_kelamin',
         '$tanggal_lahir', '$alamat', '$status_aktif')");

    if ($query) {
        header("Location: data_siswa.php");
        exit;
    } else {
        $error = "Data gagal ditambahkan: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Siswa</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body>

<div class="d-flex">

    <!-- SIDEBAR -->
<div class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">

    <h4 class="text-white mb-4">My Website</h4>

    <ul class="nav flex-column gap-2">

        <li class="nav-item">
            <a href="dashboard.php" class="nav-link text-white text-decoration-none">
                Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a href="kelola_siswa.php" class="nav-link text-white text-decoration-none">
                Data Siswa
            </a>
        </li>

        <li class="nav-item">
            <a href="kelola_guru.php" class="nav-link text-white text-decoration-none">
                Data Guru
            </a>
        </li>

        <li class="nav-item">
            <a href="kelola_kelas.php" class="nav-link text-white text-decoration-none">
                Kelas
            </a>
        </li>

        <li class="nav-item">
            <a href="tahun_ajaran.php" class="nav-link text-white text-decoration-none">
                Tahun Ajaran
            </a>
        </li>

        <li class="nav-item">
            <a href="penempatan_siswa.php" class="nav-link text-white text-decoration-none">
                Penempatan Siswa
            </a>
        </li>

        <li class="nav-item">
            <a href="wali_kelas.php" class="nav-link text-white text-decoration-none">
                Wali Kelas
            </a>
        </li>

        <li class="nav-item">
            <a href="kelola_jenis_pelanggaran.php" class="nav-link text-white text-decoration-none">
                Kategori Pelanggaran
            </a>
        </li>

        <li class="nav-item">
            <a href="jenis_pelanggaran.php" class="nav-link text-white text-decoration-none">
               Jenis Pelanggaran 
            </a>
        </li>

        <li class="nav-item">
            <a href="laporan.php" class="nav-link text-white text-decoration-none">
                Laporan
            </a>
        </li>

        <li class="nav-item">
            <a href="about.php" class="nav-link text-white text-decoration-none">
                About Me
            </a>
        </li>

        <li class="nav-item mt-2">
            <a href="logout.php" class="nav-link text-danger text-decoration-none">
                Logout
            </a>
        </li>

    </ul>

</div>


    <!-- CONTENT -->
    <div class="flex-grow-1 p-4">

        <h2>Tambah Siswa</h2>

        <p class="text-muted">Tambahkan data siswa baru</p>

        <?php if ($error != ''): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="card">

            <div class="card-header">
                <strong>Form Tambah Siswa</strong>
            </div>

            <div class="card-body">

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label">NIS</label>
                        <input type="text" name="nis"
                               class="form-control"
                               placeholder="Masukkan NIS" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">NISN</label>
                        <input type="text" name="nisn"
                               class="form-control"
                               placeholder="Masukkan NISN" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Siswa</label>
                        <input type="text" name="nama"
                               class="form-control"
                               placeholder="Masukkan nama siswa" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin"
                                class="form-select" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Masukkan alamat siswa"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status_aktif"
                                class="form-select" required>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>

                    <!-- TOMBOL -->
                    <div class="d-flex gap-2">

                        <button type="submit" name="simpan"
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

    </div>
</div>

</body>
</html>
