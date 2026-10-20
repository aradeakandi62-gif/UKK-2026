
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: kategori_pelanggaran.php");
    exit;
}

$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_pelanggaran_kategori WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: kategori_pelanggaran.php");
    exit;
}

$error = mysqli_stmt_error($stmt);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Gagal Menghapus Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <div class="alert alert-danger">
        Kategori tidak dapat dihapus. Kemungkinan masih digunakan oleh data
        pelanggaran lain.
        <hr>
        <?= htmlspecialchars($error) ?>
    </div>
    <a href="kategori_pelanggaran.php" class="btn btn-secondary">
        Kembali
    </a>
</div>
</body>
</html>
