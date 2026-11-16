
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$role = strtolower(trim($_SESSION['role'] ?? ''));

// Hanya Admin yang boleh memindahkan kelas
if ($role !== 'admin') {
    die("Anda tidak memiliki akses untuk memindahkan kelas.");
}

$error = '';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: penempatan_siswa.php");
    exit;
}

// ==========================================
// AMBIL DATA PENEMPATAN SISWA
// ==========================================

$stmt = mysqli_prepare($koneksi, "
    SELECT
        ks.id,
        ks.siswa_id,
        ks.kelas_id,
        ks.tahun_ajaran_id,
        s.nis,
        s.nama AS nama_siswa,
        k.nama AS nama_kelas,
        ta.nama AS nama_tahun_ajaran
    FROM t_kelas_siswa ks
    LEFT JOIN t_siswa s ON ks.siswa_id = s.id
    LEFT JOIN t_kelas k ON ks.kelas_id = k.id
    LEFT JOIN t_tahun_ajaran ta ON ks.tahun_ajaran_id = ta.id
    WHERE ks.id = ?
");

if (!$stmt) {
    die("Gagal mengambil data: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    die("Data penempatan siswa tidak ditemukan.");
}

// ==========================================
// PROSES PINDAH KELAS
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $kelas_baru = (int)($_POST['kelas_id'] ?? 0);

    if ($kelas_baru <= 0) {

        $error = "Silakan pilih kelas tujuan.";

    } elseif ($kelas_baru === (int)$data['kelas_id']) {

        $error = "Siswa sudah berada di kelas tersebut.";

    } else {

        // Pastikan kelas tujuan tersedia dan aktif
        $cekKelas = mysqli_prepare($koneksi, "
            SELECT id
            FROM t_kelas
            WHERE id = ?
              AND status_aktif = 1
            LIMIT 1
        ");

        mysqli_stmt_bind_param(
            $cekKelas,
            "i",
            $kelas_baru
        );

        mysqli_stmt_execute($cekKelas);

        $hasilKelas = mysqli_stmt_get_result($cekKelas);
        $kelasTersedia = mysqli_fetch_assoc($hasilKelas);

        mysqli_stmt_close($cekKelas);

        if (!$kelasTersedia) {

            $error = "Kelas tujuan tidak ditemukan atau tidak aktif.";

        } else {

            // Cek apakah siswa memiliki penempatan lain
            // pada tahun ajaran yang sama
            $cek = mysqli_prepare($koneksi, "
                SELECT id
                FROM t_kelas_siswa
                WHERE siswa_id = ?
                  AND tahun_ajaran_id = ?
                  AND id <> ?
                LIMIT 1
            ");

            mysqli_stmt_bind_param(
                $cek,
                "iii",
                $data['siswa_id'],
                $data['tahun_ajaran_id'],
                $id
            );

            mysqli_stmt_execute($cek);

            $hasilCek = mysqli_stmt_get_result($cek);
            $duplikat = mysqli_fetch_assoc($hasilCek);

            mysqli_stmt_close($cek);

            if ($duplikat) {

                $error = "Siswa sudah memiliki penempatan lain pada tahun ajaran ini.";

            } else {

                // Update kelas pada penempatan yang dipilih
                $update = mysqli_prepare($koneksi, "
                    UPDATE t_kelas_siswa
                    SET kelas_id = ?
                    WHERE id = ?
                      AND siswa_id = ?
                      AND tahun_ajaran_id = ?
                ");

                mysqli_stmt_bind_param(
                    $update,
                    "iiii",
                    $kelas_baru,
                    $id,
                    $data['siswa_id'],
                    $data['tahun_ajaran_id']
                );

                if (mysqli_stmt_execute($update)) {

                    mysqli_stmt_close($update);

                    $_SESSION['pesan_sukses'] =
                        "Siswa berhasil dipindahkan ke kelas baru.";

                    header("Location: penempatan_siswa.php");
                    exit;

                } else {

                    $error = "Gagal memindahkan kelas: " .
                             mysqli_stmt_error($update);

                    mysqli_stmt_close($update);
                }
            }
        }
    }
}

// ==========================================
// AMBIL KELAS TUJUAN
// KELAS SAAT INI DIKECUALIKAN
// ==========================================

$queryKelas = mysqli_prepare($koneksi, "
    SELECT id, nama, tingkat, jurusan
    FROM t_kelas
    WHERE status_aktif = 1
      AND id <> ?
    ORDER BY tingkat ASC, nama ASC
");

if (!$queryKelas) {
    die("Gagal mengambil data kelas: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param(
    $queryKelas,
    "i",
    $data['kelas_id']
);

mysqli_stmt_execute($queryKelas);

$hasilKelas = mysqli_stmt_get_result($queryKelas);

function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pindah Kelas Siswa</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <!-- KONTEN UTAMA -->
    <main class="flex-grow-1 p-4"
          style="margin-left: 275px; min-width: 0;">

        <h2 class="fw-bold mb-1">Pindah Kelas Siswa</h2>

        <p class="text-muted mb-4">
            Pindahkan siswa ke kelas lain pada tahun ajaran yang sama.
        </p>

        <div class="card shadow-sm">

            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    Form Pindah Kelas
                </h5>
            </div>

            <div class="card-body">

                <!-- PESAN ERROR -->
                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger">
                        <?= e($error); ?>
                    </div>
                <?php endif; ?>

                <!-- INFORMASI SISWA -->
                <div class="alert alert-info">

                    <strong>Informasi Siswa</strong>

                    <hr>

                    <div class="row">

                        <div class="col-md-6 mb-2">
                            <strong>NIS:</strong>
                            <?= e($data['nis']); ?>
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Nama Siswa:</strong>
                            <?= e($data['nama_siswa']); ?>
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Kelas Sekarang:</strong>
                            <?= e($data['nama_kelas']); ?>
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Tahun Ajaran:</strong>
                            <?= e($data['nama_tahun_ajaran']); ?>
                        </div>

                    </div>

                </div>

                <!-- FORM PINDAH KELAS -->
                <form method="POST"
                      action="pindah_kelas.php?id=<?= $id; ?>">

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Pilih Kelas Tujuan
                        </label>

                        <select name="kelas_id"
                                class="form-select"
                                required>

                            <option value="">
                                -- Pilih Kelas Baru --
                            </option>

                            <?php if (mysqli_num_rows($hasilKelas) > 0): ?>

                                <?php while ($kelas = mysqli_fetch_assoc($hasilKelas)): ?>

                                    <option
                                        value="<?= (int)$kelas['id']; ?>"
                                        <?= (
                                            isset($_POST['kelas_id']) &&
                                            (int)$_POST['kelas_id'] === (int)$kelas['id']
                                        ) ? 'selected' : ''; ?>>

                                        <?= e($kelas['nama']); ?>

                                        (<?= e($kelas['tingkat']); ?> - <?= e($kelas['jurusan']); ?>)

                                    </option>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <option value="" disabled>
                                    Tidak ada kelas tujuan lain yang tersedia.
                                </option>

                            <?php endif; ?>

                        </select>

                        <div class="form-text">
                            Kelas yang sedang ditempati tidak ditampilkan.
                        </div>

                    </div>

                    <!-- TOMBOL -->
                    <div class="d-flex gap-2">

                        <button type="submit"
                                class="btn btn-primary"
                                <?= mysqli_num_rows($hasilKelas) === 0 ? 'disabled' : ''; ?>
                                onclick="return confirm('Yakin ingin memindahkan siswa ke kelas yang dipilih?')">

                            Pindahkan Siswa

                        </button>

                        <a href="penempatan_siswa.php"
                           class="btn btn-secondary">

                            Kembali

                        </a>

                    </div>

                </form>

            </div>
        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
mysqli_stmt_close($queryKelas);
?>

