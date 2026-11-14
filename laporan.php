
<?php
session_start();

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/cek_session.php';

// ==================================================
// 1. IDENTITAS SEKOLAH
// ==================================================
// Ganti bagian ini sesuai identitas sekolah sebenarnya.
$namaSekolah = '[Smk Muhammadiyah]';
$alamatSekolah = '[l. Rumah Sakit No. 29, Kelurahan Empangsari, Kecamatan Tawang, Kota Tasikmalaya, Jawa Barat]';
$emailSekolah = '[smksmuhammadiyahtsm@gmail.com]';
$teleponSekolah = '[327380]';
$namaKepalaSekolah = '[Wawan Kurniawan, S.Pd.I]';
$nipKepalaSekolah = '[197106172021011917]';
$tempatCetak = '[KOTA/KABUPATEN]';

// ==================================================
// 2. LOGIN DAN HAK AKSES
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
// 3. FUNGSI BANTU
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
        die('Query gagal: ' . e(mysqli_error($koneksi)));
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
        die('Eksekusi gagal: ' . e(mysqli_stmt_error($stmt)));
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

function formatTanggal($tanggal)
{
    if (empty($tanggal)) {
        return '-';
    }

    $waktu = strtotime($tanggal);

    return $waktu ? date('d-m-Y', $waktu) : '-';
}

// ==================================================
// 4. MENENTUKAN KOLOM TAHUN AJARAN
// ==================================================
if (kolomAda($koneksi, 't_tahun_ajaran', 'nama')) {
    $kolomTahun = 'nama';
} elseif (kolomAda($koneksi, 't_tahun_ajaran', 'nama_tahun_ajaran')) {
    $kolomTahun = 'nama_tahun_ajaran';
} else {
    die('Kolom nama tahun ajaran tidak ditemukan.');
}

// ==================================================
// 5. MENGAMBIL DATA GURU LOGIN
// ==================================================
$guruId = 0;
$namaWaliKelas = $_SESSION['name'] ?? '........................';

if ($isWali) {
    $dataGuru = ambilData(
        $koneksi,
        "SELECT id, nama
         FROM t_guru
         WHERE user_id = ?
         LIMIT 1",
        'i',
        [$userId]
    );

    if (!empty($dataGuru)) {
        $guruId = (int)$dataGuru[0]['id'];
        $namaWaliKelas = $dataGuru[0]['nama'];
    }
}

// ==================================================
// 6. DAFTAR TAHUN AJARAN
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
// 7. FILTER TAHUN AJARAN
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
// 8. DAFTAR KELAS
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
// 9. FILTER KELAS
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
// 10. DAFTAR SISWA
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
// 11. FILTER SISWA DAN TANGGAL
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
// 12. NAMA TAHUN AJARAN DAN KELAS
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
// 13. MENGAMBIL DATA LAPORAN
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
// 14. URL FILTER
// ==================================================
$queryFilter = http_build_query([
    'tahun_ajaran' => $tahunId,
    'kelas' => $kelasId,
    'siswa' => $siswaId,
    'tanggal' => $tanggal
]);

