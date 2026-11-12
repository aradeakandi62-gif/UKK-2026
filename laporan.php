
<?php
session_start();

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/cek_session.php';

// ==================================================
// 1. LOGIN DAN HAK AKSES
// ==================================================
$userId = (int)($_SESSION['id'] ?? 0);
$role = strtolower(trim($_SESSION['role'] ?? ''));

if ($role === 'wali kelas') {
    $role = 'wali_kelas';
}

if ($role === 'administrator') {
    $role = 'admin';
}

if (!in_array($role, ['admin', 'wali_kelas', 'guru_wali_kelas'], true)) {
    http_response_code(403);
    exit('Anda tidak memiliki akses ke halaman ini.');
}

$isAdmin = ($role === 'admin');
$isWali = !$isAdmin;

// ==================================================
// 2. FUNGSI BANTU
// ==================================================
function e($value)
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function ambilData($koneksi, $sql, $types = '', $params = [])
{
    $stmt = mysqli_prepare($koneksi, $sql);

    if (!$stmt) {
        die('Query gagal: ' . mysqli_error($koneksi));
    }

    if ($types !== '') {
        $bind = [$types];

        foreach ($params as $key => $value) {
            $bind[] = &$params[$key];
        }

        call_user_func_array(
            [$stmt, 'bind_param'],
            $bind
        );
    }

    if (!mysqli_stmt_execute($stmt)) {
        die('Eksekusi gagal: ' . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    $data = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }

    mysqli_stmt_close($stmt);

    return $data;
}

function kolomAda($koneksi, $tabel, $kolom)
{
    $tabelDiizinkan = [
        't_tahun_ajaran',
        't_wali_kelas',
        't_guru'
    ];

    if (!in_array($tabel, $tabelDiizinkan, true)) {
        return false;
    }

    $result = mysqli_query(
        $koneksi,
        "SHOW COLUMNS FROM `$tabel`"
    );

    if (!$result) {
        return false;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['Field'] === $kolom) {
            return true;
        }
    }

    return false;
}

// ==================================================
// 3. KOLOM TAHUN AJARAN
// ==================================================
if (kolomAda($koneksi, 't_tahun_ajaran', 'nama')) {
    $kolomTahun = 'nama';
} elseif (
    kolomAda($koneksi, 't_tahun_ajaran', 'nama_tahun_ajaran')
) {
    $kolomTahun = 'nama_tahun_ajaran';
} else {
    die('Kolom nama tahun ajaran tidak ditemukan.');
}

// ==================================================
// 4. MENGAMBIL DATA GURU LOGIN
// ==================================================
$guruId = 0;

if ($isWali) {
    $dataGuru = ambilData(
        $koneksi,
        "SELECT id FROM t_guru WHERE user_id = ? LIMIT 1",
        'i',
        [$userId]
    );

    if (!empty($dataGuru)) {
        $guruId = (int)$dataGuru[0]['id'];
    }
}

// ==================================================
// 5. DAFTAR TAHUN AJARAN
// ==================================================
if ($isWali) {

    if ($guruId > 0) {
        $tahunList = ambilData(
            $koneksi,
            "SELECT DISTINCT
                ta.id,
                ta.`$kolomTahun` AS nama_tahun
             FROM t_wali_kelas wk
             INNER JOIN t_tahun_ajaran ta
                ON wk.tahun_ajaran_id = ta.id
             WHERE wk.guru_id = ?
               AND wk.status_aktif = 1
             ORDER BY ta.id DESC",
            'i',
            [$guruId]
        );
    } else {
        $tahunList = [];
    }

} else {

    $tahunList = ambilData(
        $koneksi,
        "SELECT
            id,
            `$kolomTahun` AS nama_tahun
         FROM t_tahun_ajaran
         ORDER BY id DESC"
    );
}

// ==================================================
// 6. FILTER TAHUN AJARAN
// ==================================================
$tahunId = isset($_GET['tahun_ajaran'])
    ? (int)$_GET['tahun_ajaran']
    : 0;

$daftarTahun = array_map(
    'intval',
    array_column($tahunList, 'id')
);

