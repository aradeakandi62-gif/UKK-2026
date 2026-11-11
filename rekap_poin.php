
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// ===============================
// ROLE PENGGUNA
// ===============================
$role = strtolower(trim($_SESSION['role'] ?? ''));

// ===============================
// FUNGSI KEAMANAN OUTPUT
// ===============================
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// ===============================
// PENCARIAN SISWA
// ===============================
$cari = trim($_GET['cari'] ?? '');

$where = "";

if ($cari !== '') {
    $keyword = mysqli_real_escape_string($koneksi, $cari);

    $where = "WHERE nama_siswa LIKE '%$keyword%'
              OR nama_kelas LIKE '%$keyword%'";
}

// ===============================
// TOTAL SISWA YANG MELANGGAR
// ===============================
$querySiswa = mysqli_query($koneksi, "
    SELECT COUNT(DISTINCT siswa_id) AS total
    FROM t_pelanggaran_siswa
    WHERE siswa_id IS NOT NULL
");

$totalSiswa = (int) mysqli_fetch_assoc($querySiswa)['total'];

// ===============================
// TOTAL PELANGGARAN
// ===============================
$queryPelanggaran = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM t_pelanggaran_siswa
");

$totalPelanggaran = (int) mysqli_fetch_assoc($queryPelanggaran)['total'];

// ===============================
// TOTAL SELURUH POIN
// ===============================
$queryPoin = mysqli_query($koneksi, "
    SELECT COALESCE(SUM(poin), 0) AS total
    FROM t_pelanggaran_siswa
");

$totalPoin = (int) mysqli_fetch_assoc($queryPoin)['total'];

// ===============================
// REKAP POIN PER SISWA
// ===============================
$query = mysqli_query($koneksi, "
    SELECT
        siswa_id,
        nama_siswa,
        kelas_id,
        nama_kelas,
        COUNT(*) AS jumlah_pelanggaran,
        SUM(poin) AS total_poin
    FROM t_pelanggaran_siswa
    $where
    GROUP BY siswa_id, nama_siswa, kelas_id, nama_kelas
    ORDER BY total_poin DESC, nama_siswa ASC
");

if (!$query) {
    die('Query rekap poin gagal: ' . mysqli_error($koneksi));
}

// ===============================
// DASHBOARD BERDASARKAN ROLE
// ===============================
$dashboard = ($role === 'guru')
    ? 'dashboard_guru.php'
    : 'dashboard.php';

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Rekap Poin Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        main {
            margin-left: 213px;
            width: calc(100% - 213px);
            min-width: 0;
        }

        @media print {
            @page {
                size: landscape;
                margin: 10mm;
            }

            body {
                background: white !important;
            }

            .sidebar-print-hide,
            .d-print-none {
                display: none !important;
            }

            main {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 0 !important;
            }

            .card {
                box-shadow: none !important;
            }

            .table {
                font-size: 11px;
            }
        }
    </style>

</head>

<body>

<div class="d-flex">

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
                    Rekap Poin Pelanggaran
                </h3>

                <p class="text-muted">
                    Ringkasan total poin setiap siswa.
                </p>

            </div>

            <!-- KARTU STATISTIK -->
            <div class="row g-3 mb-4">

                <!-- TOTAL SISWA -->
                <div class="col-md-4">

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

                <!-- TOTAL PELANGGARAN -->
                <div class="col-md-4">

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

                <!-- TOTAL POIN -->
                <div class="col-md-4">

                    <div class="card bg-warning text-dark border-0 shadow-sm h-100">

                        <div class="card-body">

                            <h6 class="fw-bold">
                                Total Seluruh Poin
                            </h6>

                            <h2 class="fw-bold mb-0">
                                <?= $totalPoin ?>
                            </h2>

                        </div>

                    </div>

                </div>

            </div>

            <!-- TABEL REKAP -->
            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">

                    <strong>Data Rekap Poin Siswa</strong>

                </div>

                <div class="card-body">

                    <!-- FORM PENCARIAN -->
                    <form method="GET" class="row g-2 mb-3 d-print-none">

                        <div class="col-md-5">

                            <input type="text"
                                   name="cari"
                                   class="form-control"
                                   placeholder="Cari nama siswa atau kelas..."
                                   value="<?= e($cari) ?>">

                        </div>

                        <div class="col-auto">

                            <button type="submit" class="btn btn-primary">
                                Cari
                            </button>

                        </div>

                        <div class="col-auto">

                            <a href="rekap_poin.php" class="btn btn-secondary">
                                Tampilkan Semua
                            </a>

                        </div>

                    </form>

                    <!-- TABEL -->
                    <div class="table-responsive">

                        <table class="table table-bordered table-striped table-hover align-middle">

                            <thead class="table-dark">

                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jumlah Pelanggaran</th>
                                    <th>Total Poin</th>
                                    <th>Keterangan</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php if (mysqli_num_rows($query) > 0): ?>

                                    <?php $no = 1; ?>

                                    <?php while ($data = mysqli_fetch_assoc($query)): ?>

                                        <tr>

                                            <td><?= $no++ ?></td>

                                            <td>
                                                <?= e($data['nama_siswa']) ?>
                                            </td>

                                            <td>
                                                <?= e($data['nama_kelas']) ?>
                                            </td>

                                            <td>
                                                <?= (int)$data['jumlah_pelanggaran'] ?>
                                            </td>

                                            <td>
                                                <span class="badge bg-danger">
                                                    <?= (int)$data['total_poin'] ?> Poin
                                                </span>
                                            </td>

                                            <td>

                                                <?php if ((int)$data['total_poin'] >= 50): ?>

                                                    <span class="badge bg-warning text-dark">
                                                        Perlu Pembinaan
                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge bg-success">
                                                        Terkendali
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Data rekap poin siswa tidak ditemukan.
                                        </td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                    <!-- TOMBOL -->
                    <div class="mt-3 d-flex gap-2 d-print-none">

                        <button onclick="window.print()"
                                class="btn btn-primary">
                            Cetak Rekap Poin
                        </button>

                        <a href="<?= e($dashboard) ?>"
                           class="btn btn-secondary">
                            Kembali
                        </a>

                    </div>

                </div>

            </div>

            <!-- KETERANGAN -->
            <div class="mt-3">

                <small class="text-muted">
                    Rekap poin diperbarui otomatis berdasarkan data
                    pelanggaran siswa yang tersimpan di database.
                </small>

            </div>

        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
