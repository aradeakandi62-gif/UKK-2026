<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: penempatan_siswa.php");
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    header("Location: penempatan_siswa.php");
    exit;
}

$stmt = mysqli_prepare($koneksi, "
    DELETE FROM t_kelas_siswa
    WHERE id = ?
");

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: penempatan_siswa.php");
    exit;
} else {
    die("Gagal menghapus data: " . htmlspecialchars(mysqli_stmt_error($stmt)));
}
?>