if (!in_array($tahunId, $daftarTahun, true)) {
    $tahunId = !empty($daftarTahun)
        ? $daftarTahun[0]
        : 0;
}

// ==================================================
// 7. DAFTAR KELAS
// ==================================================
if ($isWali) {

    if ($guruId > 0 && $tahunId > 0) {
        $kelasList = ambilData(
            $koneksi,
            "SELECT DISTINCT
                k.id,
                k.nama
             FROM t_wali_kelas wk
             INNER JOIN t_kelas k
                ON wk.kelas_id = k.id
             WHERE wk.guru_id = ?
               AND wk.tahun_ajaran_id = ?
               AND wk.status_aktif = 1
             ORDER BY k.nama ASC",
            'ii',
            [$guruId, $tahunId]
        );
    } else {
        $kelasList = [];
    }

} else {

    $kelasList = ambilData(
        $koneksi,
        "SELECT id, nama
         FROM t_kelas
         ORDER BY nama ASC"
    );
}

// ==================================================
// 8. FILTER KELAS
// ==================================================
$kelasId = isset($_GET['kelas'])
    ? (int)$_GET['kelas']
    : 0;

$daftarKelas = array_map(
    'intval',
    array_column($kelasList, 'id')
);

if (!in_array($kelasId, $daftarKelas, true)) {
    $kelasId = !empty($daftarKelas)
        ? $daftarKelas[0]
        : 0;
}

// ==================================================
// 9. DAFTAR SISWA
// ==================================================
$siswaList = [];

if ($tahunId > 0 && $kelasId > 0) {
    $siswaList = ambilData(
        $koneksi,
        "SELECT DISTINCT
            s.id,
            s.nis,
            s.nama
         FROM t_kelas_siswa ks
         INNER JOIN t_siswa s
            ON ks.siswa_id = s.id
         WHERE ks.tahun_ajaran_id = ?
           AND ks.kelas_id = ?
         ORDER BY s.nama ASC",
        'ii',
        [$tahunId, $kelasId]
    );
}

// ==================================================
// 10. FILTER SISWA DAN TANGGAL
// ==================================================
$siswaId = isset($_GET['siswa'])
    ? (int)$_GET['siswa']
    : 0;

$daftarSiswa = array_map(
    'intval',
    array_column($siswaList, 'id')
);

if (!in_array($siswaId, $daftarSiswa, true)) {
    $siswaId = 0;
}

$tanggal = trim($_GET['tanggal'] ?? '');

if (
    $tanggal !== '' &&
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)
) {
    $tanggal = '';
}

// ==================================================
// 11. AMBIL DATA LAPORAN
// ==================================================
$dataLaporan = [];
$totalPelanggaran = 0;
$totalPoin = 0;

if ($tahunId > 0 && $kelasId > 0) {

    $sql = "
        SELECT
            ps.id,
            ps.tanggal,

            COALESCE(
                NULLIF(ps.nama_siswa, ''),
                s.nama
            ) AS nama_siswa,

            COALESCE(
                NULLIF(ps.nama_kelas, ''),
                k.nama
            ) AS nama_kelas,

            COALESCE(
                NULLIF(ps.nama_pelanggaran, ''),
                p.nama
            ) AS nama_pelanggaran,

            COALESCE(ps.poin, 0) AS poin,
            ps.keterangan,
            ps.tindakan

        FROM t_pelanggaran_siswa ps

        LEFT JOIN t_siswa s
            ON ps.siswa_id = s.id

        LEFT JOIN t_kelas k
            ON ps.kelas_id = k.id

        LEFT JOIN t_pelanggaran p
            ON ps.pelanggaran_id = p.id

        WHERE ps.tahun_ajaran_id = ?
          AND ps.kelas_id = ?
    ";

    $types = 'ii';
    $params = [$tahunId, $kelasId];

    if ($siswaId > 0) {
        $sql .= " AND ps.siswa_id = ?";
        $types .= 'i';
        $params[] = $siswaId;
    }

    if ($tanggal !== '') {
        $sql .= " AND DATE(ps.tanggal) = ?";
        $types .= 's';
        $params[] = $tanggal;
    }

    $sql .= " ORDER BY ps.tanggal DESC, ps.id DESC";

    $dataLaporan = ambilData(
        $koneksi,
        $sql,
        $types,
        $params
    );

    $totalPelanggaran = count($dataLaporan);

    foreach ($dataLaporan as $row) {
        $totalPoin += (int)$row['poin'];
    }
}

