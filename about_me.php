
<?php
session_start();

include "includes/cek_session.php";

// Data profil
$nama = "anjay suranjay";
$kelas = "XII RPL";
$sekolah = "SMK Muhammadiyah Tasikmalaya";
$jurusan = "Rekayasa Perangkat Lunak";
$hobi = "Tidur";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Profil Siswa</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR SAMA DENGAN DASHBOARD -->
        <?php include "layout/sidebar.php"; ?>

        <!-- KONTEN PROFIL -->
        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-1">Profil Siswa</h2>
            <p class="text-muted mb-4">
                Informasi data diri siswa
            </p>

            <div class="row justify-content-center">
                <div class="col-12 col-lg-9 col-xl-8">

                    <div class="card border-0 shadow-sm">

                        <!-- HEADER NETRAL -->
                        <div class="card-header bg-white text-dark p-4">
                            <h4 class="fw-bold mb-1">
                                <i class="bi bi-person-vcard me-2"></i>
                                Profil Saya
                            </h4>

                            <p class="text-muted mb-0">
                                Informasi pribadi dan pendidikan
                            </p>
                        </div>

                        <div class="card-body p-4">

                            <!-- FOTO DAN NAMA -->
                            <div class="text-center mb-4">

                                <img
                                    src="jamet.jpg"
                                    alt="Foto Profil"
                                    width="140"
                                    height="140"
                                    class="rounded-circle img-thumbnail mb-3"
                                    style="object-fit: cover;"
                                >

                                <h3 class="fw-bold mb-2">
                                    <?= htmlspecialchars($nama); ?>
                                </h3>

                                <span class="badge bg-secondary px-3 py-2">
                                    <?= htmlspecialchars($kelas); ?>
                                </span>

                            </div>

                            <hr>

                            <!-- DATA DIRI -->
                            <h5 class="fw-bold mb-3">
                                Data Diri
                            </h5>

                            <div class="list-group list-group-flush">

                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person fs-4 me-3"></i>

                                        <div>
                                            <small class="text-muted">
                                                Nama Lengkap
                                            </small>

                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($nama); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-people fs-4 me-3"></i>

                                        <div>
                                            <small class="text-muted">
                                                Kelas
                                            </small>

                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($kelas); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-mortarboard fs-4 me-3"></i>

                                        <div>
                                            <small class="text-muted">
                                                Jurusan
                                            </small>

                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($jurusan); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-building fs-4 me-3"></i>

                                        <div>
                                            <small class="text-muted">
                                                Nama Sekolah
                                            </small>

                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($sekolah); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-heart fs-4 me-3"></i>

                                        <div>
                                            <small class="text-muted">
                                                Hobi
                                            </small>

                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($hobi); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- TENTANG SAYA -->
                            <div class="card bg-light border mt-4">
                                <div class="card-body">
                                    <h5 class="fw-bold">
                                        <i class="bi bi-chat-square-text me-2"></i>
                                        Tentang Saya
                                    </h5>

                                    <p class="mb-0">
                                        Halo, saya
                                        <?= htmlspecialchars($nama); ?>,
                                        siswa kelas
                                        <?= htmlspecialchars($kelas); ?>
                                        di
                                        <?= htmlspecialchars($sekolah); ?>.
                                        Saya tertarik pada teknologi,
                                        pemrograman, dan pengembangan website.
                                    </p>
                                </div>
                            </div>

                            <!-- TOMBOL KEMBALI -->
                            <div class="d-flex justify-content-end mt-4">
                                <a
                                    href="dashboard.php"
                                    class="btn btn-secondary"
                                >
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Kembali ke Dashboard
                                </a>
                            </div>

                        </div>
                    </div>

                    <p class="text-muted text-center mt-3 small">
                        &copy; <?= date('Y'); ?> UKK-2026
                    </p>

                </div>
            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
