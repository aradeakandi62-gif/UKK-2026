<?php
include "config/koneksi.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');

    if ($nama === '') {
        $error = "Nama kelas wajib diisi.";
    } else {
        $tingkat = 'X';
        $jurusan = 'RPL';
        $status_aktif = 1;

        $stmt = $koneksi->prepare(
            "INSERT INTO t_kelas
            (nama, tingkat, jurusan, status_aktif)
            VALUES (?, ?, ?, ?)"
        );

        if (!$stmt) {
            $error = "Gagal menyiapkan query: " . $koneksi->error;
        } else {
            $stmt->bind_param(
                "sssi",
                $nama,
                $tingkat,
                $jurusan,
                $status_aktif
            );

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: data_kelas.php");
                exit;
            } else {
                $error = "Data gagal disimpan: " . $stmt->error;
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Kelas</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body>
<div class="container-fluid">
    <div class="row">
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


        <div class="col-md-10 p-4">
            <h2>Tambah Kelas</h2>
            <p class="text-secondary">Tambahkan data kelas baru</p>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header fw-bold">
                    Form Tambah Kelas
                </div>

                <div class="card-body">
                    <form method="POST" action="">

                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>
                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                placeholder="Contoh: X RPL 1"
                                required>
                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary">
                            Simpan Kelas
                        </button>

                        <a
                            href="data_kelas.php"
                            class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>