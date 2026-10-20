
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$query = mysqli_query($koneksi, "
    SELECT
        w.*,
        g.nama AS nama_guru,
        g.nip AS nip_guru
    FROM t_wali_kelas w
    LEFT JOIN t_guru g ON w.guru_id = g.id
    ORDER BY w.guru_id ASC
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
    <title>Data Wali Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>

<div class="d-flex">
    <?php include 'layout/sidebar.php'; ?>

    <main class="flex-grow-1 p-4">
        <h2>Wali Kelas</h2>
        <p class="text-muted">Kelola data wali kelas sekolah.</p>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Data Wali Kelas</strong>
                <a href="tambah_wali_kelas.php" class="btn btn-primary">
                    + Tambah Wali Kelas
                </a>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama Guru</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php while ($data = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($data['nip_guru'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($data['nama_guru'] ?? '-'); ?></td>
                            <td>
                                <a href="edit_wali_kelas.php?id=<?= (int)$data['guru_id']; ?>"
                                   class="btn btn-warning btn-sm">Edit</a>

                                <a href="hapus_wali_kelas.php?id=<?= (int)$data['guru_id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus data wali kelas ini?')">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>
