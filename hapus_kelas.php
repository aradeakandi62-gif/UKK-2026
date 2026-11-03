
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Pastikan hanya menggunakan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: data_kelas.php");
    exit;
}

// Ambil ID kelas
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id <= 0) {
    header("Location: data_kelas.php?pesan=id_tidak_valid");
    exit;
}

// Periksa apakah kelas tersedia
$stmt = mysqli_prepare($koneksi, "
    SELECT id FROM t_kelas WHERE id = ?
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) == 0) {
    mysqli_stmt_close($stmt);
    header("Location: data_kelas.php?pesan=data_tidak_ditemukan");
    exit;
}

mysqli_stmt_close($stmt);

// Periksa relasi pada tabel t_kelas_siswa
$stmt1 = mysqli_prepare($koneksi, "
    SELECT COUNT(*) FROM t_kelas_siswa
    WHERE kelas_id = ?
");

mysqli_stmt_bind_param($stmt1, "i", $id);
mysqli_stmt_execute($stmt1);
mysqli_stmt_bind_result($stmt1, $total_siswa);
mysqli_stmt_fetch($stmt1);
mysqli_stmt_close($stmt1);

// Periksa relasi pada tabel t_wali_kelas
$stmt2 = mysqli_prepare($koneksi, "
    SELECT COUNT(*) FROM t_wali_kelas
    WHERE kelas_id = ?
");

mysqli_stmt_bind_param($stmt2, "i", $id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_bind_result($stmt2, $total_wali);
mysqli_stmt_fetch($stmt2);
mysqli_stmt_close($stmt2);

// Jangan hapus jika kelas masih digunakan
if ($total_siswa > 0 || $total_wali > 0) {
    header("Location: data_kelas.php?pesan=kelas_digunakan");
    exit;
}

// Hapus kelas
$stmt3 = mysqli_prepare($koneksi, "
    DELETE FROM t_kelas WHERE id = ?
");

mysqli_stmt_bind_param($stmt3, "i", $id);

if (mysqli_stmt_execute($stmt3)) {

    if (mysqli_stmt_affected_rows($stmt3) > 0) {
        mysqli_stmt_close($stmt3);
        header("Location: data_kelas.php?pesan=berhasil");
        exit;
    }

} else {
    mysqli_stmt_close($stmt3);
    header("Location: data_kelas.php?pesan=gagal");
    exit;
}

mysqli_stmt_close($stmt3);

header("Location: data_kelas.php?pesan=gagal");
exit;
?>
