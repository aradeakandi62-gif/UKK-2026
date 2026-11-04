
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$error = '';

$tahun = mysqli_query($koneksi, "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC");
$kelas = mysqli_query($koneksi, "SELECT id, nama FROM t_kelas ORDER BY nama ASC");
$guru  = mysqli_query($koneksi, "SELECT id, nama FROM t_guru ORDER BY nama ASC");

if (!$tahun || !$kelas || !$guru) {
    die("Gagal mengambil data: " . mysqli_error($koneksi));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tahun_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
    $kelas_id = (int) ($_POST['kelas_id'] ?? 0);
    $guru_id  = (int) ($_POST['guru_id'] ?? 0);

    $mulai   = $_POST['tanggal_mulai'] ?? '';
    $selesai = $_POST['tanggal_selesai'] ?? '';
    $status  = (int) ($_POST['status_aktif'] ?? 1);

    if (!$tahun_id || !$kelas_id || !$guru_id || !$mulai) {

        $error = 'Tahun ajaran, kelas, guru, dan tanggal mulai wajib diisi.';

    } elseif ($selesai !== '' && $selesai < $mulai) {

        $error = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';

    } elseif (!in_array($status, [0, 1], true)) {

        $error = 'Status tidak valid.';

    } else {

        // Cek wali kelas aktif pada kelas dan tahun ajaran yang sama
        $cek = mysqli_prepare($koneksi, "
            SELECT id
            FROM t_wali_kelas
            WHERE kelas_id = ?
            AND tahun_ajaran_id = ?
            AND status_aktif = 1
        ");

        if (!$cek) {
            die("Query pengecekan gagal: " . mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param($cek, "ii", $kelas_id, $tahun_id);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        $jumlah = mysqli_stmt_num_rows($cek);
        mysqli_stmt_close($cek);

        if ($jumlah > 0 && $status === 1) {

            $error = 'Kelas ini sudah memiliki wali kelas aktif pada tahun ajaran tersebut.';

        } else {

            $stmt = mysqli_prepare($koneksi, "
                INSERT INTO t_wali_kelas
                (
                    tahun_ajaran_id,
                    kelas_id,
                    guru_id,
                    tanggal_mulai,
                    tanggal_selesai,
                    status_aktif,
                    created_at,
                    updated_at
                )
                VALUES (?, ?, ?, ?, NULLIF(?, ''), ?, NOW(), NOW())
            ");

            if (!$stmt) {
                die("Query penyimpanan gagal: " . mysqli_error($koneksi));
            }

            mysqli_stmt_bind_param(
                $stmt,
                "iiissi",
                $tahun_id,
                $kelas_id,
                $guru_id,
                $mulai,
                $selesai,
                $status
            );

            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);
                header('Location: wali_kelas.php');
                exit;

            } else {

                $error = 'Gagal menyimpan data: ' . mysqli_stmt_error($stmt);
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
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tambah Wali Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <h2 class="mb-1">Tambah Wali Kelas</h2>

            <p class="text-muted mb-3">
                Tambahkan guru sebagai wali kelas pada tahun ajaran tertentu.
            </p>

            <!-- FORM TAMBAH WALI KELAS -->
            <div class="card shadow-sm">

                <div class="card-header bg-light fw-bold">
                    Form Wali Kelas
                </div>

                <div class="card-body">

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran</label>

                            <select name="tahun_ajaran_id" class="form-select" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>

                                <?php while ($r = mysqli_fetch_assoc($tahun)): ?>
                                    <option value="<?= (int)$r['id'] ?>"
                                        <?= (($_POST['tahun_ajaran_id'] ?? '') == $r['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['nama'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endwhile; ?>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>

                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>

                                <?php while ($r = mysqli_fetch_assoc($kelas)): ?>
                                    <option value="<?= (int)$r['id'] ?>"
                                        <?= (($_POST['kelas_id'] ?? '') == $r['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['nama'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endwhile; ?>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Guru</label>

                            <select name="guru_id" class="form-select" required>
                                <option value="">-- Pilih Guru --</option>

                                <?php while ($r = mysqli_fetch_assoc($guru)): ?>
                                    <option value="<?= (int)$r['id'] ?>"
                                        <?= (($_POST['guru_id'] ?? '') == $r['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['nama'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endwhile; ?>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>

                            <input
                                type="date"
                                name="tanggal_mulai"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST['tanggal_mulai'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>

                            <input
                                type="date"
                                name="tanggal_selesai"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST['tanggal_selesai'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>

                            <select name="status_aktif" class="form-select">
                                <option value="1"
                                    <?= (($_POST['status_aktif'] ?? '1') == '1') ? 'selected' : '' ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= (($_POST['status_aktif'] ?? '') == '0') ? 'selected' : '' ?>>
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>

                        <a href="wali_kelas.php" class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
