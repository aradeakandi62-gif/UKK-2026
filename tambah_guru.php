<?php

include 'config/koneksi.php';

if (isset($_POST['simpan'])) {

    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];

    $status_aktif = 1;

    $query = mysqli_query($koneksi, "INSERT INTO t_guru
        (nip, nama, email, status_aktif)
        VALUES
        ('$nip', '$nama', '$email', '$status_aktif')
    ");

    if ($query) {
        header("Location: data_guru.php");
        exit;
    } else {
        echo "Data guru gagal ditambahkan: " . mysqli_error($koneksi);
    }

}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Guru</title>

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

        <h2>Tambah Guru</h2>

        <p class="text-muted">
            Tambahkan data guru baru
        </p>

        <div class="card">

            <div class="card-header">

                <strong>
                    Form Tambah Guru
                </strong>

            </div>

            <div class="card-body">

                <form method="POST">

                    <!-- NIP -->
                    <div class="mb-3">

                        <label class="form-label">
                            NIP
                        </label>

                        <input
                            type="text"
                            name="nip"
                            class="form-control"
                            placeholder="Masukkan NIP"
                            required>

                    </div>


                    <!-- NAMA -->
                    <div class="mb-3">

                        <label class="form-label">
                            Nama Guru
                        </label>

                        <input
                            type="text"
                            name="nama"
                            class="form-control"
                            placeholder="Masukkan nama guru"
                            required>

                    </div>


                    <!-- EMAIL -->
                    <div class="mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Masukkan email guru"
                            required>

                    </div>


                    <!-- USER -->
                    <div class="mb-3">

                        <label class="form-label">
                            User
                        </label>

                        <select
                            name="user_id"
                            class="form-select"
                            required>

                            <option value="">
                                -- Pilih User --
                            </option>

                            <?php while ($user = mysqli_fetch_assoc($users)) { ?>

                                <option value="<?= $user['id']; ?>">

                                    <?= htmlspecialchars($user['name']); ?>

                                    -
                                    <?= htmlspecialchars($user['email']); ?>

                                </option>

                            <?php } ?>

                        </select>

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
                            href="data_guru.php"
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