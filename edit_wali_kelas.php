
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: wali_kelas.php');
    exit;
}

// Ambil data wali kelas
$stmt = mysqli_prepare($koneksi,
    "SELECT * FROM t_wali_kelas WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$data) {
    exit('Data wali kelas tidak ditemukan.');
}

$error = '';

// Ambil data dropdown
$tahun = mysqli_query($koneksi,
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC"
);

$kelas = mysqli_query($koneksi,
    "SELECT id, nama FROM t_kelas ORDER BY nama"
);

$guru = mysqli_query($koneksi,
    "SELECT id, nama FROM t_guru ORDER BY nama"
);

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tahun_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
    $kelas_id = (int) ($_POST['kelas_id'] ?? 0);
    $guru_id = (int) ($_POST['guru_id'] ?? 0);

    $mulai = $_POST['tanggal_mulai'] ?? '';
    $selesai = $_POST['tanggal_selesai'] ?? '';

    $status = (int) ($_POST['status_aktif'] ?? 1);

    if (!$tahun_id || !$kelas_id || !$guru_id || !$mulai) {

        $error = 'Lengkapi semua kolom wajib.';

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
            AND id <> ?
        ");

        mysqli_stmt_bind_param(
            $cek,
            "iii",
            $kelas_id,
            $tahun_id,
            $id
        );

        mysqli_stmt_execute($cek);

        $hasil_cek = mysqli_stmt_get_result($cek);

        if ($status === 1 && mysqli_num_rows($hasil_cek) > 0) {

            $error = 'Kelas ini sudah memiliki wali kelas aktif.';

        } else {

            $stmt = mysqli_prepare($koneksi, "
                UPDATE t_wali_kelas
                SET
                    tahun_ajaran_id = ?,
                    kelas_id = ?,
                    guru_id = ?,
                    tanggal_mulai = ?,
                    tanggal_selesai = NULLIF(?, ''),
                    status_aktif = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            mysqli_stmt_bind_param(
                $stmt,
                "iiissii",
                $tahun_id,
                $kelas_id,
                $guru_id,
                $mulai,
                $selesai,
                $status,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {

                header('Location: wali_kelas.php');
                exit;

            } else {

                $error = 'Gagal mengubah data: ' .
                    mysqli_stmt_error($stmt);
            }
        }
    }
}

// Nilai form, termasuk jika validasi gagal
$val_tahun = $_POST['tahun_ajaran_id'] ?? $data['tahun_ajaran_id'];
$val_kelas = $_POST['kelas_id'] ?? $data['kelas_id'];
$val_guru = $_POST['guru_id'] ?? $data['guru_id'];
$val_mulai = $_POST['tanggal_mulai'] ?? $data['tanggal_mulai'];
$val_selesai = $_POST['tanggal_selesai'] ?? ($data['tanggal_selesai'] ?? '');
$val_status = $_POST['status_aktif'] ?? $data['status_aktif'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Wali Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">

            <h2 class="mb-1">Edit Wali Kelas</h2>

            <p class="text-muted">
                Ubah data wali kelas berdasarkan tahun ajaran, kelas, dan guru.
            </p>

            <div class="card shadow-sm">

                <div class="card-header bg-light">
                    <strong>Form Edit Wali Kelas</strong>
                </div>

                <div class="card-body p-4">

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <!-- TAHUN AJARAN -->
                        <div class="mb-3">

                            <label class="form-label">Tahun Ajaran</label>

                            <select name="tahun_ajaran_id"
                                    class="form-select"
                                    required>

                                <option value="">-- Pilih Tahun Ajaran --</option>

                                <?php while ($r = mysqli_fetch_assoc($tahun)): ?>

                                    <option value="<?= $r['id'] ?>"
                                        <?= ($r['id'] == $val_tahun) ? 'selected' : '' ?>>

                                        <?= htmlspecialchars($r['nama']) ?>

                                    </option>

                                <?php endwhile; ?>

                            </select>

                        </div>

                        <!-- KELAS -->
                        <div class="mb-3">

                            <label class="form-label">Kelas</label>

                            <select name="kelas_id"
                                    class="form-select"
                                    required>

                                <option value="">-- Pilih Kelas --</option>

                                <?php while ($r = mysqli_fetch_assoc($kelas)): ?>

                                    <option value="<?= $r['id'] ?>"
                                        <?= ($r['id'] == $val_kelas) ? 'selected' : '' ?>>

                                        <?= htmlspecialchars($r['nama']) ?>

                                    </option>

                                <?php endwhile; ?>

                            </select>

                        </div>

                        <!-- GURU -->
                        <div class="mb-3">

                            <label class="form-label">Guru</label>

                            <select name="guru_id"
                                    class="form-select"
                                    required>

                                <option value="">-- Pilih Guru --</option>

                                <?php while ($r = mysqli_fetch_assoc($guru)): ?>

                                    <option value="<?= $r['id'] ?>"
                                        <?= ($r['id'] == $val_guru) ? 'selected' : '' ?>>

                                        <?= htmlspecialchars($r['nama']) ?>

                                    </option>

                                <?php endwhile; ?>

                            </select>

                        </div>

                        <!-- TANGGAL MULAI -->
                        <div class="mb-3">

                            <label class="form-label">Tanggal Mulai</label>

                            <input type="date"
                                   name="tanggal_mulai"
                                   class="form-control"
                                   value="<?= htmlspecialchars($val_mulai) ?>"
                                   required>

                        </div>

                        <!-- TANGGAL SELESAI -->
                        <div class="mb-3">

                            <label class="form-label">Tanggal Selesai</label>

                            <input type="date"
                                   name="tanggal_selesai"
                                   class="form-control"
                                   value="<?= htmlspecialchars($val_selesai) ?>">

                        </div>

                        <!-- STATUS -->
                        <div class="mb-3">

                            <label class="form-label">Status</label>

                            <select name="status_aktif" class="form-select">

                                <option value="1"
                                    <?= ($val_status == 1) ? 'selected' : '' ?>>

                                    Aktif

                                </option>

                                <option value="0"
                                    <?= ($val_status == 0) ? 'selected' : '' ?>>

                                    Tidak Aktif

                                </option>

                            </select>

                        </div>

                        <!-- TOMBOL -->
                        <div class="d-flex gap-2">

                            <button type="submit" class="btn btn-primary">
                                Simpan Perubahan
                            </button>

                            <a href="wali_kelas.php" class="btn btn-secondary">
                                Kembali
                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
