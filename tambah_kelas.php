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

        <div class="col-md-2 bg-dark text-white min-vh-100 p-3">
            <h4>My Website</h4>

            <a href="dashboard.php" class="d-block text-white my-3">
                Dashboard
            </a>

            <a href="data_siswa.php" class="d-block text-white my-3">
                Data Siswa
            </a>

            <a href="data_guru.php" class="d-block text-white my-3">
                Data Guru
            </a>

            <a href="data_kelas.php" class="d-block text-white my-3">
                Data Kelas
            </a>

            <a href="logout.php" class="d-block text-danger my-3">
                Logout
            </a>
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