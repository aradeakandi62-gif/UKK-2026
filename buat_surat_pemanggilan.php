
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$role = strtolower(trim($_SESSION['role'] ?? ''));

// Hak akses Guru dan Wali Kelas
if (!in_array($role, ['guru', 'wali_kelas', 'wali kelas', 'walikelas'], true)) {
    die("Anda tidak memiliki akses.");
}

// Fungsi keamanan
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

$siswa_id = (int)($_GET['siswa_id'] ?? 0);
$kelas_id = (int)($_GET['kelas_id'] ?? 0);

$error = '';

if ($siswa_id <= 0 || $kelas_id <= 0) {
    header("Location: rekap_poin.php");
    exit;
}

// ======================================
// AMBIL DATA REKAP SISWA
// ======================================

$stmt = mysqli_prepare($koneksi, "
    SELECT
        siswa_id,
        nama_siswa,
        kelas_id,
        nama_kelas,
        COUNT(*) AS jumlah_pelanggaran,
        COALESCE(SUM(poin), 0) AS total_poin,
        (
            SELECT nis
            FROM t_siswa
            WHERE t_siswa.id = t_pelanggaran_siswa.siswa_id
            LIMIT 1
        ) AS nis
    FROM t_pelanggaran_siswa
    WHERE siswa_id = ?
      AND kelas_id = ?
      AND status = 'Selesai'
    GROUP BY
        siswa_id,
        nama_siswa,
        kelas_id,
        nama_kelas
    LIMIT 1
");

if (!$stmt) {
    die("Query gagal: " . e(mysqli_error($koneksi)));
}

mysqli_stmt_bind_param($stmt, "ii", $siswa_id, $kelas_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    die("Data siswa tidak ditemukan.");
}

// ======================================
// TENTUKAN TAHAP PEMANGGILAN
// ======================================

$totalPoin = (int)$data['total_poin'];

if ($totalPoin >= 100) {
    $tahap = 3;
} elseif ($totalPoin >= 75) {
    $tahap = 2;
} elseif ($totalPoin >= 50) {
    $tahap = 1;
} else {
    die("Siswa belum mencapai batas 50 poin.");
}

// ======================================
// PROSES SIMPAN JADWAL
// ======================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tanggal = trim($_POST['tanggal_pertemuan'] ?? '');
    $waktu = trim($_POST['waktu_pertemuan'] ?? '');
    $tempat = trim($_POST['tempat_pertemuan'] ?? '');
    $catatan = trim($_POST['catatan'] ?? '');

    $tanggalValid = DateTime::createFromFormat('Y-m-d', $tanggal);
    $waktuValid = DateTime::createFromFormat('H:i', $waktu);

    if (
        !$tanggalValid ||
        $tanggalValid->format('Y-m-d') !== $tanggal
    ) {
        $error = "Tanggal pertemuan tidak valid.";

    } elseif ($tanggal < date('Y-m-d')) {

        $error = "Tanggal pertemuan tidak boleh sebelum hari ini.";

    } elseif (
        !$waktuValid ||
        $waktuValid->format('H:i') !== $waktu
    ) {

        $error = "Waktu pertemuan tidak valid.";

    } elseif ($tempat === '') {

        $error = "Tempat pertemuan wajib diisi.";

    } else {

        $namaPetugas = $_SESSION['nama']
            ?? $_SESSION['nama_lengkap']
            ?? $_SESSION['name']
            ?? 'Petugas';

        $nis = $data['nis'] ?? '';
        $namaSiswa = $data['nama_siswa'];
        $namaKelas = $data['nama_kelas'];
        $jumlah = (int)$data['jumlah_pelanggaran'];

        $insert = mysqli_prepare($koneksi, "
            INSERT INTO t_surat_pemanggilan
            (
                siswa_id,
                kelas_id,
                nis,
                nama_siswa,
                nama_kelas,
                jumlah_pelanggaran,
                total_poin,
                tahap,
                tanggal_pertemuan,
                waktu_pertemuan,
                tempat_pertemuan,
                catatan,
                dibuat_oleh,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Dijadwalkan')
        ");

        if (!$insert) {

            $error = "Gagal menyiapkan query: " .
                     mysqli_error($koneksi);

        } else {

            mysqli_stmt_bind_param(
                $insert,
                "iisssiiisssss",
                $siswa_id,
                $kelas_id,
                $nis,
                $namaSiswa,
                $namaKelas,
                $jumlah,
                $totalPoin,
                $tahap,
                $tanggal,
                $waktu,
                $tempat,
                $catatan,
                $namaPetugas
            );

            if (mysqli_stmt_execute($insert)) {

                $suratId = mysqli_insert_id($koneksi);

                mysqli_stmt_close($insert);

                header("Location: surat_pemanggilan.php?id=" . $suratId);
                exit;

            } else {

                $error = "Gagal menyimpan jadwal: " .
                         mysqli_stmt_error($insert);

                mysqli_stmt_close($insert);
            }
        }
    }
}

// ======================================
// SIDEBAR
// ======================================

if (file_exists(__DIR__ . '/layout/sidebar_guru.php')) {
    $sidebar = 'layout/sidebar_guru.php';
} elseif (file_exists(__DIR__ . '/layout/sidebar_wali_kelas.php')) {
    $sidebar = 'layout/sidebar_wali_kelas.php';
} else {
    $sidebar = 'layout/sidebar.php';
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Jadwal Pemanggilan Orang Tua</title>

    <!-- BOOTSTRAP -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include __DIR__ . '/' . $sidebar; ?>

    <!-- KONTEN UTAMA -->
    <main
        class="flex-grow-1 p-4"
        style="margin-left: 275px; width: calc(100% - 275px); min-width: 0;">

        <!-- JUDUL -->
        <div class="mb-4">

            <h3 class="fw-bold mb-1">
                Jadwal Pemanggilan Orang Tua
            </h3>

            <p class="text-secondary mb-0">
                Atur jadwal pertemuan terlebih dahulu sebelum mencetak surat.
            </p>

        </div>

        <!-- PESAN ERROR -->
        <?php if ($error !== ''): ?>

            <div class="alert alert-danger alert-dismissible fade show">

                <?= e($error) ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

        <?php endif; ?>

        <!-- ================================= -->
        <!-- INFORMASI SISWA -->
        <!-- ================================= -->

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="fw-bold mb-0">
                    Informasi Siswa
                </h5>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <strong>NIS:</strong>

                        <?= e($data['nis'] ?? '-') ?>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>Nama:</strong>

                        <?= e($data['nama_siswa']) ?>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>Kelas:</strong>

                        <?= e($data['nama_kelas']) ?>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>Jumlah Pelanggaran:</strong>

                        <?= (int)$data['jumlah_pelanggaran'] ?> kali

                    </div>

                    <div class="col-md-6 mb-2">

                        <strong>Total Poin:</strong>

                        <span class="badge bg-danger">
                            <?= $totalPoin ?> Poin
                        </span>

                    </div>

                    <div class="col-md-6 mb-2">

                        <strong>Tahap Pemanggilan:</strong>

                        <span class="badge bg-primary">
                            Tahap <?= $tahap ?>
                        </span>

                    </div>

                </div>

            </div>

        </div>

        <!-- ================================= -->
        <!-- FORM JADWAL PERTEMUAN -->
        <!-- ================================= -->

        <div class="card shadow-sm">

            <div class="card-header bg-white py-3">

                <h5 class="fw-bold mb-0">
                    Form Jadwal Pertemuan
                </h5>

            </div>

            <div class="card-body">

                <form method="POST"
                      action="buat_surat_pemanggilan.php?siswa_id=<?= $siswa_id ?>&kelas_id=<?= $kelas_id ?>">

                    <div class="row">

                        <!-- TANGGAL -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-semibold">
                                Tanggal Pertemuan
                            </label>

                            <input
                                type="date"
                                name="tanggal_pertemuan"
                                class="form-control"
                                min="<?= date('Y-m-d') ?>"
                                value="<?= e($_POST['tanggal_pertemuan'] ?? '') ?>"
                                required>

                        </div>

                        <!-- WAKTU -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-semibold">
                                Waktu Pertemuan
                            </label>

                            <input
                                type="time"
                                name="waktu_pertemuan"
                                class="form-control"
                                value="<?= e($_POST['waktu_pertemuan'] ?? '') ?>"
                                required>

                        </div>

                        <!-- TEMPAT -->
                        <div class="col-12 mb-3">

                            <label class="form-label fw-semibold">
                                Tempat Pertemuan
                            </label>

                            <input
                                type="text"
                                name="tempat_pertemuan"
                                class="form-control"
                                placeholder="Contoh: Ruang BK SMK Muhammadiyah Tasikmalaya"
                                value="<?= e($_POST['tempat_pertemuan'] ?? '') ?>"
                                required>

                        </div>

                        <!-- CATATAN -->
                        <div class="col-12 mb-4">

                            <label class="form-label fw-semibold">
                                Catatan / Keperluan Pertemuan
                            </label>

                            <textarea
                                name="catatan"
                                class="form-control"
                                rows="4"
                                placeholder="Contoh: Membahas pembinaan dan perkembangan perilaku siswa."><?= e($_POST['catatan'] ?? '') ?></textarea>

                        </div>

                    </div>

                    <!-- TOMBOL -->
                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            class="btn btn-primary">

                            Simpan Jadwal & Lihat Surat

                        </button>

                        <a
                            href="rekap_poin.php"
                            class="btn btn-secondary">

                            Kembali

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </main>

</div>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
