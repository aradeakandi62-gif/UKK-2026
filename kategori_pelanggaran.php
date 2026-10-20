
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$query = mysqli_query($koneksi, "
    SELECT * FROM t_pelanggaran_kategori
    ORDER BY id DESC
");

if (!$query) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kategori Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-10 p-4">
            <h2>Kategori Pelanggaran</h2>
            <p>Kelola kategori pelanggaran siswa.</p>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Data Kategori Pelanggaran</strong>
                    <a href="tambah_kategori_pelanggaran.php"
                       class="btn btn-primary">
                        + Tambah Kategori
                    </a>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $no = 1;

                        while ($data = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <?= htmlspecialchars($data['nama'] ?? '') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($data['deskripsi'] ?? '-') ?>
                                </td>
                                <td>
                                    <?php if (($data['status_aktif'] ?? 0) == 1) { ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a href="edit_kategori_pelanggaran.php?id=<?= (int)$data['id'] ?>"
                                       class="btn btn-warning btn-sm">Edit</a>

                                    <a href="hapus_kategori_pelanggaran.php?id=<?= (int)$data['id'] ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>

                        <?php if (mysqli_num_rows($query) == 0) { ?>
                            <tr>
                                <td colspan="5" class="text-center">
                                    Belum ada data kategori.
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</div>

</body>
</html>
