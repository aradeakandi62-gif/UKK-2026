<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Pastikan menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: wali_kelas.php");
    exit;
}

// Ambil ID wali kelas
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id <= 0) {
    echo "<script>
        alert('ID wali kelas tidak valid!');
        window.location='wali_kelas.php';
    </script>";
    exit;
}

// Hapus berdasarkan primary key tabel t_wali_kelas
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_wali_kelas WHERE id = ?"
);

if (!$stmt) {
    die("Gagal menyiapkan query: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {

    if (mysqli_stmt_affected_rows($stmt) > 0) {

        echo "<script>
            alert('Data wali kelas berhasil dihapus!');
            window.location='wali_kelas.php';
        </script>";

    } else {

        echo "<script>
            alert('Data wali kelas tidak ditemukan!');
            window.location='wali_kelas.php';
        </script>";

    }

} else {

    echo "<script>
        alert('Gagal menghapus data wali kelas!');
        window.location='wali_kelas.php';
    </script>";

}

mysqli_stmt_close($stmt);
exit;
?>
