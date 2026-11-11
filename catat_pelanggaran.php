
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// =====================================
// CEK LOGIN GURU
// =====================================

if (strtolower(trim($_SESSION['role'] ?? '')) !== 'guru') {
    header("Location: dashboard.php");
    exit;
}

$user_id = (int)($_SESSION['id'] ?? 0);

// =====================================
// FUNGSI
// =====================================

function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function cariData($daftar, $id) {
    foreach ($daftar as $item) {
        if ((int)$item['id'] === (int)$id) {
            return $item;
        }
    }

    return null;
}

// =====================================
// AMBIL DATA GURU YANG LOGIN
// =====================================

$stmtGuru = mysqli_prepare($koneksi, "
    SELECT id, nama
    FROM t_guru
    WHERE user_id = ?
    LIMIT 1
");

if (!$stmtGuru) {
    die("Query guru gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmtGuru, "i", $user_id);
mysqli_stmt_execute($stmtGuru);

$resultGuru = mysqli_stmt_get_result($stmtGuru);
$dataGuru = mysqli_fetch_assoc($resultGuru);

mysqli_stmt_close($stmtGuru);

if (!$dataGuru) {
    die("Data guru tidak ditemukan. Periksa hubungan t_guru.user_id dengan akun login.");
}

$guru_id = (int)$dataGuru['id'];
$nama_guru = $dataGuru['nama'];

// =====================================
// AMBIL DATA SISWA BESERTA PENEMPATAN
// KELAS DAN TAHUN AJARAN
// =====================================

$siswaList = mysqli_query($koneksi, "
    SELECT
        s.id,
        s.nis,
        s.nama,
        ks.kelas_id,
        k.nama AS nama_kelas,
        ks.tahun_ajaran_id,
        ta.nama AS nama_tahun_ajaran
    FROM t_siswa s
    LEFT JOIN t_kelas_siswa ks
        ON ks.siswa_id = s.id
        AND ks.status_aktif = 1
    LEFT JOIN t_kelas k
        ON k.id = ks.kelas_id
    LEFT JOIN t_tahun_ajaran ta
        ON ta.id = ks.tahun_ajaran_id
    WHERE s.status_aktif = 1
    ORDER BY s.nama ASC
");

if (!$siswaList) {
    die("Query siswa gagal: " . mysqli_error($koneksi));
}

$siswaData = mysqli_fetch_all($siswaList, MYSQLI_ASSOC);

// =====================================
// AMBIL DATA JENIS PELANGGARAN
// =====================================

$queryPelanggaran = mysqli_query($koneksi, "
    SELECT
        id,
        kode,
        nama,
        pelanggaran_kategori_id,
        poin
    FROM t_pelanggaran
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

if (!$queryPelanggaran) {
    die("Query pelanggaran gagal: " . mysqli_error($koneksi));
}

$pelanggaranData = mysqli_fetch_all(
    $queryPelanggaran,
    MYSQLI_ASSOC
);

// =====================================
// NILAI AWAL FORM
// =====================================

$error = '';

$siswa_id = 0;
$pelanggaran_id = 0;
$tanggal = date('Y-m-d');
$keterangan = '';
$tindakan = '';
$status = 'tercatat';

// =====================================
// PROSES SIMPAN
// =====================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $siswa_id = (int)($_POST['siswa_id'] ?? 0);

    $pelanggaran_id = (int)($_POST['pelanggaran_id'] ?? 0);

    $tanggal = trim($_POST['tanggal'] ?? '');

    $keterangan = trim($_POST['keterangan'] ?? '');

    $tindakan = trim($_POST['tindakan'] ?? '');

    $status = trim($_POST['status'] ?? '');

    // Cari data asli berdasarkan ID
    $siswa = cariData($siswaData, $siswa_id);

    $pelanggaran = cariData(
        $pelanggaranData,
        $pelanggaran_id
    );

    $daftarStatus = [
        'tercatat',
        'ditindaklanjuti',
        'selesai'
    ];

    // Validasi
    if (!$siswa) {

        $error = "Silakan pilih siswa yang memiliki penempatan kelas dan tahun ajaran.";

    } elseif (!$pelanggaran) {

        $error = "Silakan pilih jenis pelanggaran.";

    } elseif ($tanggal === '' || $tindakan === '') {

        $error = "Tanggal dan tindakan wajib diisi.";

    } elseif (!in_array($status, $daftarStatus, true)) {

        $error = "Status pelanggaran tidak valid.";

    } else {

        // =====================================
        // DATA OTOMATIS DARI SISWA
        // =====================================

        $nama_siswa = $siswa['nama'];

        $kelas_id = (int)$siswa['kelas_id'];

        $nama_kelas = $siswa['nama_kelas'];

        $tahun_ajaran_id = (int)$siswa['tahun_ajaran_id'];

        // =====================================
        // DATA OTOMATIS DARI PELANGGARAN
        // =====================================

        $nama_pelanggaran = $pelanggaran['nama'];

        $kategori_id = (int)$pelanggaran['pelanggaran_kategori_id'];

        // Poin diambil langsung dari database
        $poin = (int)$pelanggaran['poin'];

        // =====================================
        // GURU OTOMATIS DARI AKUN LOGIN
        // =====================================

        // $guru_id dan $nama_guru sudah diperoleh
        // dari akun yang sedang login.

        // =====================================
        // SIMPAN KE DATABASE
        // =====================================

        $stmt = mysqli_prepare($koneksi, "
            INSERT INTO t_pelanggaran_siswa (
                tahun_ajaran_id,
                siswa_id,
                nama_siswa,
                kelas_id,
                nama_kelas,
                pelanggaran_id,
                nama_pelanggaran,
                pelanggaran_kategori_id,
                guru_id,
                nama_guru,
                tanggal,
                keterangan,
                poin,
                tindakan,
                status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {

            $error = "Query gagal disiapkan: " . mysqli_error($koneksi);

        } else {

            mysqli_stmt_bind_param(
                $stmt,
                "iisisisiisssiss",
                $tahun_ajaran_id,
                $siswa_id,
                $nama_siswa,
                $kelas_id,
                $nama_kelas,
                $pelanggaran_id,
                $nama_pelanggaran,
                $kategori_id,
                $guru_id,
                $nama_guru,
                $tanggal,
                $keterangan,
                $poin,
                $tindakan,
                $status
            );

            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                header("Location: riwayat_pelanggaran.php?pesan=berhasil");
                exit;

            } else {

                $error = "Gagal menyimpan: " .
                         mysqli_stmt_error($stmt);

                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Catat Pelanggaran Siswa</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Select2 untuk pencarian siswa -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
          rel="stylesheet">

    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
        }

        .select2-container--default .select2-selection--single
        .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
        }

        .select2-container--default .select2-selection--single
        .select2-selection__arrow {
            height: 36px;
        }
    </style>

</head>

<body class="bg-light">

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php
    include 'layout/sidebar_guru.php';
    ?>

    <!-- KONTEN UTAMA -->
    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <div class="container-fluid">

            <h3 class="fw-bold mb-1">
                Catat Pelanggaran
            </h3>

            <p class="text-muted mb-4">
                Tambahkan data pelanggaran siswa ke dalam sistem.
            </p>

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">

                    <h5 class="fw-bold mb-0">
                        Form Tambah Pelanggaran Siswa
                    </h5>

                </div>

                <div class="card-body p-4">

                    <?php if ($error !== ''): ?>

                        <div class="alert alert-danger">
                            <?= e($error) ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST"
                          action="catat_pelanggaran.php">

                        <div class="row">

                            <!-- SISWA -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Nama Siswa
                                </label>

                                <select name="siswa_id"
                                        id="siswa_id"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Cari Nama atau NIS Siswa --
                                    </option>

                                    <?php foreach ($siswaData as $siswa): ?>

                                        <option
                                            value="<?= (int)$siswa['id'] ?>"
                                            data-kelas-id="<?= (int)$siswa['kelas_id'] ?>"
                                            data-kelas="<?= e($siswa['nama_kelas']) ?>"
                                            data-tahun-id="<?= (int)$siswa['tahun_ajaran_id'] ?>"
                                            data-tahun="<?= e($siswa['nama_tahun_ajaran']) ?>"
                                            <?= $siswa_id == $siswa['id'] ? 'selected' : '' ?>>

                                            <?= e($siswa['nis']) ?> -
                                            <?= e($siswa['nama']) ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                                <small class="text-muted">
                                    Ketik nama atau NIS untuk mencari siswa.
                                </small>

                            </div>

                            <!-- TAHUN AJARAN OTOMATIS -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Tahun Ajaran
                                </label>

                                <input type="text"
                                       id="nama_tahun_ajaran"
                                       class="form-control"
                                       placeholder="Otomatis dari siswa"
                                       readonly>

                                <input type="hidden"
                                       name="tahun_ajaran_id"
                                       id="tahun_ajaran_id">

                            </div>

                            <!-- KELAS OTOMATIS -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Kelas
                                </label>

                                <input type="text"
                                       id="nama_kelas"
                                       class="form-control"
                                       placeholder="Otomatis dari siswa"
                                       readonly>

                                <input type="hidden"
                                       name="kelas_id"
                                       id="kelas_id">

                            </div>

                            <!-- TANGGAL -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Tanggal Pelanggaran
                                </label>

                                <input type="date"
                                       name="tanggal"
                                       class="form-control"
                                       value="<?= e($tanggal) ?>"
                                       required>

                            </div>

                            <!-- JENIS PELANGGARAN -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Jenis Pelanggaran
                                </label>

                                <select name="pelanggaran_id"
                                        id="pelanggaran_id"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Pelanggaran --
                                    </option>

                                    <?php foreach ($pelanggaranData as $pelanggaran): ?>

                                        <option
                                            value="<?= (int)$pelanggaran['id'] ?>"
                                            data-poin="<?= (int)$pelanggaran['poin'] ?>"
                                            <?= $pelanggaran_id == $pelanggaran['id'] ? 'selected' : '' ?>>

                                            <?= e($pelanggaran['kode']) ?> -
                                            <?= e($pelanggaran['nama']) ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <!-- POIN OTOMATIS -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Poin Pelanggaran
                                </label>

                                <input type="number"
                                       id="poin"
                                       class="form-control"
                                       placeholder="Otomatis dari jenis pelanggaran"
                                       readonly>

                            </div>

                            <!-- GURU PENCATAT -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Guru Pencatat
                                </label>

                                <input type="text"
                                       class="form-control"
                                       value="<?= e($nama_guru) ?>"
                                       readonly>

                                <small class="text-muted">
                                    Otomatis mengikuti akun yang login.
                                </small>

                            </div>

                            <!-- TINDAKAN -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Tindakan
                                </label>

                                <select name="tindakan"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Tindakan --
                                    </option>

                                    <?php

                                    $daftarTindakan = [
                                        'Teguran lisan',
                                        'Teguran tertulis',
                                        'Pembinaan oleh wali kelas',
                                        'Pemanggilan siswa',
                                        'Pemanggilan orang tua',
                                        'Pemanggilan orang tua dan skorsing'
                                    ];

                                    foreach ($daftarTindakan as $pilihan):
                                    ?>

                                        <option value="<?= e($pilihan) ?>"
                                            <?= $tindakan === $pilihan ? 'selected' : '' ?>>

                                            <?= e($pilihan) ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <!-- STATUS -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Status Penanganan
                                </label>

                                <select name="status"
                                        class="form-select"
                                        required>

                                    <option value="tercatat"
                                        <?= $status === 'tercatat' ? 'selected' : '' ?>>
                                        Tercatat
                                    </option>

                                    <option value="ditindaklanjuti"
                                        <?= $status === 'ditindaklanjuti' ? 'selected' : '' ?>>
                                        Ditindaklanjuti
                                    </option>

                                    <option value="selesai"
                                        <?= $status === 'selesai' ? 'selected' : '' ?>>
                                        Selesai
                                    </option>

                                </select>

                            </div>

                            <!-- KETERANGAN -->
                            <div class="col-12 mb-3">

                                <label class="form-label">
                                    Keterangan
                                </label>

                                <textarea name="keterangan"
                                          class="form-control"
                                          rows="4"
                                          placeholder="Masukkan keterangan pelanggaran..."><?= e($keterangan) ?></textarea>

                            </div>

                        </div>

                        <hr>

                        <div class="d-flex gap-2">

                            <button type="submit"
                                    class="btn btn-primary">

                                Simpan Pelanggaran

                            </button>

                            <a href="riwayat_pelanggaran.php"
                               class="btn btn-secondary">

                                Kembali

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </main>

</div>

<!-- JQUERY -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- SELECT2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

// =====================================
// PENCARIAN SISWA
// =====================================

$('#siswa_id').select2({
    placeholder: '-- Cari Nama atau NIS Siswa --',
    allowClear: true,
    width: '100%',
    language: {
        noResults: function() {
            return 'Siswa tidak ditemukan';
        },
        searching: function() {
            return 'Sedang mencari...';
        }
    }
});

// =====================================
// OTOMATIS KELAS DAN TAHUN AJARAN
// =====================================

function tampilkanDataSiswa() {

    const pilihan = document.getElementById('siswa_id');

    const option = pilihan.options[pilihan.selectedIndex];

    if (!option || !option.value) {

        document.getElementById('nama_kelas').value = '';
        document.getElementById('kelas_id').value = '';

        document.getElementById('nama_tahun_ajaran').value = '';
        document.getElementById('tahun_ajaran_id').value = '';

        return;
    }

    document.getElementById('nama_kelas').value =
        option.dataset.kelas || '';

    document.getElementById('kelas_id').value =
        option.dataset.kelasId || '';

    document.getElementById('nama_tahun_ajaran').value =
        option.dataset.tahun || '';

    document.getElementById('tahun_ajaran_id').value =
        option.dataset.tahunId || '';
}

// =====================================
// OTOMATIS POIN PELANGGARAN
// =====================================

function tampilkanPoin() {

    const pilihan = document.getElementById('pelanggaran_id');

    const option = pilihan.options[pilihan.selectedIndex];

    document.getElementById('poin').value =
        option && option.value
        ? option.dataset.poin || ''
        : '';
}

// =====================================
// EVENT
// =====================================

$('#siswa_id').on('change', function() {
    tampilkanDataSiswa();
});

document.getElementById('pelanggaran_id')
    .addEventListener('change', tampilkanPoin);

// =====================================
// TAMPILKAN KEMBALI JIKA VALIDASI GAGAL
// =====================================

tampilkanDataSiswa();
tampilkanPoin();

</script>

</body>
</html>
