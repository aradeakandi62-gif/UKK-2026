
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$role = strtolower(trim($_SESSION['role'] ?? ''));

if (!in_array($role, ['guru', 'wali_kelas', 'wali kelas', 'walikelas'], true)) {
    die("Anda tidak memiliki akses.");
}

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Nomor surat tidak valid.");
}

$stmt = mysqli_prepare($koneksi, "
    SELECT *
    FROM t_surat_pemanggilan
    WHERE id = ?
    LIMIT 1
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    die("Surat pemanggilan tidak ditemukan.");
}

$bulanIndonesia = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

$tanggalSurat = strtotime($data['created_at']);
$tanggalPertemuan = strtotime($data['tanggal_pertemuan']);

$tanggalSuratText =
    date('d', $tanggalSurat) . ' ' .
    $bulanIndonesia[(int)date('n', $tanggalSurat)] . ' ' .
    date('Y', $tanggalSurat);

$tanggalPertemuanText =
    date('d', $tanggalPertemuan) . ' ' .
    $bulanIndonesia[(int)date('n', $tanggalPertemuan)] . ' ' .
    date('Y', $tanggalPertemuan);

$tahap = (int)$data['tahap'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Pemanggilan Orang Tua</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #f1f3f5;
        }

        .surat {
            background: white;
            max-width: 850px;
            margin: 25px auto;
            padding: 45px 60px;
            min-height: 1050px;
        }

        .garis {
            border-top: 3px solid black;
            margin-top: 15px;
            margin-bottom: 25px;
        }

        .isi-surat {
            line-height: 1.8;
            text-align: justify;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm;
            }

            body {
                background: white !important;
            }

            .surat {
                max-width: 100%;
                margin: 0;
                padding: 0;
                min-height: auto;
            }

            .d-print-none {
                display: none !important;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="d-flex justify-content-center gap-2 mt-4 d-print-none">

        <button onclick="window.print()" class="btn btn-primary">
            Cetak Surat
        </button>

        <a href="rekap_poin.php" class="btn btn-secondary">
            Kembali ke Rekap
        </a>

    </div>

    <div class="surat">

        <!-- KOP SURAT -->
        <div class="text-center">
            <h5 class="fw-bold mb-1">
                SMK MUHAMMADIYAH TASIKMALAYA
            </h5>

            <p class="mb-1">
                Jl. Rumah Sakit No. 29, Kelurahan Empangsari (Kel. Empang), Kecamatan Tawang, Kota Tasikmalaya
            </p>

            <p class="mb-0">
                (0265) 327380 / smksmuhammadiyahtsm@gmail.com 
            </p>
        </div>

        <div class="garis"></div>

        <!-- JUDUL -->
        <div class="text-center mb-4">
            <h5 class="fw-bold text-decoration-underline">
                SURAT PEMANGGILAN ORANG TUA/WALI
            </h5>

            <p class="mb-0">
                Nomor: <?= e($data['id']) ?>/SP/<?= date('Y') ?>
            </p>
        </div>

        <div class="mb-4">
            <p class="mb-1">
                Tasikmalaya, <?= e($tanggalSuratText) ?>
            </p>

            <p class="mb-1">
                Perihal:
                <strong>Pemanggilan Orang Tua/Wali Tahap <?= $tahap ?></strong>
            </p>
        </div>

        <div class="isi-surat">
            <p>
                Kepada Yth.<br>
                Bapak/Ibu Orang Tua/Wali dari:
            </p>
        </div>

        <!-- IDENTITAS SISWA -->
        <table class="table table-borderless table-sm w-auto mb-4">
            <tr>
                <td width="160">Nama Siswa</td>
                <td width="10">:</td>
                <td><strong><?= e($data['nama_siswa']) ?></strong></td>
            </tr>

            <tr>
                <td>NIS</td>
                <td>:</td>
                <td><?= e($data['nis']) ?></td>
            </tr>

            <tr>
                <td>Kelas</td>
                <td>:</td>
                <td><?= e($data['nama_kelas']) ?></td>
            </tr>

            <tr>
                <td>Jumlah Pelanggaran</td>
                <td>:</td>
                <td><?= (int)$data['jumlah_pelanggaran'] ?> kali</td>
            </tr>

            <tr>
                <td>Total Poin</td>
                <td>:</td>
                <td><strong><?= (int)$data['total_poin'] ?> Poin</strong></td>
            </tr>
        </table>

        <div class="isi-surat">
            <p>Dengan hormat,</p>

            <p>
                Berdasarkan hasil pencatatan dan rekapitulasi pelanggaran
                tata tertib sekolah, siswa tersebut telah memperoleh
                total <strong><?= (int)$data['total_poin'] ?> poin pelanggaran</strong>.
            </p>

            <p>
                Sehubungan dengan hal tersebut, kami mengundang
                Bapak/Ibu Orang Tua/Wali untuk hadir ke sekolah guna
                melakukan konsultasi, pembinaan, serta membahas langkah
                penanganan lebih lanjut terhadap siswa yang bersangkutan.
            </p>

            <p>
                Pertemuan akan dilaksanakan pada:
            </p>
        </div>

        <!-- JADWAL YANG SUDAH DISIMPAN -->
        <table class="table table-borderless table-sm w-auto mb-4">
            <tr>
                <td width="160">Hari/Tanggal</td>
                <td width="10">:</td>
                <td><?= e(date('l', $tanggalPertemuan)) ?>, <?= e($tanggalPertemuanText) ?></td>
            </tr>

            <tr>
                <td>Waktu</td>
                <td>:</td>
                <td><?= e(substr($data['waktu_pertemuan'], 0, 5)) ?> WIB</td>
            </tr>

            <tr>
                <td>Tempat</td>
                <td>:</td>
                <td><?= e($data['tempat_pertemuan']) ?></td>
            </tr>
        </table>

        <?php if (!empty($data['catatan'])): ?>
            <div class="isi-surat">
                <p>
                    <strong>Keperluan:</strong>
                    <?= nl2br(e($data['catatan'])) ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="isi-surat">
            <p>
                Demikian surat pemanggilan ini kami sampaikan.
                Atas perhatian dan kerja sama Bapak/Ibu, kami
                mengucapkan terima kasih.
            </p>
        </div>

        <!-- TANDA TANGAN -->
        <div class="row mt-5">
            <div class="col-6"></div>

            <div class="col-6 text-center">
                <p class="mb-1">Hormat kami,</p>
                <p class="mb-1">Pihak Sekolah</p>

                <br><br><br>

                <p class="fw-bold mb-0">
                    ........................................
                </p>

                <p class="mb-0">
                    NIP. ..................................
                </p>
            </div>
        </div>

        <div class="mt-5 pt-3 border-top">
            <small class="text-muted">
                Surat Pemanggilan Tahap <?= $tahap ?>.
            </small>
        </div>

    </div>
</div>

</body>
</html>
