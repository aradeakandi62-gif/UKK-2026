
<?php
// buat_user_guru.php
// Jalankan file ini sekali saja melalui browser.

require_once __DIR__ . '/config/koneksi.php';

$name = 'administrator';
$email = 'guru@gmail.com';
$password = password_hash('guru123', PASSWORD_DEFAULT);
$role = 'guru';

// Query menggunakan prepared statement
$sql = "INSERT INTO t_users (name, email, password, role)
        VALUES (?, ?, ?, ?)";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param(
    $stmt,
    "ssss",
    $name,
    $email,
    $password,
    $role
);

if (mysqli_stmt_execute($stmt)) {
    echo "User guru berhasil dibuat!";
    echo "<br>Email: guru@gmail.com";
    echo "<br>Password: guru123";
    echo "<br>Silakan hapus file buat_user_guru.php setelah berhasil.";
} else {
    echo "Gagal membuat user: " . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);
?>
