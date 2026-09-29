<?php

session_start();

include "includes/cek_session.php";

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h1>Dashboard</h1>

<p>
    Selamat datang,
    <b><?php echo $_SESSION['name']; ?></b>
</p>

<p>
    Role:
    <b><?php echo $_SESSION['role']; ?></b>
</p>

<hr>

<?php if ($_SESSION['role'] == 'admin'): ?>

    <h3>Menu Admin</h3>

    <a href="menu1.php">Menu 1</a><br>
    <a href="menu2.php">Menu 2</a><br>
    <a href="menu3.php">Menu 3</a><br>
    <a href="menu4.php">Menu 4</a><br>

<?php elseif ($_SESSION['role'] == 'guru'): ?>

    <h3>Menu Guru</h3>

    <a href="menu3.php">Menu 3</a><br>
    <a href="menu4.php">Menu 4</a><br>

<?php endif; ?>

<br>

<a href="logout.php">Logout</a>

</body>
</html>