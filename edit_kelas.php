<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: data_kelas.php");
    exit;
}

// Ambil data kelas berdasarkan ID
$stmt = mysqli_prepare($koneksi, "SELECT * FROM t_kelas WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Data kelas tidak ditemukan.";
    exit;
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $tingkat = trim($_POST['tingkat']);
    $jurusan = trim($_POST['jurusan']);
    $status_aktif = (int) $_POST['status_aktif'];

    if ($nama === '' || $tingkat === '' || $jurusan === '') {
        $pesan = "Semua kolom wajib diisi.";
    } else {
        $update = mysqli_prepare(
            $koneksi,
            "UPDATE t_kelas
             SET nama = ?, tingkat = ?, jurusan = ?, status_aktif = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $update,
            "sssii",
            $nama,
            $tingkat,
            $jurusan,
            $status_aktif,
            $id
        );

        if (mysqli_stmt_execute($update)) {
            header("Location: data_kelas.php");
            exit;
        } else {
            $pesan = "Gagal mengubah data kelas.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Edit Kelas</h4>
        </div>

        <div class="card-body">

            <?php if (!empty($pesan)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($pesan) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Kelas</label>
                    <input type="text" name="nama" class="form-control"
                           value="<?= htmlspecialchars($data['nama']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tingkat</label>
                    <select name="tingkat" class="form-select" required>
                        <?php foreach (['X', 'XI', 'XII'] as $tingkat): ?>
                            <option value="<?= $tingkat ?>"
                                <?= $data['tingkat'] === $tingkat ? 'selected' : '' ?>>
                                <?= $tingkat ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jurusan</label>
                    <input type="text" name="jurusan" class="form-control"
                           value="<?= htmlspecialchars($data['jurusan']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status_aktif" class="form-select" required>
                        <option value="1"
                            <?= $data['status_aktif'] == 1 ? 'selected' : '' ?>>
                            Aktif
                        </option>
                        <option value="0"
                            <?= $data['status_aktif'] == 0 ? 'selected' : '' ?>>
                            Tidak Aktif
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Simpan Perubahan
                </button>

                <a href="data_kelas.php" class="btn btn-secondary">
                    Kembali
                </a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
```