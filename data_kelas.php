
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

// Ambil data kelas
$query = mysqli_query($koneksi, "SELECT * FROM t_kelas");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white mb-4">My Website</h4>

            <ul class="nav nav-pills flex-column gap-2">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="data_siswa.php" class="nav-link text-white">
                        Data Siswa
                    </a>
                </li>
                <li class="nav-item">
                    <a href="data_guru.php" class="nav-link text-white">
                        Data Guru
                    </a>
                </li>
                <li class="nav-item">
                    <a href="data_kelas.php" class="nav-link active">
                        Data Kelas
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                        Logout
                    </a>
                </li>
            </ul>
        </div>

        <!-- Konten -->
        <div class="col-md-10 p-4">
            <h2>Data Kelas</h2>
            <p class="text-secondary">Daftar data kelas</p>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Data Kelas</strong>
                    <a href="tambah_kelas.php" class="btn btn-primary">
                        + Tambah Kelas
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Data Kelas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $no = 1;

                            if ($query && mysqli_num_rows($query) > 0):
                                while ($data = mysqli_fetch_assoc($query)):
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            json_encode($data, JSON_UNESCAPED_UNICODE)
                                        );
                                        ?>
                                    </td>
                                    <td>
                                        <a href="edit_kelas.php?id=<?= (int)$data['id'] ?>"
                                           class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                        <a href="hapus_kelas.php?id=<?= (int)$data['id'] ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Yakin ingin menghapus kelas ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                                <tr>
                                    <td colspan="3" class="text-center">
                                        Belum ada data kelas.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>
