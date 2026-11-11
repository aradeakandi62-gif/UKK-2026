<?php
require_once __DIR__ . '/includes/cek_session.php';
require_once __DIR__ . '/config/akses.php';
require_once __DIR__ . '/config/koneksi.php';

wajib_role(['admin', 'guru_wali_kelas', 'wali_kelas']);

$namaPengguna = $_SESSION['nama'] ?? $_SESSION['name'] ?? $_SESSION['username'] ?? 'Pengguna';
$userId = (int) ($_SESSION['id'] ?? 0);
$guruId = (int) ($_SESSION['guru_id'] ?? 0);
$role = strtolower(trim((string) ($_SESSION['role'] ?? '')));

$role = match ($role) {
    'administrator' => 'admin',
    'guru sekaligus wali kelas' => 'guru_wali_kelas',
    'wali kelas' => 'wali_kelas',
    default => $role
};

// Fungsi menghitung jumlah data
function hitungData($koneksi, $tabel) {

    $daftarTabel = [
        't_siswa',
        't_guru',
        't_kelas',
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
        die("Gagal menghitung $tabel: " . mysqli_error($koneksi));
    }

    $data = mysqli_fetch_assoc($query);

    return (int) $data['total'];
}

// Mengambil jumlah data
$totalSiswa = hitungData($koneksi, 't_siswa');
$totalGuru = hitungData($koneksi, 't_guru');
$totalKelas = hitungData($koneksi, 't_kelas');
$totalPelanggaran = hitungData($koneksi, 't_pelanggaran_siswa');

// Role pengguna
$role = strtolower(trim($_SESSION['role'] ?? ''));

// Dashboard berdasarkan role
$dashboard = ($role === 'guru')
    ? 'dashboard_guru.php'
    : 'dashboard.php';

// Fungsi keamanan output
function e($value) {
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Laporan - Aplikasi Pelanggaran Siswa</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- PENGATURAN CETAK -->
    <style>

        @media print {

            @page {
                size: A4 landscape;
                margin: 10mm;
            }

            /* Sembunyikan seluruh halaman */
            body * {
                visibility: hidden !important;
            }

            /* Tampilkan hanya ringkasan laporan */
            #area-ringkasan,
            #area-ringkasan * {
                visibility: visible !important;
            }

            /* Atur posisi laporan */
            #area-ringkasan {
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }

            /* Hilangkan tombol saat dicetak */
            .d-print-none {
                display: none !important;
            }

            /* Atur tabel */
            #area-ringkasan table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            #area-ringkasan th,
            #area-ringkasan td {
                border: 1px solid #000 !important;
                padding: 10px !important;
                color: #000 !important;
            }

            #area-ringkasan thead th {
                background-color: #212529 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            #area-ringkasan .table-striped tbody tr:nth-of-type(odd) {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            #area-ringkasan .card-header {
                background: white !important;
                color: black !important;
            }

            #area-ringkasan .badge {
                background-color: #dc3545 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            #area-ringkasan {
                break-inside: avoid;
            }

        }

    </style>

</head>

<body class="bg-light">

<div class="container-fluid px-0">

    <div class="row g-0">

        <!-- SIDEBAR -->
        <div class="sidebar-print-hide">

            <?php
            if ($role === 'guru') {
                include 'layout/sidebar_guru.php';
            } else {
                include 'layout/sidebar.php';
            }
            ?>

        </div>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <div class="container-fluid">

                <!-- JUDUL -->
                <div class="mb-4">

                    <h3 class="fw-bold">
                        Laporan
                    </h3>

                    <p class="text-muted mb-0">
                        Ringkasan data Aplikasi Pelanggaran Siswa
                    </p>

                </div>


                <!-- KARTU TOTAL DATA -->
                <div class="row g-3 mb-4">

                    <!-- TOTAL SISWA -->
                    <div class="col-md-3">

                        <div class="card bg-primary text-white border-0 shadow-sm h-100">

                            <div class="card-body">

                                <h6 class="fw-bold">
                                    Total Siswa
                                </h6>

                                <h2 class="fw-bold mb-0">
                                    <?= $totalSiswa ?>
                                </h2>

                            </div>

                        </div>

                    </div>


                    <!-- TOTAL GURU -->
                    <div class="col-md-3">

                        <div class="card bg-success text-white border-0 shadow-sm h-100">

                            <div class="card-body">

                                <h6 class="fw-bold">
                                    Total Guru
                                </h6>

                                <h2 class="fw-bold mb-0">
                                    <?= $totalGuru ?>
                                </h2>

                            </div>

                        </div>

                    </div>


                    <!-- TOTAL KELAS -->
                    <div class="col-md-3">

                        <div class="card bg-warning text-dark border-0 shadow-sm h-100">

                            <div class="card-body">

                                <h6 class="fw-bold">
                                    Total Kelas
                                </h6>

                                <h2 class="fw-bold mb-0">
                                    <?= $totalKelas ?>
                                </h2>

                            </div>

                        </div>

                    </div>


                    <!-- TOTAL PELANGGARAN -->
                    <div class="col-md-3">

                        <div class="card bg-danger text-white border-0 shadow-sm h-100">

                            <div class="card-body">

                                <h6 class="fw-bold">
                                    Total Pelanggaran
                                </h6>

                                <h2 class="fw-bold mb-0">
                                    <?= $totalPelanggaran ?>
                                </h2>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- TABEL RINGKASAN LAPORAN -->
                <!-- BAGIAN INI SAJA YANG AKAN DICETAK -->

                <div class="card border-0 shadow-sm"
                     id="area-ringkasan">

                    <div class="card-header bg-white py-3">

                        <h5 class="fw-bold mb-0">
                            Ringkasan Laporan
                        </h5>

                    </div>


                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped align-middle mb-0">

                                <thead class="table-dark">

                                    <tr>
                                        <th width="10%">No</th>
                                        <th>Jenis Data</th>
                                        <th>Jumlah</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <tr>
                                        <td>1</td>
                                        <td>Data Siswa</td>
                                        <td>
                                            <?= $totalSiswa ?> siswa
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>2</td>
                                        <td>Data Guru</td>
                                        <td>
                                            <?= $totalGuru ?> guru
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>3</td>
                                        <td>Data Kelas</td>
                                        <td>
                                            <?= $totalKelas ?> kelas
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>4</td>
                                        <td>Data Pelanggaran</td>
                                        <td>
                                            <span class="badge bg-danger">
                                                <?= $totalPelanggaran ?> pelanggaran
                                            </span>
                                        </td>
                                    </tr>

                                </tbody>

                            </table>

                        </div>


                        <!-- TOMBOL -->
                        <div class="mt-3 d-print-none">

                            <button onclick="window.print()"
                                    class="btn btn-primary">

                                Cetak Laporan

                            </button>

                            <a href="<?= e($dashboard) ?>"
                               class="btn btn-secondary">

                                Kembali

                            </a>

                        </div>

                    </div>

                </div>


                <!-- KETERANGAN -->
                <div class="mt-3 d-print-none">

                    <small class="text-muted">

                        Laporan diperbarui otomatis berdasarkan jumlah
                        data yang tersimpan di database.

                    </small>

                </div>

            </div>

        </main>

    </div>

</div>


<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>
