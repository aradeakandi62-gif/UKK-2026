
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Pastikan hanya menerima request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: penempatan_siswa.php");
    exit;
}

// Ambil ID penempatan
$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    header("Location: penempatan_siswa.php");
    exit;
}

// Hapus data berdasarkan ID
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_kelas_siswa WHERE id = ?"
);

if (!$stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    header("Location: penempatan_siswa.php");
    exit;
} else {
    $error = mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);

    die("Gagal menghapus data: " . htmlspecialchars($error));
}
?>
