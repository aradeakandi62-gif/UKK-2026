
<?php
session_start();

include "config/koneksi.php";

// =====================================
// AMBIL DATA DARI FORM
// =====================================

$email = mysqli_real_escape_string($koneksi, $_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// =====================================
// CARI AKUN
// =====================================

$query = mysqli_query($koneksi, "
    SELECT * FROM t_users
    WHERE email = '$email'
    LIMIT 1
");

if (!$query) {
    die("Query gagal: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: login.php?pesan=gagal");
    exit;
}

// =====================================
// PERIKSA PASSWORD
// =====================================

$password_cocok = password_verify(
    $password,
    $data['password']
);

// Mendukung password lama yang masih berbentuk teks biasa
if (!$password_cocok && $password === $data['password']) {
    $password_cocok = true;
}

if (!$password_cocok) {
    header("Location: login.php?pesan=gagal");
    exit;
}

// =====================================
// AMBIL ROLE
// =====================================

$role = strtolower(trim($data['role']));

// =====================================
// TENTUKAN ROLE PENGGUNA
// =====================================

$role_login = '';
$wali_id = null;

// ADMIN
if ($role === 'admin') {

    $role_login = 'admin';

}

// GURU
elseif ($role === 'guru') {

    // Cek apakah guru merupakan wali kelas aktif
    $cek_guru = mysqli_prepare($koneksi, "
        SELECT w.id
        FROM t_guru g
        INNER JOIN t_wali_kelas w
            ON g.id = w.guru_id
        WHERE g.user_id = ?
        AND g.status_aktif = 1
        AND w.status_aktif = 1
        AND (
            w.tanggal_selesai IS NULL
            OR w.tanggal_selesai >= CURDATE()
        )
        LIMIT 1
    ");

    if (!$cek_guru) {
        die("Query wali kelas gagal: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param(
        $cek_guru,
        "i",
        $data['id']
    );

    mysqli_stmt_execute($cek_guru);
    mysqli_stmt_store_result($cek_guru);

    if (mysqli_stmt_num_rows($cek_guru) > 0) {

        mysqli_stmt_bind_result($cek_guru, $wali_id);
        mysqli_stmt_fetch($cek_guru);

        $role_login = 'wali_kelas';

    } else {

        $role_login = 'guru';

    }

    mysqli_stmt_close($cek_guru);

}

// ROLE TIDAK DIKENAL
else {

    header("Location: login.php?pesan=role");
    exit;

}

// =====================================
// BUAT SESSION LOGIN
// =====================================

session_regenerate_id(true);

$_SESSION = [];

$_SESSION['login'] = true;
$_SESSION['id'] = $data['id'];
$_SESSION['name'] = $data['name'];
$_SESSION['email'] = $data['email'];
$_SESSION['role'] = $role_login;

if ($role_login === 'wali_kelas') {
    $_SESSION['wali_kelas_id'] = $wali_id;
}

// SEMUA ROLE MASUK KE DASHBOARD YANG SAMA
header("Location: dashboard.php");
exit;

?>