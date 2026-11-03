
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

            <!-- CARD DASHBOARD -->
            <div class="row g-3">

                <!-- DATA SISWA -->
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">

                            <h5 class="card-title">
                                Data Siswa
                            </h5>

                            <h2 class="mb-0">
                                120
                            </h2>

                        </div>
                    </div>
                </div>

                <!-- DATA GURU -->
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">

                            <h5 class="card-title">
                                Data Guru
                            </h5>

                            <h2 class="mb-0">
                                25
                            </h2>

                        </div>
                    </div>
                </div>

                <!-- DATA KELAS -->
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">

                            <h5 class="card-title">
                                Kelas
                            </h5>

                            <h2 class="mb-0">
                                12
                            </h2>

                        </div>
                    </div>
                </div>

            </div>

        </main>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>