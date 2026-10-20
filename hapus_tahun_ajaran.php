
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit('ID tahun ajaran tidak valid.');
}

$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_tahun_ajaran WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: tahun_ajaran.php');
    exit;
}

$error = mysqli_stmt_error($stmt);
mysqli_stmt_close($stmt);

exit(
    'Gagal menghapus tahun ajaran. Data mungkin masih digunakan oleh data lain. '
    . htmlspecialchars($error)
);
?>
