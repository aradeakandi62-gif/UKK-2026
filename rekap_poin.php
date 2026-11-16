
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$role = strtolower(trim($_SESSION['role'] ?? ''));

// Halaman ini untuk Guru dan Wali Kelas
if (!in_array($role, ['guru', 'wali_kelas', 'wali kelas', 'walikelas'], true)) {
    die("Anda tidak memiliki akses ke halaman ini.");
}

// Fungsi keamanan output
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Pencarian
$cari = trim($_GET['cari'] ?? '');

// =====================================
// RINGKASAN TOTAL
// =====================================

$queryRingkasan = mysqli_query($koneksi, "
    SELECT
        COUNT(DISTINCT siswa_id) AS total_siswa,
        COUNT(*) AS total_pelanggaran,
        COALESCE(SUM(poin), 0) AS total_poin
    FROM t_pelanggaran_siswa
    WHERE status = 'Selesai'
");

if (!$queryRingkasan) {
    die("Gagal mengambil ringkasan: " . e(mysqli_error($koneksi)));
}

$ringkasan = mysqli_fetch_assoc($queryRingkasan);

$totalSiswa = (int)($ringkasan['total_siswa'] ?? 0);
$totalPelanggaran = (int)($ringkasan['total_pelanggaran'] ?? 0);
$totalSeluruhPoin = (int)($ringkasan['total_poin'] ?? 0);

// =====================================
// DATA REKAP POIN SISWA
// =====================================

$sql = "
    SELECT
        siswa_id,
        nama_siswa,
        kelas_id,
        nama_kelas,
        COUNT(*) AS jumlah_pelanggaran,
        COALESCE(SUM(poin), 0) AS total_poin
    FROM t_pelanggaran_siswa
    WHERE status = 'Selesai'
";

if ($cari !== '') {
    $sql .= "
        AND (
            nama_siswa LIKE ?
            OR nama_kelas LIKE ?
        )
    ";
}

$sql .= "
    GROUP BY
        siswa_id,
        nama_siswa,
        kelas_id,
        nama_kelas
    ORDER BY total_poin DESC, nama_siswa ASC
";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Query rekap gagal: " . e(mysqli_error($koneksi)));
}

if ($cari !== '') {
    $keyword = '%' . $cari . '%';
    mysqli_stmt_bind_param($stmt, "ss", $keyword, $keyword);
}

mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);

if (!$hasil) {
    die("Gagal mengambil data rekap.");
}

// =====================================
// SIDEBAR
// =====================================

