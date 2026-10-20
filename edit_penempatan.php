<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$error = '';
$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: penempatan_siswa.php');
    exit;
}

// Ambil data penempatan
$stmt = mysqli_prepare($koneksi, "
    SELECT * FROM t_kelas_siswa WHERE id = ?
");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    exit('Data penempatan siswa tidak ditemukan.');
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siswa_id = (int) ($_POST['siswa_id'] ?? 0);
    $kelas_id = (int) ($_POST['kelas_id'] ?? 0);
    $tahun_ajaran_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $status_aktif = (int) ($_POST['status_aktif'] ?? 1);

    if (
        $siswa_id <= 0 || $kelas_id <= 0 ||
        $tahun_ajaran_id <= 0 || $tanggal_mulai === ''
    ) {
        $error = 'Siswa, kelas, tahun ajaran, dan tanggal mulai wajib diisi.';
    } elseif (
        $tanggal_selesai !== '' &&
        $tanggal_selesai < $tanggal_mulai
    ) {
        $error = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
    } else {
        $sql = "UPDATE t_kelas_siswa
                SET siswa_id = ?,
                    kelas_id = ?,
                    tahun_ajaran_id = ?,
                    tanggal_mulai = ?,
                    tanggal_selesai = NULLIF(?, ''),
                    status_aktif = ?,
                    updated_at = NOW()
                WHERE id = ?";

        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "iiissii",
            $siswa_id,
            $kelas_id,
            $tahun_ajaran_id,
            $tanggal_mulai,
            $tanggal_selesai,
            $status_aktif,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            header('Location: penempatan_siswa.php');
            exit;
        }

        $error = 'Gagal mengubah data: ' . mysqli_stmt_error($stmt);
    }
}

// Ambil pilihan dropdown
$siswa = mysqli_query($koneksi, "SELECT id, nis, nama FROM t_siswa ORDER BY nama");
$kelas = mysqli_query($koneksi, "SELECT id, nama FROM t_kelas ORDER BY nama");
$tahun = mysqli_query($koneksi, "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Penempatan Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <?php include 'layout/sidebar.php'; ?>

    <main class="flex-grow-1 p-4">
        <h2>Edit Penempatan Siswa</h2>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Siswa</label>
                        <select name="siswa_id" class="form-select" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php while ($s = mysqli_fetch_assoc($siswa)): ?>
                                <option value="<?= $s['id'] ?>"
                                    <?= (int)$s['id'] === (int)$data['siswa_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php while ($k = mysqli_fetch_assoc($kelas)): ?>
                                <option value="<?= $k['id'] ?>"
                                    <?= (int)$k['id'] === (int)$data['kelas_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="form-select" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            <?php while ($t = mysqli_fetch_assoc($tahun)): ?>
                                <option value="<?= $t['id'] ?>"
                                    <?= (int)$t['id'] === (int)$data['tahun_ajaran_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai"
                               class="form-control" required
                               value="<?= htmlspecialchars($data['tanggal_mulai']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai"
                               class="form-control"
                               value="<?= htmlspecialchars($data['tanggal_selesai'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status_aktif" class="form-select">
                            <option value="1" <?= (int)$data['status_aktif'] === 1 ? 'selected' : '' ?>>
                                Aktif
                            </option>
                            <option value="0" <?= (int)$data['status_aktif'] === 0 ? 'selected' : '' ?>>
                                Tidak Aktif
                            </option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="penempatan_siswa.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>