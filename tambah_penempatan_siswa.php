
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$siswa = mysqli_query($koneksi,
    "SELECT id, nis, nama FROM t_siswa ORDER BY nama ASC");

$kelas = mysqli_query($koneksi,
    "SELECT id, nama, tingkat, jurusan FROM t_kelas ORDER BY nama ASC");

$tahun = mysqli_query($koneksi,
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC");

$error = '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Penempatan Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <?php include 'layout/sidebar.php'; ?>

    <main class="flex-grow-1 p-4">
        <h2>Tambah Penempatan Siswa</h2>
        <p class="text-muted">Tambahkan siswa ke kelas dan tahun ajaran.</p>

        <div class="card">
            <div class="card-header">
                <strong>Form Penempatan Siswa</strong>
            </div>

            <div class="card-body">
                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!$siswa || !$kelas || !$tahun): ?>
                    <div class="alert alert-danger">
                        Gagal mengambil data dari database.
                        <?= htmlspecialchars(mysqli_error($koneksi)); ?>
                    </div>
                <?php else: ?>
                <form action="proses_tambah_penempatan_siswa.php"
                      method="POST">

                    <div class="mb-3">
                        <label class="form-label">Siswa</label>
                        <select name="siswa_id" class="form-select" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php while ($s = mysqli_fetch_assoc($siswa)): ?>
                                <option value="<?= $s['id']; ?>">
                                    <?= htmlspecialchars($s['nis'].' - '.$s['nama']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php while ($k = mysqli_fetch_assoc($kelas)): ?>
                                <option value="<?= $k['id']; ?>">
                                    <?= htmlspecialchars(
                                        $k['nama'].' - '.$k['tingkat'].' '.$k['jurusan']
                                    ); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id"
                                class="form-select" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            <?php while ($t = mysqli_fetch_assoc($tahun)): ?>
                                <option value="<?= $t['id']; ?>">
                                    <?= htmlspecialchars($t['nama']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai"
                               class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status_aktif" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>

                    <a href="penempatan_siswa.php"
                       class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">
                        Simpan Penempatan
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
