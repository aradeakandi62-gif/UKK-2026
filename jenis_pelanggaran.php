
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$sql = "SELECT p.*, k.nama AS nama_kategori
        FROM t_pelanggaran p
        LEFT JOIN t_pelanggaran_kategori k
        ON p.pelanggaran_kategori_id = k.id
        ORDER BY p.id DESC";

$query = mysqli_query($koneksi, $sql);

if (!$query) {
    die('Query gagal: ' . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jenis Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">

        <div class="col-md-2 bg-dark text-white min-vh-100 p-3">
            <h4>My Website</h4>
            <hr>

            <a href="dashboard.php" class="d-block text-white text-decoration-none mb-3">Dashboard</a>
            <a href="data_siswa.php" class="d-block text-white text-decoration-none mb-3">Data Siswa</a>
            <a href="data_guru.php" class="d-block text-white text-decoration-none mb-3">Data Guru</a>
            <a href="data_kelas.php" class="d-block text-white text-decoration-none mb-3">Kelas</a>
            <a href="tahun_ajaran.php" class="d-block text-white text-decoration-none mb-3">Tahun Ajaran</a>
            <a href="penempatan_siswa.php" class="d-block text-white text-decoration-none mb-3">Penempatan Siswa</a>
            <a href="wali_kelas.php" class="d-block text-white text-decoration-none mb-3">Wali Kelas</a>
            <a href="kategori_pelanggaran.php" class="d-block text-white text-decoration-none mb-3">Kategori Pelanggaran</a>
            <a href="jenis_pelanggaran.php" class="d-block bg-primary text-white text-decoration-none rounded p-2 mb-3">Jenis Pelanggaran</a>
            <a href="laporan.php" class="d-block text-white text-decoration-none mb-3">Laporan</a>
            <a href="logout.php" class="d-block text-danger text-decoration-none mt-4">Logout</a>
        </div>

        <div class="col-md-10 p-4">
            <h2>Jenis Pelanggaran</h2>
            <p>Kelola data jenis pelanggaran siswa.</p>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Data Jenis Pelanggaran</strong>
                    <a href="tambah_jenis_pelanggaran.php" class="btn btn-primary">
                        + Tambah Jenis Pelanggaran
                    </a>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Pelanggaran</th>
                                <th>Kategori</th>
                                <th>Poin</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($query)) : ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['kode'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['nama'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['nama_kategori'] ?? '-'); ?></td>
                                <td><?= (int)$row['poin']; ?></td>
                                <td><?= htmlspecialchars($row['deskripsi'] ?? '-'); ?></td>
                                <td>
                                    <?= $row['status_aktif'] ? 'Aktif' : 'Tidak Aktif'; ?>
                                </td>
                                <td>
                                    <a href="edit_jenis_pelanggaran.php?id=<?= (int)$row['id']; ?>"
                                       class="btn btn-warning btn-sm">Edit</a>

                                    <a href="hapus_jenis_pelanggaran.php?id=<?= (int)$row['id']; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>
