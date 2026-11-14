
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Pastikan hanya admin yang dapat menghapus kelas
$role = strtolower(trim($_SESSION['role'] ?? ''));

if ($role !== 'admin') {
    $_SESSION['pesan_error'] = 'Kamu tidak memiliki izin menghapus kelas.';
    header("Location: data_kelas.php");
    exit;
}

// Pastikan menggunakan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: data_kelas.php");
    exit;
}

// Ambil ID kelas
$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['pesan_error'] = 'ID kelas tidak valid.';
    header("Location: data_kelas.php");
    exit;
}

// Periksa apakah kelas tersedia
$stmt = mysqli_prepare($koneksi, "
    SELECT id
    FROM t_kelas
    WHERE id = ?
    LIMIT 1
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) === 0) {
    mysqli_stmt_close($stmt);

    $_SESSION['pesan_error'] = 'Data kelas tidak ditemukan.';
    header("Location: data_kelas.php");
    exit;
}

mysqli_stmt_close($stmt);

// Periksa penggunaan kelas pada penempatan siswa
$stmt = mysqli_prepare($koneksi, "
    SELECT COUNT(*)
    FROM t_kelas_siswa
    WHERE kelas_id = ?
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $total_siswa);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Periksa penggunaan kelas pada wali kelas
$stmt = mysqli_prepare($koneksi, "
    SELECT COUNT(*)
    FROM t_wali_kelas
    WHERE kelas_id = ?
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $total_wali);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Jangan hapus jika masih memiliki relasi
if ($total_siswa > 0 || $total_wali > 0) {

    $_SESSION['pesan_error'] =
        "Kelas tidak dapat dihapus karena masih digunakan. " .
        "Penempatan siswa: $total_siswa data, " .
        "Wali kelas: $total_wali data. " .
        "Data terkait tetap aman dan tidak dihapus.";

    header("Location: data_kelas.php");
    exit;
}

// Hapus hanya jika tidak memiliki relasi
$stmt = mysqli_prepare($koneksi, "
    DELETE FROM t_kelas
    WHERE id = ?
");

if (!$stmt) {
    $_SESSION['pesan_error'] = 'Gagal menyiapkan proses penghapusan.';
    header("Location: data_kelas.php");
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt) &&
    mysqli_stmt_affected_rows($stmt) > 0) {

    $_SESSION['pesan_sukses'] = 'Data kelas berhasil dihapus.';

} else {

    $_SESSION['pesan_error'] =
        'Data kelas gagal dihapus. Periksa kembali hubungan data kelas.';

}

mysqli_stmt_close($stmt);

header("Location: data_kelas.php");
exit;
