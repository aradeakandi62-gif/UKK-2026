
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$nama_pengguna = $_SESSION['name'] ?? 'Pengguna';
$role = $_SESSION['role'] ?? '-';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>About Me</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">

            <!-- JUDUL -->
            <div class="mb-4">
                <h2 class="fw-bold">About Me</h2>
                <p class="text-muted">
                    Informasi tentang pembuat dan project website pelanggaran siswa.
                </p>
            </div>

            <!-- PROFIL -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">

                    <div class="row align-items-center">

                        <div class="col-md-3 text-center mb-3 mb-md-0">

                            <!-- FOTO PROFIL -->
                            <img
                                src="aradea.jpeg"
                                alt="Foto Profil"
                                class="img-fluid rounded-circle border"
                                style="width: 150px; height: 150px; object-fit: cover;"
                            >

                        </div>

                        <div class="col-md-9">

                            <h3 class="fw-bold mb-2">
                                <?= htmlspecialchars($nama_pengguna) ?>
                            </h3>

                            <p class="text-primary fw-semibold mb-2">
                                Siswa SMK - Rekayasa Perangkat Lunak
                            </p>

                            <p class="text-muted mb-3">
                                Saya merupakan siswa jurusan Rekayasa Perangkat Lunak
                                yang sedang mengembangkan aplikasi berbasis web
                                sebagai project Uji Kompetensi Keahlian (UKK).
                            </p>

                            <span class="badge bg-primary">
                                <?= htmlspecialchars(ucfirst($role)) ?>
                            </span>

                            <span class="badge bg-secondary">
                                Web Developer
                            </span>

                        </div>

                    </div>

                </div>
            </div>

            <!-- INFORMASI DIRI -->
            <div class="card shadow-sm mb-4">

                <div class="card-header bg-light">
                    <strong>Informasi Pembuat</strong>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Nama Lengkap</label>
                            <h6 class="fw-semibold">Aradea Kandi</h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Kelas</label>
                            <h6 class="fw-semibold">Xll RPL 2</h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Jurusan</label>
                            <h6 class="fw-semibold">Rekayasa Perangkat Lunak</h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Sekolah</label>
                            <h6 class="fw-semibold">Smk Muhammadiyah Kota Tasikmalaya</h6>
                        </div>

                    </div>

                </div>
            </div>

            <!-- INFORMASI PROJECT -->
            <div class="card shadow-sm mb-4">

                <div class="card-header bg-light">
                    <strong>Informasi Project</strong>
                </div>

                <div class="card-body">

                    <h5 class="fw-bold">
                        Sistem Informasi Pelanggaran Siswa
                    </h5>

                    <p class="text-muted">
                        Aplikasi berbasis web yang dibuat untuk membantu
                        sekolah dalam mengelola data pelanggaran siswa
                        secara terkomputerisasi.
                    </p>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Nama Project</label>
                            <h6 class="fw-semibold">
                                Website Pelanggaran Siswa
                            </h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Jenis Project</label>
                            <h6 class="fw-semibold">
                                Uji Kompetensi Keahlian (UKK)
                            </h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Platform</label>
                            <h6 class="fw-semibold">
                                Website
                            </h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Database</label>
                            <h6 class="fw-semibold">
                                MySQL
                            </h6>
                        </div>

                    </div>

                </div>
            </div>

            <!-- TEKNOLOGI -->
            <div class="card shadow-sm mb-4">

                <div class="card-header bg-light">
                    <strong>Teknologi yang Digunakan</strong>
                </div>

                <div class="card-body">

                    <div class="d-flex flex-wrap gap-2">

                        <span class="badge bg-primary p-2">
                            PHP Native
                        </span>

                        <span class="badge bg-success p-2">
                            MySQL
                        </span>

                        <span class="badge bg-danger p-2">
                            HTML
                        </span>

                        <span class="badge bg-info text-dark p-2">
                            Bootstrap 5
                        </span>

                        <span class="badge bg-warning text-dark p-2">
                            JavaScript
                        </span>

                        <span class="badge bg-secondary p-2">
                            XAMPP
                        </span>

                    </div>

                </div>
            </div>

            <!-- PENUTUP -->
            <div class="card shadow-sm">

                <div class="card-body text-center p-4">

                    <h5 class="fw-bold">
                        Terima Kasih
                    </h5>

                    <p class="text-muted mb-0">
                        Terima kasih telah menggunakan aplikasi
                        Sistem Informasi Pelanggaran Siswa.
                    </p>

                    <hr>

                    <small class="text-muted">
                        © <?= date('Y') ?> Website Pelanggaran Siswa.
                        Dibuat untuk Project UKK Rekayasa Perangkat Lunak.
                    </small>

                </div>

            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
