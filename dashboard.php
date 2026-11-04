
<?php
session_start();

include "includes/cek_admin.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include "layout/sidebar.php"; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <div class="mb-4">
                <h2>Dashboard</h2>

                <p class="text-muted">
                    Selamat datang di halaman dashboard.
                </p>
            </div>

            <div class="row g-3">

    <!-- KARTU DATA SISWA -->
    <div class="col-md-4">
        <a href="siswa.php" class="text-decoration-none text-dark">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Data Siswa</h5>
                    <h3>120</h3>
                    <span class="text-primary">Lihat Data Siswa →</span>
                </div>
            </div>
        </a>
    </div>

    <!-- KARTU DATA GURU -->
    <div class="col-md-4">
        <a href="guru.php" class="text-decoration-none text-dark">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Data Guru</h5>
                    <h3>25</h3>
                    <span class="text-primary">Lihat Data Guru →</span>
                </div>
            </div>
        </a>
    </div>

    <!-- KARTU KELAS -->
    <div class="col-md-4">
        <a href="kelas.php" class="text-decoration-none text-dark">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Kelas</h5>
                    <h3>12</h3>
                    <span class="text-primary">Lihat Data Kelas →</span>
                </div>
            </div>
        </a>
    </div>

</div>
        </main>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>