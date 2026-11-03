
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Pastikan request menggunakan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: data_guru.php");
    exit;
}

// Ambil ID guru
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    header("Location: data_guru.php?pesan=id_tidak_valid");
    exit;
}

// Periksa apakah guru masih menjadi wali kelas
$cek = mysqli_prepare($koneksi, "
    SELECT COUNT(*) AS total
    FROM t_wali_kelas
    WHERE guru_id = ?
");

mysqli_stmt_bind_param($cek, "i", $id);
mysqli_stmt_execute($cek);

$hasil = mysqli_stmt_get_result($cek);
$data = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($cek);

// Jika masih digunakan sebagai wali kelas
if ($data['total'] > 0) {
    header("Location: data_guru.php?pesan=guru_digunakan");
    exit;
}

// Hapus data guru
$stmt = mysqli_prepare($koneksi, "
    DELETE FROM t_guru WHERE id = ?
");

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: data_guru.php?pesan=berhasil");
    } else {
        header("Location: data_guru.php?pesan=data_tidak_ditemukan");
    }

} else {

    header("Location: data_guru.php?pesan=gagal");

}

mysqli_stmt_close($stmt);
exit;
?>
