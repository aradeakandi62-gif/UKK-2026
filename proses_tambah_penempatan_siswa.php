
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah_penempatan_siswa.php');
    exit;
}

$siswa_id = (int)($_POST['siswa_id'] ?? 0);
$kelas_id = (int)($_POST['kelas_id'] ?? 0);
$tahun_ajaran_id = (int)($_POST['tahun_ajaran_id'] ?? 0);
$tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
$tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
$status_aktif = (int)($_POST['status_aktif'] ?? 1);

if (
    $siswa_id <= 0 || $kelas_id <= 0 ||
    $tahun_ajaran_id <= 0 || $tanggal_mulai === '' ||
    !in_array($status_aktif, [0, 1], true)
) {
    exit('Data wajib belum lengkap. <a href="tambah_penempatan_siswa.php">Kembali</a>');
}

if ($tanggal_selesai !== '' && $tanggal_selesai < $tanggal_mulai) {
    exit('Tanggal selesai tidak boleh sebelum tanggal mulai.');
}

if ($tanggal_selesai === '') {
    $tanggal_selesai = null;
}

$stmt = mysqli_prepare($koneksi,
    "INSERT INTO t_kelas_siswa
    (siswa_id, tahun_ajaran_id, kelas_id, tanggal_mulai,
     tanggal_selesai, status_aktif)
    VALUES (?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    exit('Gagal menyiapkan query: '.mysqli_error($koneksi));
}

mysqli_stmt_bind_param(
    $stmt, 'iiissi',
    $siswa_id, $tahun_ajaran_id, $kelas_id,
    $tanggal_mulai, $tanggal_selesai, $status_aktif
);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: penempatan_siswa.php');
    exit;
}

echo 'Gagal menyimpan penempatan: '.htmlspecialchars(mysqli_stmt_error($stmt));
mysqli_stmt_close($stmt);
?>
