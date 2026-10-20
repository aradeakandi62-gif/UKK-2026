
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: wali_kelas.php');
    exit;
}

$stmt = mysqli_prepare($koneksi, "DELETE FROM t_wali_kelas WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    header('Location: wali_kelas.php');
    exit;
}

echo "Gagal menghapus data wali kelas. Data mungkin masih digunakan tabel lain.";
echo '<br><a href="wali_kelas.php">Kembali</a>';
?>
