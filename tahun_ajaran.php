
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$current_page = basename($_SERVER['PHP_SELF']);

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM t_tahun_ajaran ORDER BY id DESC"
);

if (!$query) {
    die("Gagal mengambil data: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tahun Ajaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>

<div class="d-flex">
    <?php include 'layout/sidebar.php'; ?>

    <main class="flex-grow-1 p-4">
        <h2>Tahun Ajaran</h2>
        <p class="text-muted">Kelola data tahun ajaran sekolah</p>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Data Tahun Ajaran</strong>
                <a href="tambah_tahun_ajaran.php" class="btn btn-primary">
                    + Tambah Tahun Ajaran
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Tahun Ajaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    <tbody>
    <?php if (mysqli_num_rows($query) > 0): ?>
        <?php $no = 1; ?>

        <?php while ($data = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><?= $no++; ?></td>

                <td>
                    <?= htmlspecialchars($data['nama']); ?>
                </td>

                <td>
                    <a href="edit_tahun_ajaran.php?id=<?= (int)$data['id']; ?>"
                       class="btn btn-warning btn-sm">
                        Edit
                    </a>

                    <a href="hapus_tahun_ajaran.php?id=<?= (int)$data['id']; ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Yakin ingin menghapus tahun ajaran ini?')">
                        Hapus
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>

    <?php else: ?>
        <tr>
            <td colspan="3" class="text-center">
                Belum ada data tahun ajaran.
            </td>
        </tr>
    <?php endif; ?>
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>
c