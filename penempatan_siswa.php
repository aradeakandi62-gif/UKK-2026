

<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Ambil data penempatan siswa
$query = mysqli_query($koneksi, "
    SELECT
        p.id,
        s.nis,
        s.nama AS nama_siswa,
        k.nama AS nama_kelas,
        ta.nama AS nama_tahun_ajaran
    FROM t_kelas_siswa AS p
    LEFT JOIN t_siswa AS s
        ON p.siswa_id = s.id
    LEFT JOIN t_kelas AS k
        ON p.kelas_id = k.id
    LEFT JOIN p.t_tahun_ajaran AS ta
        ON p.tahun_pelajaran_id = ta.id
    ORDER BY p.id DESC
");

if (!$query) {
    die('Query gagal: ' . mysqli_error($koneksi));
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Penempatan Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>
<div class="d-flex">
    <?php include 'layout/sidebar.php'; ?>

    <main class="flex-grow-1 p-4">
        <h2>Penempatan Siswa</h2>
        <p class="text-muted">Kelola penempatan siswa ke kelas.</p>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Data Penempatan Siswa</strong>
                <a href="tambah_penempatan_siswa.php"
                   class="btn btn-primary">
                    + Tambah Penempatan
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Tahun Ajaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        
<tbody>
<?php
$no = 1;

while ($data = mysqli_fetch_assoc($query)) {
?>
    <tr>
        <td><?= $no++; ?></td>
        <td><?= htmlspecialchars($data['nis'] ?? '-'); ?></td>
        <td><?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?></td>
        <td><?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?></td>
        <td><?= htmlspecialchars($data['nama_tahun_ajaran'] ?? '-'); ?></td>
        <td>
            <a href="edit_penempatan.php?id=<?= (int)$data['id']; ?>"
               class="btn btn-warning btn-sm">
                Edit
            </a>

            <a href="hapus_penempatan.php?id=<?= (int)$data['id']; ?>"
               class="btn btn-danger btn-sm"
               onclick="return confirm('Yakin ingin menghapus penempatan siswa ini?')">
                Hapus
            </a>
        </td>
    </tr>
<?php } ?>
</tbody>

                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
