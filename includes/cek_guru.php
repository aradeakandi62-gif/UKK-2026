
<?php

include_once 'includes/cek_session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: dashboard.php");
    exit;
}

?>