// ==================================================
// 12. EXPORT EXCEL
// ==================================================
if (isset($_GET['export']) && $_GET['export'] === 'excel') {

    $namaFile = 'Laporan_Pelanggaran_' .
        date('Y-m-d_H-i-s') . '.xls';

    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $namaFile . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo "\xEF\xBB\xBF";
    ?>

    <html>
    <head>
        <meta charset="UTF-8">
    </head>

    <body>

        <h2>LAPORAN PELANGGARAN SISWA</h2>

        <p>Tahun Ajaran: <?= e($namaTahun ?? '') ?></p>
        <p>Total Pelanggaran: <?= $totalPelanggaran ?></p>
        <p>Total Poin: <?= $totalPoin ?></p>

        <table border="1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Pelanggaran</th>
                    <th>Poin</th>
                    <th>Keterangan</th>
                    <th>Tindakan</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($dataLaporan as $i => $row): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e(date('d-m-Y', strtotime($row['tanggal']))) ?></td>
                        <td><?= e($row['nama_siswa']) ?></td>
                        <td><?= e($row['nama_kelas']) ?></td>
                        <td><?= e($row['nama_pelanggaran']) ?></td>
                        <td><?= (int)$row['poin'] ?></td>
                        <td><?= e($row['keterangan']) ?></td>
                        <td><?= e($row['tindakan']) ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($dataLaporan)): ?>
                    <tr>
                        <td colspan="8">Tidak ada data laporan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </body>
    </html>

    <?php
    exit;
}

// ==================================================
// 13. SIDEBAR SESUAI ROLE
// ==================================================
if ($isAdmin) {
    $dashboard = 'dashboard.php';

    $sidebarCandidates = [
        __DIR__ . '/layout/sidebar.php',
        __DIR__ . '/layout/sidebar_admin.php'
    ];
} else {
    $dashboard = 'dashboard_wali_kelas.php';

    $sidebarCandidates = [
        __DIR__ . '/layout/sidebar_wali_kelas.php',
        __DIR__ . '/layout/sidebar_guru.php',
        __DIR__ . '/layout/sidebar.php'
    ];
}

$sidebarPath = null;

foreach ($sidebarCandidates as $candidate) {
    if (is_file($candidate)) {
        $sidebarPath = $candidate;
        break;
    }
}

// ==================================================
// 14. NAMA TAHUN AJARAN DAN KELAS
// ==================================================
$namaTahun = '-';
$namaKelas = '-';

foreach ($tahunList as $th) {
    if ((int)$th['id'] === $tahunId) {
        $namaTahun = $th['nama_tahun'];
        break;
    }
}

foreach ($kelasList as $kl) {
    if ((int)$kl['id'] === $kelasId) {
        $namaKelas = $kl['nama'];
        break;
    }
}

