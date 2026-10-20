<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: jenis_pelanggaran.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: jenis_pelanggaran.php');
    exit;
}

$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_pelanggaran WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: jenis_pelanggaran.php');
    exit;
}

$error = mysqli_stmt_error($stmt);
mysqli_stmt_close($stmt);

die('Gagal menghapus data. Mungkin data ini sedang digunakan pada tabel pelanggaran siswa. Detail: '
    . htmlspecialchars($error));
