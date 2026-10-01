<?php
include 'config/koneksi.php';

if (isset($_POST['simpan'])) {

    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $status_aktif = $_POST['status_aktif'];

    $query = mysqli_query($koneksi, "INSERT INTO t_siswa 
        (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif)
        VALUES 
        ('$nis', '$nisn', '$nama', '$jenis_kelamin', '$tanggal_lahir', '$alamat', '$status_aktif')
    ");

    if ($query) {
        header("Location: data_siswa.php");
        exit;
    } else {
        echo "Data gagal ditambahkan: " . mysqli_error($koneksi);
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
    <div class="bg-dark text-white p-3 min-vh-100"
         style="width: 230px;">

        <h4 class="mb-4">
            My Website
        </h4>

        <div class="nav flex-column">

            <a href="dashboard.php"
               class="nav-link text-white mb-2">
                Dashboard
            </a>

            <a href="data_siswa.php"
               class="nav-link text-white mb-2">
                Data Siswa
            </a>

            <a href="data_guru.php"
               class="nav-link text-white mb-2">
                Data Guru
            </a>

            <a href="kelas.php"
               class="nav-link text-white mb-2">
                Kelas
            </a>

            <a href="laporan.php"
               class="nav-link text-white mb-2">
                Laporan
            </a>

            <a href="about.php"
               class="nav-link text-white mb-4">
                About Me
            </a>

            <hr>

            <a href="logout.php"
               class="nav-link text-danger">
                Logout
            </a>

        </div>

    </div>


    <!-- CONTENT -->
    <div class="flex-grow-1 p-4">

        <h2>Tambah Siswa</h2>

        <p class="text-muted">
            Tambahkan data siswa baru
        </p>


        <div class="card">

            <div class="card-header">

                <strong>
                    Form Tambah Siswa
                </strong>

            </div>


            <div class="card-body">

                <form method="POST">


                    <!-- NIS -->
                    <div class="mb-3">

                        <label class="form-label">
                            NIS
                        </label>

                        <input
                            type="text"
                            name="nis"
                            class="form-control"
                            placeholder="Masukkan NIS"
                            required>

                    </div>


                    <!-- NISN -->
                    <div class="mb-3">

                        <label class="form-label">
                            NISN
                        </label>

                        <input
                            type="text"
                            name="nisn"
                            class="form-control"
                            placeholder="Masukkan NISN"
                            required>

                    </div>


                    <!-- NAMA -->
                    <div class="mb-3">

                        <label class="form-label">
                            Nama Siswa
                        </label>

                        <input
                            type="text"
                            name="nama"
                            class="form-control"
                            placeholder="Masukkan nama siswa"
                            required>

                    </div>


                    <!-- JENIS KELAMIN -->
                    <div class="mb-3">

                        <label class="form-label">
                            Jenis Kelamin
                        </label>

                        <select
                            name="jenis_kelamin"
                            class="form-select"
                            required>

                            <option value="">
                                -- Pilih Jenis Kelamin --
                            </option>

                            <option value="L">
                                Laki-laki
                            </option>

                            <option value="P">
                                Perempuan
                            </option>

                        </select>

                    </div>


                    <!-- TANGGAL LAHIR -->
                    <div class="mb-3">

                        <label class="form-label">
                            Tanggal Lahir
                        </label>

                        <input
                            type="date"
                            name="tanggal_lahir"
                            class="form-control"
                            required>

                    </div>


                    <!-- ALAMAT -->
                    <div class="mb-3">

                        <label class="form-label">
                            Alamat
                        </label>

                        <textarea
                            name="alamat"
                            class="form-control"
                            rows="3"
                            placeholder="Masukkan alamat siswa"></textarea>

                    </div>


                    <!-- STATUS -->
                    <div class="mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status_aktif"
                            class="form-select"
                            required>

                            <option value="1">
                                Aktif
                            </option>

                            <option value="0">
                                Tidak Aktif
                            </option>

                        </select>

                    </div>


                    <!-- BUTTON -->
                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            name="simpan"
                            class="btn btn-primary">
                            Simpan
                        </button>

                        <a
                            href="data_siswa.php"
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