if (file_exists(__DIR__ . '/layout/sidebar_wali_kelas.php')) {
    $sidebar = 'layout/sidebar_wali_kelas.php';
} elseif (file_exists(__DIR__ . '/layout/sidebar_guru.php')) {
    $sidebar = 'layout/sidebar_guru.php';
} else {
    $sidebar = 'layout/sidebar.php';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Rekap Poin Pelanggaran</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include __DIR__ . '/' . $sidebar; ?>

    <!-- KONTEN UTAMA -->
    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">


        <!-- JUDUL -->
        <h3 class="fw-bold mb-1">
            Rekap Poin Pelanggaran
        </h3>

        <p class="text-secondary mb-4">
            Ringkasan total poin setiap siswa berdasarkan pelanggaran
            yang sudah selesai ditangani.
        </p>

        <!-- ================================= -->
        <!-- KARTU RINGKASAN -->
        <!-- ================================= -->

        <div class="row g-3 mb-4">

            <!-- TOTAL SISWA -->
            <div class="col-md-4">

                <div class="card bg-primary text-white border-0 shadow-sm">

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

                <div class="card bg-danger text-white border-0 shadow-sm">

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

                <div class="card bg-warning border-0 shadow-sm">

                    <div class="card-body">

                        <h6 class="fw-bold">
                            Total Seluruh Poin
                        </h6>

                        <h2 class="fw-bold mb-0">
                            <?= $totalSeluruhPoin ?>
                        </h2>

                    </div>

                </div>

            </div>

        </div>

        <!-- ================================= -->
        <!-- TABEL REKAP -->
        <!-- ================================= -->

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">

                <h5 class="fw-bold mb-0">
                    Data Rekap Poin Siswa
                </h5>

            </div>

            <div class="card-body">

                <!-- FORM PENCARIAN -->
                <form method="GET" class="mb-3">

                    <div class="row g-2">

                        <div class="col-md-5">

                            <input
                                type="text"
                                name="cari"
                                class="form-control"
                                placeholder="Cari nama siswa atau kelas..."
                                value="<?= e($cari) ?>">

                        </div>

                        <div class="col-auto">

                            <button
                                type="submit"
                                class="btn btn-primary">

                                Cari

                            </button>

                        </div>

                        <div class="col-auto">

                            <a
                                href="rekap_poin.php"
                                class="btn btn-secondary">

                                Tampilkan Semua

                            </a>

                        </div>

                    </div>

                </form>

                <!-- TABEL -->
                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-hover align-middle">

                        <thead class="table-dark">

                            <tr>
                                <th width="50">No</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jumlah Pelanggaran</th>
                                <th>Total Poin</th>
                                <th>Keterangan</th>
                                <th width="210">Surat Pemanggilan</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php
                            $no = 1;
                            $adaData = false;

                            while ($row = mysqli_fetch_assoc($hasil)):

                                $adaData = true;

                                $siswaId = (int)$row['siswa_id'];
                                $kelasId = (int)$row['kelas_id'];
                                $poin = (int)$row['total_poin'];

                                // Keterangan dan tahap pemanggilan
                                $tahap = 0;
                                $keterangan = 'Terkendali';
                                $badge = 'success';

                                if ($poin >= 100) {
                                    $tahap = 3;
                                    $keterangan = 'Pemanggilan Tahap 3';
                                    $badge = 'danger';
                                } elseif ($poin >= 75) {
                                    $tahap = 2;
                                    $keterangan = 'Pemanggilan Tahap 2';
                                    $badge = 'warning';
                                } elseif ($poin >= 50) {
                                    $tahap = 1;
                                    $keterangan = 'Pemanggilan Tahap 1';
                                    $badge = 'primary';
                                }
                            ?>

                            <tr>

                                <td>
                                    <?= $no++ ?>
                                </td>

                                <td>
                                    <?= e($row['nama_siswa']) ?>
                                </td>

                                <td>
                                    <?= e($row['nama_kelas']) ?>
                                </td>

                                <td>
                                    <?= (int)$row['jumlah_pelanggaran'] ?>
                                </td>

                                <td>

                                    <?php if ($poin > 0): ?>

                                        <span class="badge bg-danger">
                                            <?= $poin ?> Poin
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-secondary">
                                            0 Poin
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <span class="badge bg-<?= e($badge) ?>">
                                        <?= e($keterangan) ?>
                                    </span>

                                </td>

                                <td>

                                    <?php if ($poin >= 50): ?>

                                        <a
                                            href="buat_surat_pemanggilan.php?siswa_id=<?= $siswaId ?>&kelas_id=<?= $kelasId ?>"
                                            class="btn btn-primary btn-sm text-nowrap">

                                            Buat Surat Pemanggilan

                                        </a>

                                    <?php else: ?>

                                        <span class="text-muted small">
                                            Belum memenuhi poin
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                            <?php endwhile; ?>

                            <?php if (!$adaData): ?>

                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        Data rekap poin siswa tidak ditemukan.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

                <!-- TOMBOL BAWAH -->
                <div class="d-flex gap-2 mt-3">

                    <button
                        type="button"
                        onclick="window.print()"
                        class="btn btn-primary">

                        Cetak Rekap Poin

                    </button>

                    <a
                        href="dashboard_guru.php"
                        class="btn btn-secondary">

                        Kembali

                    </a>

                </div>

            </div>

        </div>

        <!-- KETERANGAN -->
        <div class="mt-3">

            <small class="text-secondary">
                Rekap poin hanya menghitung pelanggaran dengan status
                <strong>Selesai</strong>. Pelanggaran berstatus
                Pending dan Proses tidak dihitung.
            </small>

        </div>

    </main>

</div>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
mysqli_stmt_close($stmt);
?>

<style>
@media print {

    body {
        background: white !important;
    }

    aside,
    nav,
    .btn,
    form {
        display: none !important;
    }

    main {
        margin-left: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .table {
        font-size: 11px;
    }

}
</style>
