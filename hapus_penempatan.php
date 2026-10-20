<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Location: penempatan_siswa.php');
    exit;
}

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: penempatan_siswa.php');
    exit;
}

$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_kelas_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    header('Location: penempatan_siswa.php');
    exit;
}

$error = mysqli_stmt_error($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Gagal Menghapus Penempatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="container py-4">
    <div class="alert alert-danger">
        Gagal menghapus data penempatan:
        <?= htmlspecialchars($error) ?>
    </div>
    <a href="penempatan_siswa.php" class="btn btn-secondary">Kembali</a>
</body>
</html>