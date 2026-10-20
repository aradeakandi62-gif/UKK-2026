
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "SELECT * FROM t_wali_kelas WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$data) {
    exit('Data wali kelas tidak ditemukan.');
}

$error = '';

$tahun = mysqli_query($koneksi, "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC");
$kelas = mysqli_query($koneksi, "SELECT id, nama FROM t_kelas ORDER BY nama");
$guru = mysqli_query($koneksi, "SELECT id, nama FROM t_guru ORDER BY nama");

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
    } else {
        $cek = mysqli_prepare($koneksi,
            "SELECT id FROM t_wali_kelas
             WHERE kelas_id = ? AND tahun_ajaran_id = ?
             AND status_aktif = 1 AND id <> ?"
        );
        mysqli_stmt_bind_param($cek, "iii", $kelas_id, $tahun_id, $id);
        mysqli_stmt_execute($cek);

        if ($status === 1 && mysqli_num_rows(mysqli_stmt_get_result($cek)) > 0) {
            $error = 'Kelas ini sudah memiliki wali kelas aktif.';
        } else {
            $stmt = mysqli_prepare($koneksi,
                "UPDATE t_wali_kelas
                 SET tahun_ajaran_id = ?, kelas_id = ?, guru_id = ?,
                     tanggal_mulai = ?, tanggal_selesai = NULLIF(?, ''),
                     status_aktif = ?, updated_at = NOW()
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt, "iiissii",
                $tahun_id, $kelas_id, $guru_id,
                $mulai, $selesai, $status, $id
            );

            if (mysqli_stmt_execute($stmt)) {
                header('Location: wali_kelas.php');
                exit;
            } else {
                $error = 'Gagal mengubah data: ' . mysqli_stmt_error($stmt);
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
    <title>Edit Wali Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3>Edit Wali Kelas</h3>

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" class="form-select" required>
                                <?php while ($r = mysqli_fetch_assoc($tahun)): ?>
                                    <option value="<?= $r['id'] ?>"
                                        <?= ($r['id'] == ($_POST['tahun_ajaran_id'] ?? $data['tahun_ajaran_id'])) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['nama']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <?php while ($r = mysqli_fetch_assoc($kelas)): ?>
                                    <option value="<?= $r['id'] ?>"
                                        <?= ($r['id'] == ($_POST['kelas_id'] ?? $data['kelas_id'])) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['nama']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Guru</label>
                            <select name="guru_id" class="form-select" required>
                                <?php while ($r = mysqli_fetch_assoc($guru)): ?>
                                    <option value="<?= $r['id'] ?>"
                                        <?= ($r['id'] == ($_POST['guru_id'] ?? $data['guru_id'])) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['nama']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control"
                                value="<?= htmlspecialchars($_POST['tanggal_mulai'] ?? $data['tanggal_mulai']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control"
                                value="<?= htmlspecialchars($_POST['tanggal_selesai'] ?? $data['tanggal_selesai'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status_aktif" class="form-select">
                                <option value="1" <?= (($_POST['status_aktif'] ?? $data['status_aktif']) == 1) ? 'selected' : '' ?>>Aktif</option>
                                <option value="0" <?= (($_POST['status_aktif'] ?? $data['status_aktif']) == 0) ? 'selected' : '' ?>>Tidak Aktif</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="wali_kelas.php" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
