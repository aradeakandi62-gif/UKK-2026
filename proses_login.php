
<?php
session_start();

include "config/koneksi.php";

// Ambil data dari form
$email = mysqli_real_escape_string($koneksi, $_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Cari akun berdasarkan email
$query = mysqli_query($koneksi, "
    SELECT * FROM t_users
    WHERE email = '$email'
    LIMIT 1
");

if (!$query) {
    die("Query gagal: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($query);

if ($data) {

    // Periksa password hash atau password biasa
    $password_cocok = password_verify($password, $data['password']);

    // Jika password masih teks biasa
    if (!$password_cocok && $password === $data['password']) {
        $password_cocok = true;
    }

    if ($password_cocok) {

        // Simpan session
        session_regenerate_id(true);

        $_SESSION['login'] = true;
        $_SESSION['id'] = $data['id'];
        $_SESSION['name'] = $data['name'];
        $_SESSION['email'] = $data['email'];
        $_SESSION['role'] = strtolower(trim($data['role']));

        // Arahkan berdasarkan role
        if ($_SESSION['role'] === 'admin') {

            header("Location: dashboard.php");
            exit;

        } elseif ($_SESSION['role'] === 'guru') {

            header("Location: dashboard_guru.php");
            exit;

        } else {

            // Role tidak dikenal
            session_unset();
            session_destroy();

            header("Location: login.php?pesan=role");
            exit;
        }

    } else {

        header("Location: login.php?pesan=gagal");
        exit;
    }

} else {

    header("Location: login.php?pesan=gagal");
    exit;
}
?>
