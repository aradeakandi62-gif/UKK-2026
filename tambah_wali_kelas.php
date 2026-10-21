
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$error = '';

$tahun = mysqli_query($koneksi, "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC");
$kelas = mysqli_query($koneksi, "SELECT id, nama FROM t_kelas ORDER BY nama ASC");
$guru  = mysqli_query($koneksi, "SELECT id, nama FROM t_guru ORDER BY nama ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tahun_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
    $kelas_id = (int) ($_POST['kelas_id'] ?? 0);
    $guru_id = (int) ($_POST['guru_id'] ?? 0);
    $mulai = $_POST['tanggal_mulai'] ?? '';
    $selesai = $_POST['tanggal_selesai'] ?? '';
    $status = (int) ($_POST['status_aktif'] ?? 1);

    if (!$tahun_id || !$kelas_id || !$guru_id || !$mulai) {
        $error = 'Tahun ajaran, kelas, guru, dan tanggal mulai wajib diisi.';
    } elseif ($selesai !== '' && $selesai < $mulai) {
        $error = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
    } else {
        // Satu kelas tidak boleh memiliki dua wali kelas aktif
        
        
        $cek = mysqli_prepare($koneksi,
            "SELECT id FROM t_wali_kelas
            WHERE kelas_id = ?
            AND tahun_pelajaran_id = ?
            AND status_aktif = 1"
        );

        if (!$cek) {
            die("Query pengecekan gagal: " . mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param($cek, "ii", $kelas_id, $tahun_id);
        mysqli_stmt_execute($cek);

        $hasil = mysqli_stmt_get_result($cek);


        if (mysqli_num_rows($hasil) > 0 && $status === 1) {
            $error = 'Kelas ini sudah memiliki wali kelas aktif pada tahun ajaran tersebut.';
        } else {
            $stmt = mysqli_prepare($koneksi,
                "INSERT INTO t_wali_kelas
                (tahun_ajaran_id, kelas_id, guru_id, tanggal_mulai,
                 tanggal_selesai, status_aktif, created_at, updated_at)
                VALUES (?, ?, ?, ?, NULLIF(?, ''), ?, NOW(), NOW())"
            );

            mysqli_stmt_bind_param(
                $stmt, "iiissi",
                $tahun_id, $kelas_id, $guru_id,
                $mulai, $selesai, $status
            );

            if (mysqli_stmt_execute($stmt)) {
                header('Location: wali_kelas.php');
                exit;
            } else {
                $error = 'Gagal menyimpan: ' . mysqli_stmt_error($stmt);
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
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3>Tambah Wali Kelas</h3>
                    <p class="text-muted">Isi data penugasan wali kelas.</p>

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" class="form-select" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php while ($r = mysqli_fetch_assoc($tahun)): ?>
                                    <option value="<?= $r['id'] ?>"
                                        <?= (($_POST['tahun_ajaran_id'] ?? '') == $r['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['nama']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php while ($r = mysqli_fetch_assoc($kelas)): ?>
                                    <option value="<?= $r['id'] ?>"
                                        <?= (($_POST['kelas_id'] ?? '') == $r['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['nama']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Guru</label>
                            <select name="guru_id" class="form-select" required>
                                <option value="">-- Pilih Guru --</option>
                                <?php while ($r = mysqli_fetch_assoc($guru)): ?>
                                    <option value="<?= $r['id'] ?>"
                                        <?= (($_POST['guru_id'] ?? '') == $r['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['nama']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST['tanggal_mulai'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST['tanggal_selesai'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status_aktif" class="form-select">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="wali_kelas.php" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
