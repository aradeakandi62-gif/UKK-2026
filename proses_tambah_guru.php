
<?php
// proses_tambah_guru.php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah_guru.php');
    exit;
}

$nip = trim($_POST['nip'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$status_aktif = (int) ($_POST['status_aktif'] ?? 1);
$user_id = (int) ($_POST['user_id'] ?? 0);

if ($nip === '' || $nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('NIP, nama, atau email tidak valid. Silakan kembali dan periksa form.');
}

if (!in_array($status_aktif, [0, 1], true)) {
    exit('Status guru tidak valid.');
}

if ($user_id < 1) {
    exit('User ID wajib diisi dengan ID yang tersedia di tabel t_users.');
}

$sql = "INSERT INTO t_guru
        (nip, nama, email, status_aktif, user_id)
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    exit('Gagal menyiapkan query: ' . mysqli_error($koneksi));
}

mysqli_stmt_bind_param(
    $stmt,
    'sssii',
    $nip,
    $nama,
    $email,
    $status_aktif,
    $user_id
);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: data_guru.php');
    exit;
}

$error = mysqli_stmt_error($stmt);
mysqli_stmt_close($stmt);

exit('Gagal menambahkan data guru: ' . htmlspecialchars($error));
?>
