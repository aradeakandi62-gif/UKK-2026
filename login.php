<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Sistem Pelanggaran Siswa</title>
</head>
<body>

<h2>Login</h2>

<?php if (isset($_GET['pesan'])): ?>

    <?php if ($_GET['pesan'] == 'gagal'): ?>
        <p>Email atau password salah!</p>
    <?php endif; ?>

<?php endif; ?>

<form action="proses_login.php" method="POST">

    <label>Email</label><br>
    <input type="email" name="email" required>

    <br><br>

    <label>Password</label><br>
    <input type="password" name="password" required>

    <br><br>

    <button type="submit">Login</button>

</form>

</body>
</html>