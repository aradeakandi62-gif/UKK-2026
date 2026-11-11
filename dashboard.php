
<?php
session_start();

include "config/koneksi.php";
include "includes/cek_session.php";

// ==================================
// MENGAMBIL ROLE PENGGUNA
// ==================================

$role = strtolower(trim($_SESSION['role'] ?? ''));
$nama_user = $_SESSION['name'] ?? 'Pengguna';

if (!in_array($role, ['admin', 'guru', 'wali_kelas'], true)) {
    header("Location: login.php");
    exit;
}

// Menentukan sapaan
if ($role === 'admin') {
    $sapaan = 'Administrator';
} elseif ($role === 'wali_kelas') {
    $sapaan = 'Wali Kelas';
} else {
    $sapaan = 'Guru';
}

// ==================================
// MENGAMBIL JUMLAH DATA
// ==================================

// Jumlah siswa
$query_siswa = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM t_siswa
");

if (!$query_siswa) {
    die("Query siswa gagal: " . mysqli_error($koneksi));
}

$data_siswa = mysqli_fetch_assoc($query_siswa);
$total_siswa = $data_siswa['total'];

// Jumlah guru
$query_guru = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM t_guru
");

if (!$query_guru) {
    die("Query guru gagal: " . mysqli_error($koneksi));
}

$data_guru = mysqli_fetch_assoc($query_guru);
$total_guru = $data_guru['total'];

// Jumlah kelas
$query_kelas = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM t_kelas
");

if (!$query_kelas) {
    die("Query kelas gagal: " . mysqli_error($koneksi));
}

$data_kelas = mysqli_fetch_assoc($query_kelas);
$total_kelas = $data_kelas['total'];

// Jumlah pelanggaran
$query_pelanggaran = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM t_pelanggaran_siswa
");

if (!$query_pelanggaran) {
    die("Query pelanggaran gagal: " . mysqli_error($koneksi));
}

$data_pelanggaran = mysqli_fetch_assoc($query_pelanggaran);
$total_pelanggaran = $data_pelanggaran['total'];

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-white overflow-x-hidden">

