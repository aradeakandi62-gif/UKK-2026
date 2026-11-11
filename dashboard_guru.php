
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// =====================================
// MENGAMBIL ROLE DAN INFORMASI AKUN
// =====================================

$nama  = $_SESSION['name'] ?? 'Pengguna';
$email = $_SESSION['email'] ?? '-';
$role  = strtolower(trim($_SESSION['role'] ?? ''));

// Menyamakan format role
$role = str_replace(' ', '_', $role);

// Memastikan role valid
if (!in_array($role, ['admin', 'guru', 'wali_kelas'], true)) {
    die("Role pengguna tidak dikenali. Silakan hubungi administrator.");
}

// =====================================
// FUNGSI MENGHITUNG DATA
// =====================================

function hitungData($koneksi, $tabel)
{
    $daftarTabel = [
        't_siswa',
        't_guru',
        't_kelas',
        't_pelanggaran',
        't_pelanggaran_siswa'
    ];

    if (!in_array($tabel, $daftarTabel, true)) {
        return 0;
    }

    $query = mysqli_query(
        $koneksi,
        "SELECT COUNT(*) AS total FROM `$tabel`"
    );

    if (!$query) {
        return 0;
    }

    $data = mysqli_fetch_assoc($query);

    return (int) $data['total'];
}

// =====================================
// MENGAMBIL JUMLAH DATA
// =====================================

$totalSiswa = hitungData($koneksi, 't_siswa');
$totalGuru = hitungData($koneksi, 't_guru');
$totalKelas = hitungData($koneksi, 't_kelas');
$totalJenisPelanggaran = hitungData($koneksi, 't_pelanggaran');
$totalCatatan = hitungData($koneksi, 't_pelanggaran_siswa');

// =====================================
// MENENTUKAN JUDUL DASHBOARD
// =====================================

