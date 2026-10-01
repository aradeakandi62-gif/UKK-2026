<?php

include 'config/koneksi.php';

// Ambil ID siswa dari URL
$id = $_GET['id'] ?? 0;

// Hapus data siswa
$query = mysqli_query($koneksi, "DELETE FROM t_siswa WHERE id = '$id'");

// Jika berhasil
if ($query) {

    header("Location: data_siswa.php");
    exit;

} else {

    echo "Data siswa gagal dihapus: " . mysqli_error($koneksi);

}

?>