<div class="container-fluid px-0">
    <div class="row g-0">

        <!-- SIDEBAR -->
        <?php include "layout/sidebar.php"; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 ms-auto p-4 overflow-hidden">

            <!-- JUDUL -->
            <div class="mb-4">
                <h2>Dashboard</h2>

                <p class="text-muted">
                    Selamat Datang <?= htmlspecialchars($sapaan); ?>.
                </p>

                <small class="text-secondary">
                    <?= htmlspecialchars($nama_user); ?>
                </small>
            </div>

            <?php if ($role === 'admin'): ?>

                <!-- ==================================
                     DASHBOARD ADMIN
                ================================== -->

                <!-- KARTU STATISTIK -->
                <div class="row g-3">

                    <!-- DATA SISWA -->
                    <div class="col-md-4">
                        <a href="data_siswa.php" class="text-decoration-none text-dark">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5>Data Siswa</h5>
                                    <h3><?= $total_siswa; ?></h3>

                                    <span class="text-primary">
                                        Lihat Data Siswa 👥
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- DATA GURU -->
                    <div class="col-md-4">
                        <a href="data_guru.php" class="text-decoration-none text-dark">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5>Data Guru</h5>
                                    <h3><?= $total_guru; ?></h3>

                                    <span class="text-primary">
                                        Lihat Data Guru 🧑‍🏫
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- DATA KELAS -->
                    <div class="col-md-4">
                        <a href="data_kelas.php" class="text-decoration-none text-dark">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5>Kelas</h5>
                                    <h3><?= $total_kelas; ?></h3>

                                    <span class="text-primary">
                                        Lihat Data Kelas 🏫
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>

                <!-- MENU AKSES CEPAT ADMIN -->
                <div class="mt-5">

                    <h4 class="mb-3">Menu Akses Cepat</h4>

                    <div class="row g-3">

                        <div class="col-md-3">
                            <a href="penempatan_siswa.php" class="text-decoration-none">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h2>📚</h2>
                                        <h6 class="text-dark">Penempatan Siswa</h6>
                                        <small class="text-muted">
                                            Kelola penempatan kelas siswa
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="wali_kelas.php" class="text-decoration-none">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h2>👨‍🏫</h2>
                                        <h6 class="text-dark">Wali Kelas</h6>
                                        <small class="text-muted">
                                            Kelola data wali kelas
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="kategori_pelanggaran.php" class="text-decoration-none">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h2>📋</h2>
                                        <h6 class="text-dark">Kategori Pelanggaran</h6>
                                        <small class="text-muted">
                                            Kelola kategori pelanggaran
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="jenis_pelanggaran.php" class="text-decoration-none">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h2>📝</h2>
                                        <h6 class="text-dark">Jenis Pelanggaran</h6>
                                        <small class="text-muted">
                                            Kelola jenis pelanggaran
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>

            <?php else: ?>

                <!-- ==================================
                     DASHBOARD GURU DAN WALI KELAS
                ================================== -->

                <div class="row g-3">

                    <!-- DATA SISWA -->
                    <div class="col-md-4">
                        <a href="data_siswa.php" class="text-decoration-none text-dark">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5>Data Siswa</h5>
                                    <h3><?= $total_siswa; ?></h3>

                                    <span class="text-primary">
                                        Lihat Data Siswa 👥
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- CATAT PELANGGARAN -->
                    <div class="col-md-4">
                        <a href="catat_pelanggaran.php" class="text-decoration-none text-dark">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5>Catat Pelanggaran</h5>
                                    <h3>📝</h3>

                                    <span class="text-primary">
                                        Catat Pelanggaran Siswa
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- TOTAL PELANGGARAN -->
                    <div class="col-md-4">
                        <a href="riwayat_pelanggaran.php" class="text-decoration-none text-dark">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5>Total Pelanggaran</h5>
                                    <h3><?= $total_pelanggaran; ?></h3>

                                    <span class="text-primary">
                                        Lihat Riwayat Pelanggaran
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>

                <!-- MENU AKSES CEPAT GURU -->
                <div class="mt-5">

                    <h4 class="mb-3">Menu Akses Cepat</h4>

                    <div class="row g-3">

                        <div class="col-md-4">
                            <a href="tindakan_pelanggaran.php" class="text-decoration-none">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h2>📋</h2>
                                        <h6 class="text-dark">Tindakan</h6>
                                        <small class="text-muted">
                                            Lihat tindakan pelanggaran siswa
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-4">
                            <a href="rekap_poin.php" class="text-decoration-none">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h2>📊</h2>
                                        <h6 class="text-dark">Rekap Poin</h6>
                                        <small class="text-muted">
                                            Lihat rekap poin siswa
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-4">
                            <a href="riwayat_pelanggaran.php" class="text-decoration-none">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h2>📚</h2>
                                        <h6 class="text-dark">Riwayat</h6>
                                        <small class="text-muted">
                                            Lihat riwayat pelanggaran
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>

            <?php endif; ?>

            <!-- INFORMASI SISTEM -->
            <div class="row g-3 mt-4">

                <div class="col-md-7">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-4">

                            <h5 class="mb-3">🏫 Informasi Sistem</h5>

                            <p class="text-muted">
                                Selamat datang di Sistem Informasi
                                Manajemen Sekolah.
                                Aplikasi ini digunakan untuk membantu
                                pengelolaan data siswa, guru, kelas,
                                dan administrasi sekolah.
                            </p>

                            <hr>

                            <div class="d-flex justify-content-between">
                                <span>Nama Aplikasi</span>
                                <strong>UKK-2026</strong>
                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                <span>Versi Sistem</span>
                                <strong>1.0</strong>
                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                <span>Status</span>
                                <span class="badge bg-success">Aktif</span>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-4">

                            <h5 class="mb-3">✨ Fitur Sistem</h5>

                            <ul class="list-group list-group-flush">

                                <?php if ($role === 'admin'): ?>

                                    <li class="list-group-item">
                                        <span class="text-success">✓</span>
                                        Pengelolaan Data Siswa
                                    </li>

                                    <li class="list-group-item">
                                        <span class="text-success">✓</span>
                                        Pengelolaan Data Guru
                                    </li>

                                    <li class="list-group-item">
                                        <span class="text-success">✓</span>
                                        Pengelolaan Data Kelas
                                    </li>

                                    <li class="list-group-item">
                                        <span class="text-success">✓</span>
                                        Pengelolaan Tahun Ajaran
                                    </li>

                                    <li class="list-group-item">
                                        <span class="text-success">✓</span>
                                        Laporan Sekolah
                                    </li>

                                <?php else: ?>

                                    <li class="list-group-item">
                                        <span class="text-success">✓</span>
                                        Melihat Data Siswa
                                    </li>

                                    <li class="list-group-item">
                                        <span class="text-success">✓</span>
                                        Mencatat Pelanggaran Siswa
                                    </li>

                                    <li class="list-group-item">
                                        <span class="text-success">✓</span>
                                        Melihat Tindakan Pelanggaran
                                    </li>

                                    <li class="list-group-item">
                                        <span class="text-success">✓</span>
                                        Melihat Riwayat Pelanggaran
                                    </li>

                                    <li class="list-group-item">
                                        <span class="text-success">✓</span>
                                        Melihat Rekap Poin
                                    </li>

                                <?php endif; ?>

                            </ul>

                        </div>
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="text-center text-muted mt-5 mb-3">
                <hr>

                <small>
                    &copy; <?= date('Y'); ?> UKK-2026.
                    Sistem Informasi Manajemen Sekolah.
                </small>
            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
