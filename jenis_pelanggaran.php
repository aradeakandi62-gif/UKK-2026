
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

        <!-- Sidebar -->
        <div class="col-md-2 bg-dark text-white min-vh-100 p-3">
            <h4 class="mb-4">My Website</h4>

            <nav class="nav nav-pills flex-column gap-2">
                <a href="dashboard.php" class="nav-link text-white">
                    Dashboard
                </a>
                <a href="data_siswa.php" class="nav-link text-white">
                    Data Siswa
                </a>
                <a href="data_guru.php" class="nav-link text-white">
                    Data Guru
                </a>
                <a href="data_kelas.php" class="nav-link text-white">
                    Kelas
                </a>
                <a href="tahun_ajaran.php" class="nav-link text-white">
                    Tahun Ajaran
                </a>
                <a href="penempatan_siswa.php" class="nav-link text-white">
                    Penempatan Siswa
                </a>
                <a href="wali_kelas.php" class="nav-link text-white">
                    Wali Kelas
                </a>
                <a href="kategori_pelanggaran.php"
                   class="nav-link text-white">
                    Kategori Pelanggaran
                </a>
                <a href="jenis_pelanggaran.php"
                   class="nav-link active"
                   aria-current="page">
                    Jenis Pelanggaran
                </a>
                <a href="laporan.php" class="nav-link text-white">
                    Laporan
                </a>
                <a href="about_me.php" class="nav-link text-white">
                    About Me
                </a>
                <hr>
                <a href="logout.php" class="nav-link text-danger">
                    Logout
                </a>
            </nav>
        </div>

        <!-- Konten -->
        <main class="col-md-10 p-4">

            <h2>Jenis Pelanggaran</h2>
            <p class="text-secondary">
                Kelola data jenis pelanggaran siswa.
            </p>

            <div class="card">
                <div class="card-header d-flex justify-content-between
                            align-items-center flex-wrap gap-2">
                    <strong>Data Jenis Pelanggaran</strong>

                    <a href="tambah_jenis_pelanggaran.php"
                       class="btn btn-primary">
                        + Tambah Jenis Pelanggaran
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped
                                      table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Pelanggaran</th>
                                    <th>Kategori</th>
                                    <th>Poin</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th style="min-width: 115px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                $no = 1;

                                // Gunakan hasil query yang sudah ada
                                // pada bagian PHP di atas HTML.
                                while ($row = mysqli_fetch_assoc($result)):
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['kode']) ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td>
                                        <?= htmlspecialchars(
                                            $row['nama_kategori'] ?? '-'
                                        ) ?>
                                    </td>
                                    <td><?= (int)$row['poin'] ?></td>
                                    <td>
                                        <?= htmlspecialchars(
                                            $row['deskripsi'] ?? '-'
                                        ) ?>
                                    </td>
                                    <td>
                                        <?php if ((int)$row['status_aktif'] === 1): ?>
                                            <span class="badge bg-success">
                                                Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                Tidak Aktif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="edit_jenis_pelanggaran.php?id=<?= (int)$row['id'] ?>"
                                               class="btn btn-warning btn-sm">
                                                Edit
                                            </a>

                                            <form action="hapus_jenis_pelanggaran.php"
                                                  method="POST"
                                                  onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                <input type="hidden"
                                                       name="id"
                                                       value="<?= (int)$row['id'] ?>">
                                                <button type="submit"
                                                        class="btn btn-danger btn-sm">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

</body>
</html>
```
