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
    <!-- SIDEBAR -->
    <?php include 'layout/sidebar.php'; ?>

    <!-- KONTEN -->
    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

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