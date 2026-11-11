
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$role = strtolower(trim($_SESSION['role'] ?? ''));

if (!in_array($role, ['guru', 'wali_kelas'], true)) {
    header("Location: dashboard.php");
    exit;
}

$user_id = (int)($_SESSION['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: tindakan_pelanggaran.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$tindakan = trim($_POST['tindakan'] ?? '');
$status = trim($_POST['status'] ?? '');

$daftarTindakan = [
    'Teguran lisan',
    'Teguran tertulis',
    'Pembinaan oleh wali kelas',
    'Pemanggilan siswa',
    'Pemanggilan orang tua',
    'Pemanggilan orang tua dan skorsing'
];

$daftarStatus = [
    'Pending',
    'Proses',
    'Selesai'
];

if ($id <= 0 || !in_array($tindakan, $daftarTindakan, true) ||
    !in_array($status, $daftarStatus, true)) {
    header("Location: tindakan_pelanggaran.php?pesan=gagal");
    exit;
}

// Ambil ID guru dari akun login
$stmtGuru = mysqli_prepare($koneksi, "
    SELECT id
    FROM t_guru
    WHERE user_id = ?
    LIMIT 1
");

if (!$stmtGuru) {
    die("Query guru gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmtGuru, "i", $user_id);
mysqli_stmt_execute($stmtGuru);

$resultGuru = mysqli_stmt_get_result($stmtGuru);
$dataGuru = mysqli_fetch_assoc($resultGuru);
mysqli_stmt_close($stmtGuru);

if (!$dataGuru) {
    die("Data guru tidak ditemukan.");
}

$guru_id = (int)$dataGuru['id'];

// Update hanya data milik guru yang sedang login
$stmt = mysqli_prepare($koneksi, "
    UPDATE t_pelanggaran_siswa
    SET tindakan = ?, status = ?
    WHERE id = ?
      AND guru_id = ?
      AND status IN ('Pending', 'Proses')
");

if (!$stmt) {
    die("Query update gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param(
    $stmt,
    "ssii",
    $tindakan,
    $status,
    $id,
    $guru_id
);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    header("Location: tindakan_pelanggaran.php?pesan=berhasil");
    exit;
}

$error = mysqli_stmt_error($stmt);
mysqli_stmt_close($stmt);

die("Gagal memperbarui tindakan: " . $error);
?>
