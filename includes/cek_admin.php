
<?php

include_once 'includes/cek_session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard_guru.php");
    exit;
}

?>
