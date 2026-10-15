```php
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

if (!isset($_GET['id']) || !ctype_digit($_GET['id']) || (int) $_GET['id'] <= 0) {
    header("Location: data_kelas.php");
    exit;
}

$id = (int) $_GET['id'];

// Periksa apakah kelas digunakan oleh tabel lain
$cek = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS jumlah
     FROM t_kelas_siswa
     WHERE kelas_id = ?"
);

mysqli_stmt_bind_param($cek, "i", $id);
mysqli_stmt_execute($cek);

$hasil = mysqli_stmt_get_result($cek);
$row = mysqli_fetch_assoc($hasil);

if ($row['jumlah'] > 0) {
    echo '<div style="margin:20px">';
    echo '<div class="alert alert-danger">';
    echo 'Kelas tidak bisa dihapus karena masih digunakan oleh data siswa.';
    echo '</div>';
    echo '<a href="data_kelas.php" class="btn btn-secondary">Kembali</a>';
    echo '</div>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">';
    exit;
}

// Hapus kelas
$hapus = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_kelas WHERE id = ?"
);

mysqli_stmt_bind_param($hapus, "i", $id);

if (mysqli_stmt_execute($hapus)) {
    header("Location: data_kelas.php");
    exit;
}

echo "Gagal menghapus kelas. Kelas mungkin masih digunakan oleh tabel lain.";
?>
```