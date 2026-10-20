
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$error = '';

$kategori = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_pelanggaran_kategori
     WHERE status_aktif = 1 ORDER BY nama ASC"
);

if (!$kategori) {
    die("Gagal mengambil kategori: " . mysqli_error($koneksi));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori_id = (int) ($_POST['pelanggaran_kategori_id'] ?? 0);
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $poin = (int) ($_POST['poin'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if ($kategori_id <= 0 || $kode === '' || $nama === '' || $poin < 0) {
        $error = 'Kategori, kode, nama, dan poin harus diisi dengan benar.';
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO t_pelanggaran
            (pelanggaran_kategori_id, kode, nama, poin, deskripsi, status_aktif)
            VALUES (?, ?, ?, ?, ?, 1)"
        );

        if (!$stmt) {
            $error = 'Gagal menyiapkan query: ' . mysqli_error($koneksi);
        } else {
            mysqli_stmt_bind_param(
                $stmt, "issis",
                $kategori_id, $kode, $nama, $poin, $deskripsi
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header('Location: jenis_pelanggaran.php');
                exit;
            } else {
                $error = 'Gagal menyimpan data: ' . mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Jenis Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <h2>Tambah Jenis Pelanggaran</h2>
    <p>Isi formulir jenis pelanggaran di bawah ini.</p>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">Form Tambah Jenis Pelanggaran</div>
        <div class="card-body">
            <form method="POST" action="tambah_jenis_pelanggaran.php">
                <div class="mb-3">
                    <label class="form-label">Kategori Pelanggaran</label>
                    <select name="pelanggaran_kategori_id" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($k = mysqli_fetch_assoc($kategori)): ?>
                            <option value="<?= (int)$k['id']; ?>"
                                <?= (int)($_POST['pelanggaran_kategori_id'] ?? 0) === (int)$k['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($k['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kode Pelanggaran</label>
                    <input type="text" name="kode" class="form-control"
                           value="<?= htmlspecialchars($_POST['kode'] ?? ''); ?>"
                           maxlength="30" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Pelanggaran</label>
                    <input type="text" name="nama" class="form-control"
                           value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>"
                           maxlength="150" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Poin</label>
                    <input type="number" name="poin" class="form-control"
                           min="0" max="2147483647"
                           value="<?= htmlspecialchars($_POST['poin'] ?? ''); ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="jenis_pelanggaran.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
