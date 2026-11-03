
<?php
session_start();

include "includes/cek_session.php";

// Pastikan hanya guru yang bisa masuk
if ($_SESSION['role'] !== 'guru') {
    header("Location: dashboard.php");
    exit;
}

$nama = $_SESSION['name'] ?? 'Guru';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard Guru</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR GURU -->
        <?php include "layout/sidebar_guru.php"; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <h2>Dashboard Guru</h2>

            <p class="text-muted">
                Selamat datang, <?= htmlspecialchars($nama); ?>.
            </p>

            <div class="card shadow-sm">
                <div class="card-body">

                    <h5 class="card-title">
                        Selamat Datang Guru!
                    </h5>

                    <p class="mb-0">
                        Anda berhasil login sebagai guru.
                    </p>

                </div>
            </div>

        </main>

    </div>
</div>

</body>
</html>