// ==================================================
// 15. EXPORT EXCEL
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
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
        <h3>LAPORAN REKAPITULASI PELANGGARAN SISWA</h3>
        <p>Sekolah: <?= e($namaSekolah) ?></p>
        <p>Tahun Ajaran: <?= e($namaTahun) ?></p>
        <p>Kelas: <?= e($namaKelas) ?></p>
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
                <?php if (!empty($dataLaporan)): ?>
                    <?php foreach ($dataLaporan as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e(formatTanggal($row['tanggal'])) ?></td>
                            <td><?= e($row['nama_siswa']) ?></td>
                            <td><?= e($row['nama_kelas']) ?></td>
                            <td><?= e($row['nama_pelanggaran']) ?></td>
                            <td><?= (int)$row['poin'] ?></td>
                            <td><?= e($row['keterangan']) ?></td>
                            <td><?= e($row['tindakan']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">Tidak ada data laporan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5">Total</th>
                    <th><?= $totalPoin ?></th>
                    <th colspan="2"><?= $totalPelanggaran ?> Pelanggaran</th>
                </tr>
            </tfoot>
        </table>
    </body>
    </html>
    <?php
    exit;
}

// ==================================================
// 16. SIDEBAR SESUAI ROLE
// ==================================================
if ($isAdmin) {
    $sidebarCandidates = [
        __DIR__ . '/layout/sidebar.php',
        __DIR__ . '/layout/sidebar_admin.php'
    ];
} else {
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

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laporan Pelanggaran Siswa</title>

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

        .layout-wrapper {
            display: flex;
            align-items: stretch;
            width: 100%;
            min-height: 100vh;
        }

        .sidebar-holder {
            flex: 0 0 275px;
            width: 275px;
            min-width: 275px;
            min-height: 100vh;
            position: relative;
            z-index: 10;
        }

        .sidebar-holder > * {
            max-width: 275px;
        }

        .content-area {
            flex: 1 1 0;
            min-width: 0;
            padding: 24px;
            position: relative;
        }

        .container-fluid {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
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
        }

        .print-header,
        .print-signature {
            display: none;
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

        /* ==================================
           FORMAT DOKUMEN RESMI SAAT CETAK
        ================================== */
       
@media print {

    @page {
        size: A4 landscape;
        margin: 8mm 10mm;
    }

    html,
    body {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
        color: black !important;
        font-family: "Times New Roman", serif !important;
        font-size: 9pt !important;
    }

    .sidebar-holder,
    .d-print-none,
    .btn,
    button,
    .screen-title,
    .summary-cards {
        display: none !important;
    }

    .layout-wrapper {
        display: block !important;
        width: 100% !important;
        min-height: 0 !important;
    }

    .content-area,
    .container-fluid {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    #area-cetak {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .print-header,
    .print-signature {
        display: block !important;
    }

    .print-header {
        margin-bottom: 5px !important;
    }

    .print-header h6 {
        font-size: 10pt !important;
        margin-bottom: 2px !important;
    }

    .print-header h4 {
        font-size: 15pt !important;
        margin-bottom: 3px !important;
    }

    .print-header p {
        font-size: 9pt !important;
        margin-bottom: 3px !important;
    }

    .print-title {
        font-size: 12pt !important;
        text-align: center !important;
        font-weight: bold !important;
        text-decoration: underline !important;
        margin: 8px 0 3px !important;
    }

    .print-subtitle {
        font-size: 9pt !important;
        text-align: center !important;
        margin-bottom: 8px !important;
    }

    .print-header table {
        width: 100% !important;
        margin-bottom: 6px !important;
        font-size: 9pt !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .card-body {
        padding: 0 !important;
    }

    .table-responsive {
        overflow: visible !important;
    }

    .table {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: collapse !important;
        table-layout: auto !important;
        font-family: "Times New Roman", serif !important;
        font-size: 8.5pt !important;
    }

    .table th,
    .table td {
        border: 1px solid black !important;
        padding: 3px 4px !important;
        color: black !important;
        background: white !important;
        white-space: normal !important;
        vertical-align: middle !important;
    }

    .table thead {
        display: table-header-group !important;
    }

    .table tfoot {
        display: table-row-group !important;
    }

    .table thead th {
        text-align: center !important;
        font-weight: bold !important;
    }

    .table tr {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }

    .print-signature {
        margin-top: 12px !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    .print-signature .row {
        display: flex !important;
        width: 100% !important;
        margin: 0 !important;
    }

    .print-signature .col-6 {
        width: 50% !important;
        flex: 0 0 50% !important;
    }

    .print-signature p {
        font-size: 9pt !important;
        margin-bottom: 2px !important;
    }

    .signature-space {
        height: 35px !important;
    }

    .badge {
        color: black !important;
        background: white !important;
        border: none !important;
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

            <div class="screen-title mb-4">
                <h2 class="fw-bold">Laporan Pelanggaran</h2>

                <p class="text-muted mb-0">
                    <?php if ($isWali): ?>
                        Laporan pelanggaran siswa di kelas yang Anda walikan.
                    <?php else: ?>
                        Laporan seluruh pelanggaran siswa.
                    <?php endif; ?>
                </p>
            </div>

            <div id="area-cetak">

                <!-- ==================================
                     KOP SURAT DOKUMEN SEKOLAH
                =================================== -->
                <div class="print-header">

                    <div class="text-center">

                        <h4 class="fw-bold mb-1">
                            <?= e($namaSekolah) ?>
                        </h4>

                        <p class="mb-1">
                            <?= e($alamatSekolah) ?>
                        </p>

                        <p class="mb-2">
                            Email: <?= e($emailSekolah) ?>
                            |
                            Telepon: <?= e($teleponSekolah) ?>
                        </p>

                        <hr style="border: 2px solid black; opacity: 1;">
                        <hr style="border: 1px solid black; opacity: 1; margin-top: -8px;">

                    </div>

                    <h5 class="print-title">
                        LAPORAN REKAPITULASI PELANGGARAN SISWA
                    </h5>

                    <p class="print-subtitle">
                        TAHUN AJARAN <?= e($namaTahun) ?>
                    </p>

                    <table class="table table-borderless mb-3">
                        <tbody>
                            <tr>
                                <td width="18%">Nama Sekolah</td>
                                <td width="2%">:</td>
                                <td><?= e($namaSekolah) ?></td>
                            </tr>

                            <tr>
                                <td>Kelas</td>
                                <td>:</td>
                                <td><?= e($namaKelas) ?></td>
                            </tr>

                            <tr>
                                <td>Wali Kelas</td>
                                <td>:</td>
                                <td><?= e($namaWaliKelas) ?></td>
                            </tr>

                            <tr>
                                <td>Tanggal Cetak</td>
                                <td>:</td>
                                <td><?= date('d-m-Y') ?></td>
                            </tr>
                        </tbody>
                    </table>

                </div>

                <!-- ==================================
                     KARTU RINGKASAN UNTUK LAYAR
                =================================== -->
                <div class="row g-3 mb-4 summary-cards">

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

                <!-- ==================================
                     FILTER LAPORAN
                =================================== -->
                <div class="card shadow-sm mb-4 d-print-none">

                    <div class="card-body">

                        <form method="GET" action="laporan.php">

                            <div class="row g-3">

                                <div class="col-md-3">
                                    <label class="form-label">
                                        Tahun Ajaran
                                    </label>

                                    <select name="tahun_ajaran" class="form-select">
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

                                    <select name="kelas" class="form-select">
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

                                    <select name="siswa" class="form-select">
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

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                    Cari
                                </button>

                                <a href="laporan.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                    Reset
                                </a>

                            </div>

                        </form>

                    </div>
                </div>

                <!-- ==================================
                     TABEL DATA LAPORAN
                =================================== -->
                <div class="card shadow-sm">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">

                            <div>
                                <h5 class="fw-bold mb-1">
                                    <i class="bi bi-file-earmark-text"></i>
                                    Data Laporan
                                </h5>

                                <div class="text-muted small">
                                    <?= $isAdmin ? 'Administrator' : 'Wali Kelas' ?>:
                                    <?= e($namaWaliKelas) ?>
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
                                        <th class="text-center" width="4%">No</th>
                                        <th class="text-center" width="11%">Tanggal</th>
                                        <th>Nama Siswa</th>
                                        <th class="text-center">Kelas</th>
                                        <th>Jenis Pelanggaran</th>
                                        <th class="text-center">Poin</th>
                                        <th>Keterangan</th>
                                        <th>Tindakan</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php if (!empty($dataLaporan)): ?>

                                        <?php foreach ($dataLaporan as $i => $row): ?>
                                            <tr>
                                                <td class="text-center">
                                                    <?= $i + 1 ?>
                                                </td>

                                                <td class="text-center">
                                                    <?= e(formatTanggal($row['tanggal'])) ?>
                                                </td>

                                                <td>
                                                    <?= e($row['nama_siswa']) ?>
                                                </td>

                                                <td class="text-center">
                                                    <?= e($row['nama_kelas']) ?>
                                                </td>

                                                <td>
                                                    <?= e($row['nama_pelanggaran']) ?>
                                                </td>

                                                <td class="text-center">
                                                    <?= (int)$row['poin'] ?>
                                                </td>

                                                <td>
                                                    <?= e($row['keterangan'] ?: '-') ?>
                                                </td>

                                                <td>
                                                    <?= e($row['tindakan'] ?: '-') ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                Tidak ada data pelanggaran.
                                            </td>
                                        </tr>

                                    <?php endif; ?>

                                </tbody>

                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="5" class="text-end">
                                            TOTAL
                                        </td>

                                        <td class="text-center">
                                            <?= $totalPoin ?>
                                        </td>

                                        <td colspan="2">
                                            <?= $totalPelanggaran ?> Pelanggaran
                                        </td>
                                    </tr>
                                </tfoot>

                            </table>

                        </div>

                    </div>
                </div>

                <!-- ==================================
                     TANDA TANGAN DOKUMEN
                =================================== -->
                <div class="print-signature">

                    <div class="row">

                        <div class="col-6 text-center">

                            <p>Mengetahui,</p>

                            <p class="fw-bold">
                                Kepala Sekolah
                            </p>

                            <br><br><br>

                            <p class="fw-bold mb-0">
                                <?= e($namaKepalaSekolah) ?>
                            </p>

                            <p class="mb-0">
                                NIP. <?= e($nipKepalaSekolah) ?>
                            </p>

                        </div>

                        <div class="col-6 text-center">

                            <p>
                                <?= e($tempatCetak) ?>,
                                <?= date('d-m-Y') ?>
                            </p>

                            <p class="fw-bold">
                                Wali Kelas <?= e($namaKelas) ?>
                            </p>

                            <br><br><br>

                            <p class="fw-bold mb-0">
                                <?= e($namaWaliKelas) ?>
                            </p>

                            <p class="mb-0">
                                NIP. ....................................
                            </p>

                        </div>

                    </div>

                </div>

            </div>
            <!-- AKHIR AREA CETAK -->

        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