if ($role == 'admin') {
    $judul = 'Dashboard Admin';
} elseif ($role == 'wali_kelas') {
    $judul = 'Dashboard Wali Kelas';
} else {
    $judul = 'Dashboard Guru';
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= htmlspecialchars($judul) ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid px-0">
    <div class="row g-0">

        <!-- ================================= -->
        <!-- SIDEBAR SESUAI ROLE -->
        <!-- ================================= -->

        <?php if ($role == 'admin') : ?>

            <?php include 'layout/sidebar.php'; ?>

        <?php else : ?>

            <?php include 'layout/sidebar.php'; ?>

        <?php endif; ?>


        <!-- ================================= -->
        <!-- KONTEN UTAMA -->
        <!-- ================================= -->

        <?php if ($role == 'admin') : ?>

            <main class="col-md-9 col-lg-10 ms-auto p-4 overflow-hidden">

        <?php else : ?>

            <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <?php endif; ?>

            <div class="container-fluid">

                <!-- JUDUL -->
                <div class="mb-4">

                    <h3 class="fw-bold">
                        <?= htmlspecialchars($judul) ?>
                    </h3>

                    <p class="text-muted">
                        Selamat datang di Sistem Informasi Pelanggaran Siswa.
                    </p>

                </div>


                <!-- ================================= -->
                <!-- INFORMASI AKUN -->
                <!-- ================================= -->

                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-3">
                            Informasi Akun
                        </h5>

                        <div class="row align-items-center">

                            <div class="col-md-2 text-center mb-3 mb-md-0">

                                <div class="rounded-circle bg-light border
                                    d-inline-flex align-items-center justify-content-center"
                                    style="width: 85px; height: 85px; font-size: 40px;">

                                    <?php if ($role == 'admin') : ?>
                                        👨‍💻
                                    <?php else : ?>
                                        👨‍🏫
                                    <?php endif; ?>

                                </div>

                            </div>

                            <div class="col-md-10">

                                <h5 class="fw-bold mb-2">
                                    <?= htmlspecialchars($nama) ?>
                                </h5>

                                <span class="badge bg-success mb-2">
                                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $role))) ?>
                                </span>

                                <p class="text-muted mb-1">
                                    Email: <?= htmlspecialchars($email) ?>
                                </p>

                                <p class="text-muted mb-0">
                                    Tanggal: <?= date('d-m-Y') ?>
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ================================= -->
                <!-- DASHBOARD KHUSUS ADMIN -->
                <!-- ================================= -->

                <?php if ($role == 'admin') : ?>

                    <!-- KARTU STATISTIK -->

                    <div class="row g-3 mb-4">

                        <div class="col-md-4">

                            <a href="data_siswa.php"
                                class="text-decoration-none text-dark">

                                <div class="card shadow-sm h-100">
                                    <div class="card-body">

                                        <h5>Data Siswa</h5>

                                        <h3 class="fw-bold">
                                            <?= $totalSiswa ?>
                                        </h3>

                                        <span class="text-primary">
                                            Lihat Data Siswa 👥
                                        </span>

                                    </div>
                                </div>

                            </a>

                        </div>


                        <div class="col-md-4">

                            <a href="data_guru.php"
                                class="text-decoration-none text-dark">

                                <div class="card shadow-sm h-100">
                                    <div class="card-body">

                                        <h5>Data Guru</h5>

                                        <h3 class="fw-bold">
                                            <?= $totalGuru ?>
                                        </h3>

                                        <span class="text-primary">
                                            Lihat Data Guru 🧑‍🏫
                                        </span>

                                    </div>
                                </div>

                            </a>

                        </div>


                        <div class="col-md-4">

                            <a href="data_kelas.php"
                                class="text-decoration-none text-dark">

                                <div class="card shadow-sm h-100">
                                    <div class="card-body">

                                        <h5>Data Kelas</h5>

                                        <h3 class="fw-bold">
                                            <?= $totalKelas ?>
                                        </h3>

                                        <span class="text-primary">
                                            Lihat Data Kelas 🏫
                                        </span>

                                    </div>
                                </div>

                            </a>

                        </div>

                    </div>


                    <!-- MENU AKSES CEPAT -->

                    <div class="mt-5">

                        <h5 class="fw-bold mb-3">
                            Menu Akses Cepat
                        </h5>

                        <div class="row g-3">

                            <div class="col-md-3">

                                <a href="penempatan_siswa.php"
                                    class="text-decoration-none">

                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">

                                            <h2>📚</h2>

                                            <h6 class="text-dark">
                                                Penempatan Siswa
                                            </h6>

                                            <small class="text-muted">
                                                Kelola penempatan kelas siswa
                                            </small>

                                        </div>
                                    </div>

                                </a>

                            </div>


                            <div class="col-md-3">

                                <a href="wali_kelas.php"
                                    class="text-decoration-none">

                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">

                                            <h2>👨‍🏫</h2>

                                            <h6 class="text-dark">
                                                Wali Kelas
                                            </h6>

                                            <small class="text-muted">
                                                Kelola data wali kelas
                                            </small>

                                        </div>
                                    </div>

                                </a>

                            </div>


                            <div class="col-md-3">

                                <a href="kategori_pelanggaran.php"
                                    class="text-decoration-none">

                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">

                                            <h2>📋</h2>

                                            <h6 class="text-dark">
                                                Kategori Pelanggaran
                                            </h6>

                                            <small class="text-muted">
                                                Kelola kategori pelanggaran
                                            </small>

                                        </div>
                                    </div>

                                </a>

                            </div>


                            <div class="col-md-3">

                                <a href="jenis_pelanggaran.php"
                                    class="text-decoration-none">

                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">

                                            <h2>📝</h2>

                                            <h6 class="text-dark">
                                                Jenis Pelanggaran
                                            </h6>

                                            <small class="text-muted">
                                                Kelola jenis pelanggaran
                                            </small>

                                        </div>
                                    </div>

                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- INFORMASI SISTEM -->

                    <div class="row g-3 mt-4">

                        <div class="col-md-7">

                            <div class="card shadow-sm h-100">

                                <div class="card-body p-4">

                                    <h5 class="mb-3">
                                        🏫 Informasi Sistem
                                    </h5>

                                    <p class="text-muted">
                                        Selamat datang di Sistem Informasi
                                        Manajemen Sekolah. Aplikasi ini digunakan
                                        untuk membantu pengelolaan data siswa,
                                        guru, kelas, dan administrasi sekolah.
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

                                    <h5 class="mb-3">
                                        ✨ Fitur Sistem
                                    </h5>

                                    <ul class="list-group list-group-flush">

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

                                    </ul>

                                </div>

                            </div>

                        </div>

                    </div>


                <!-- ================================= -->
                <!-- DASHBOARD GURU DAN WALI KELAS -->
                <!-- ================================= -->

                <?php else : ?>

                    <!-- RINGKASAN DATA -->

                    <div class="card border-0 shadow-sm mb-4">

                        <div class="card-header bg-white py-3">

                            <h5 class="fw-bold mb-0">
                                Ringkasan Data
                            </h5>

                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-bordered table-striped mb-0">

                                    <thead class="table-dark">

                                        <tr>
                                            <th>No</th>
                                            <th>Jenis Data</th>
                                            <th>Jumlah</th>
                                        </tr>

                                    </thead>

                                    <tbody>

                                        <tr>
                                            <td>1</td>
                                            <td>Data Siswa</td>
                                            <td><?= $totalSiswa ?> siswa</td>
                                        </tr>

                                        <tr>
                                            <td>2</td>
                                            <td>Data Kelas</td>
                                            <td><?= $totalKelas ?> kelas</td>
                                        </tr>

                                        <tr>
                                            <td>3</td>
                                            <td>Jenis Pelanggaran</td>
                                            <td><?= $totalJenisPelanggaran ?> jenis</td>
                                        </tr>

                                        <tr>
                                            <td>4</td>
                                            <td>Catatan Pelanggaran</td>
                                            <td><?= $totalCatatan ?> catatan</td>
                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>


                    <!-- GRAFIK STATISTIK -->

                    <div class="card border-0 shadow-sm mb-4">

                        <div class="card-header bg-white py-3">

                            <h5 class="fw-bold mb-1">
                                Grafik Statistik Data
                            </h5>

                            <small class="text-muted">
                                Grafik jumlah data pada sistem pelanggaran siswa
                            </small>

                        </div>

                        <div class="card-body">

                            <div style="height: 350px; position: relative;">

                                <canvas id="grafikStatistik"></canvas>

                            </div>

                        </div>

                    </div>

                <?php endif; ?>


                <!-- FOOTER -->

                <div class="text-center text-muted mt-5 mb-3">

                    <hr>

                    <small>
                        &copy; <?= date('Y') ?> UKK-2026.
                        Sistem Informasi Pelanggaran Siswa.
                    </small>

                </div>

            </div>

        </main>

    </div>
</div>


<!-- ================================= -->
<!-- JAVASCRIPT -->
<!-- ================================= -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($role != 'admin') : ?>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>

        const ctx = document.getElementById('grafikStatistik');

        new Chart(ctx, {

            type: 'bar',

            data: {

                labels: [
                    'Siswa',
                    'Kelas',
                    'Jenis Pelanggaran',
                    'Catatan Pelanggaran'
                ],

                datasets: [{

                    label: 'Jumlah Data',

                    data: [
                        <?= $totalSiswa ?>,
                        <?= $totalKelas ?>,
                        <?= $totalJenisPelanggaran ?>,
                        <?= $totalCatatan ?>
                    ],

                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#ffc107',
                        '#dc3545'
                    ],

                    borderWidth: 1,
                    borderRadius: 5

                }]

            },

            options: {

                responsive: true,
                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        display: true,
                        position: 'top'
                    }

                },

                scales: {

                    y: {
                        beginAtZero: true,

                        ticks: {
                            precision: 0
                        }

                    }

                }

            }

        });

    </script>

<?php endif; ?>

</body>
</html>