// ==================================================
// 15. URL FILTER
// ==================================================
$queryFilter = http_build_query([
    'tahun_ajaran' => $tahunId,
    'kelas' => $kelasId,
    'siswa' => $siswaId,
    'tanggal' => $tanggal
]);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laporan Pelanggaran</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            background: #f5f6fa;
            overflow-x: hidden;
        }

        /*
         * LAYOUT UTAMA
         * Wrapper sidebar menyediakan ruang 220px.
         * Konten menggunakan flex agar tidak tertutup sidebar.
         */
        .layout-wrapper {
            display: flex;
            align-items: stretch;
            width: 100%;
            min-height: 100vh;
        }

        .sidebar-holder {
            flex: 0 0 220px;
            width: 220px;
            min-width: 220px;
            min-height: 100vh;
            position: relative;
            z-index: 10;
        }

        /*
         * Sidebar yang menggunakan position fixed
         * tetap berada pada kolom sebelah kiri.
         */
        .sidebar-holder > * {
            max-width: 220px;
        }

        .content-area {
            flex: 1 1 0;
            min-width: 0;
            width: auto;
            padding: 24px;
            position: relative;
        }

        .container-fluid {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            min-width: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 22px;
            background: #212529;
            color: white;
        }

        .table th,
        .table td {
            vertical-align: middle;
            white-space: nowrap;
        }

        .card {
            border-radius: 12px;
        }

        @media (max-width: 768px) {
            .layout-wrapper {
                display: block;
            }

            .sidebar-holder {
                width: 100%;
                min-width: 100%;
                min-height: auto;
            }

            .content-area {
                width: 100%;
                padding: 15px;
            }
        }

        @media print {
            @page {
                size: landscape;
                margin: 10mm;
            }

            .sidebar-holder,
            .d-print-none {
                display: none !important;
            }

            .layout-wrapper {
                display: block !important;
            }

            .content-area {
                width: 100% !important;
                padding: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

            .table {
                font-size: 11px;
            }
        }
    </style>

</head>

<body>

<div class="layout-wrapper">

    <!-- SIDEBAR -->
    <aside class="sidebar-holder">

        <?php if ($sidebarPath !== null): ?>

            <?php include $sidebarPath; ?>

        <?php else: ?>

            <div class="alert alert-danger m-2">
                File sidebar tidak ditemukan.
            </div>

        <?php endif; ?>

    </aside>

    <!-- KONTEN -->
    <main class="content-area">

        <div class="container-fluid">

            <div class="mb-4">

                <h2 class="fw-bold">
                    Laporan Pelanggaran
                </h2>

                <p class="text-muted mb-0">
                    Laporan pelanggaran siswa di kelas yang Anda walikan.
                </p>

            </div>

            <div id="area-cetak">

                <!-- KARTU TOTAL -->
                <div class="row g-3 mb-4">

                    <div class="col-md-6">

                        <div class="card stat-card h-100">

                            <div class="card-body d-flex align-items-center gap-3">

                                <div class="stat-icon">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>

                                <div>
                                    <div class="text-muted">
                                        Total Pelanggaran
                                    </div>

                                    <h3 class="fw-bold mb-0">
                                        <?= $totalPelanggaran ?>
                                    </h3>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="card stat-card h-100">

                            <div class="card-body d-flex align-items-center gap-3">

                                <div class="stat-icon">
                                    <i class="bi bi-star-fill"></i>
                                </div>

                                <div>
                                    <div class="text-muted">
                                        Total Poin
                                    </div>

                                    <h3 class="fw-bold mb-0">
                                        <?= $totalPoin ?>
                                    </h3>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- FILTER -->
                <div class="card shadow-sm border-0 mb-4 d-print-none">

                    <div class="card-body">

                        <form method="GET" action="laporan.php">

                            <div class="row g-3">

                                <div class="col-md-3">

                                    <label class="form-label">
                                        Tahun Ajaran
                                    </label>

                                    <select
                                        name="tahun_ajaran"
                                        class="form-select"
                                    >

                                        <option value="">
                                            Pilih Tahun Ajaran
                                        </option>

                                        <?php foreach ($tahunList as $th): ?>

                                            <option
                                                value="<?= (int)$th['id'] ?>"
                                                <?= $tahunId === (int)$th['id'] ? 'selected' : '' ?>
                                            >
                                                <?= e($th['nama_tahun']) ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                                <div class="col-md-3">

                                    <label class="form-label">
                                        Kelas
                                    </label>

                                    <select
                                        name="kelas"
                                        class="form-select"
                                    >

                                        <option value="">
                                            Pilih Kelas
                                        </option>

                                        <?php foreach ($kelasList as $kl): ?>

                                            <option
                                                value="<?= (int)$kl['id'] ?>"
                                                <?= $kelasId === (int)$kl['id'] ? 'selected' : '' ?>
                                            >
                                                <?= e($kl['nama']) ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                                <div class="col-md-3">

                                    <label class="form-label">
                                        Siswa
                                    </label>

                                    <select
                                        name="siswa"
                                        class="form-select"
                                    >

                                        <option value="0">
                                            Semua Siswa
                                        </option>

                                        <?php foreach ($siswaList as $s): ?>

                                            <option
                                                value="<?= (int)$s['id'] ?>"
                                                <?= $siswaId === (int)$s['id'] ? 'selected' : '' ?>
                                            >
                                                <?= e($s['nama']) ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                                <div class="col-md-3">

                                    <label class="form-label">
                                        Tanggal
                                    </label>

                                    <input
                                        type="date"
                                        name="tanggal"
                                        class="form-control"
                                        value="<?= e($tanggal) ?>"
                                    >

                                </div>

                            </div>

                            <div class="mt-3 d-flex gap-2">

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    <i class="bi bi-search"></i>
                                    Cari
                                </button>

                                <a
                                    href="laporan.php"
                                    class="btn btn-secondary"
                                >
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                    Reset
                                </a>

                            </div>

                        </form>

                    </div>

                </div>

                <!-- DATA LAPORAN -->
                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">

                            <div>

                                <h5 class="fw-bold mb-1">
                                    <i class="bi bi-file-earmark-text"></i>
                                    Data Laporan
                                </h5>

                                <div class="text-muted small">
                                    Guru: <?= e($_SESSION['name'] ?? $_SESSION['nama'] ?? 'Pengguna') ?>
                                </div>

                                <div class="text-muted small">
                                    Kelas: <?= e($namaKelas) ?>
                                    |
                                    Tahun Ajaran: <?= e($namaTahun) ?>
                                </div>

                            </div>

                            <div class="d-flex gap-2 d-print-none">

                                <a
                                    href="laporan.php?<?= e($queryFilter) ?>&export=excel"
                                    class="btn btn-success"
                                >
                                    <i class="bi bi-file-earmark-excel"></i>
                                    Export Excel
                                </a>

                                <button
                                    type="button"
                                    onclick="window.print()"
                                    class="btn btn-dark"
                                >
                                    <i class="bi bi-printer"></i>
                                    Cetak
                                </button>

                            </div>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped align-middle mb-0">

                                <thead class="table-dark">

                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Pelanggaran</th>
                                        <th>Poin</th>
                                        <th>Keterangan</th>
                                        <th>Tindakan</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php if (!empty($dataLaporan)): ?>

                                        <?php foreach ($dataLaporan as $i => $row): ?>

                                            <tr>

                                                <td><?= $i + 1 ?></td>

                                                <td>
                                                    <?= !empty($row['tanggal'])
                                                        ? e(date('d-m-Y', strtotime($row['tanggal'])))
                                                        : '-' ?>
                                                </td>

                                                <td>
                                                    <?= e($row['nama_siswa']) ?>
                                                </td>

                                                <td>
                                                    <?= e($row['nama_kelas']) ?>
                                                </td>

                                                <td>
                                                    <?= e($row['nama_pelanggaran']) ?>
                                                </td>

                                                <td>
                                                    <span class="badge bg-danger">
                                                        <?= (int)$row['poin'] ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <?= e($row['keterangan']) ?>
                                                </td>

                                                <td>
                                                    <?= e($row['tindakan']) ?>
                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="bi bi-inbox fs-3 text-muted"></i>

                                                <div class="text-muted mt-2">
                                                    Tidak ada data pelanggaran.
                                                </div>
                                            </td>
                                        </tr>

                                    <?php endif; ?>

                                </tbody>

                                <?php if (!empty($dataLaporan)): ?>

                                    <tfoot class="table-light fw-bold">

                                        <tr>

                                            <td colspan="5" class="text-end">
                                                Total
                                            </td>

                                            <td>
                                                <span class="badge bg-danger">
                                                    <?= $totalPoin ?> Poin
                                                </span>
                                            </td>

                                            <td colspan="2">
                                                <?= $totalPelanggaran ?> Pelanggaran
                                            </td>

                                        </tr>

                                    </tfoot>

                                <?php endif; ?>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
