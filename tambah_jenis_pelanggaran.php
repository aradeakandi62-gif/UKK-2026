```php
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori_id = (int) ($_POST['pelanggaran_kategori_id'] ?? 0);
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $poin = (int) ($_POST['poin'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if ($kategori_id <= 0 || $kode === '' || $nama === '' || $poin < 0) {
        $error = 'Lengkapi data dengan benar!';
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO t_pelanggaran
            (pelanggaran_kategori_id, kode, nama, poin, deskripsi, status_aktif)
            VALUES (?, ?, ?, ?, ?, 1)"
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "issis",
                $kategori_id,
                $kode,
                $nama,
                $poin,
                $deskripsi
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header('Location: jenis_pelanggaran.php');
                exit;
            }

            $error = 'Gagal menyimpan: ' . mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
        } else {
            $error = 'Query gagal: ' . mysqli_error($koneksi);
        }
    }
}

$kategori = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_pelanggaran_kategori
     WHERE status_aktif = 1 ORDER BY nama ASC"
);
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
    <p>Tambahkan data jenis pelanggaran siswa.</p>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">Form Tambah Jenis Pelanggaran</div>
        <div class="card-body">
            <form method="POST" action="tambah_jenis_pelanggaran.php">
                <div class="mb-3">
                    <label class="form-label">Kategori Pelanggaran</label>
                    <select name="pelanggaran_kategori_id"
                            class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($row = mysqli_fetch_assoc($kategori)): ?>
                            <option value="<?= (int) $row['id'] ?>">
                                <?= htmlspecialchars($row['nama']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kode Pelanggaran</label>
                    <input type="text" name="kode"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Pelanggaran</label>
                    <input type="text" name="nama"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Poin</label>
                    <input type="number" name="poin" min="0"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi"
                              class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary" name="simpan">
                    Simpan
                </button>
                <a href="jenis_pelanggaran.php" class="btn btn-secondary">
                    Kembali
                </a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
```