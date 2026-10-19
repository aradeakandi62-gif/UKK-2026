
<?php
// Koneksi database
include 'config/koneksi.php';

// Ambil data siswa
$query = mysqli_query($koneksi, "SELECT * FROM t_siswa");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>

<body>

<div class="d-flex">

    <!-- Sidebar -->
    <div class="bg-dark text-white p-3"
         style="width: 230px; min-height: 100vh; flex-shrink: 0;">

        <h4 class="mb-4">My Website</h4>

        <div class="nav flex-column">
            <a href="dashboard.php" class="nav-link text-white mb-2">Dashboard</a>
            <a href="data_siswa.php" class="nav-link text-white mb-2">Data Siswa</a>
            <a href="data_guru.php" class="nav-link text-white mb-2">Data Guru</a>
            <a href="kelas.php" class="nav-link text-white mb-2">Kelas</a>
            <a href="laporan.php" class="nav-link text-white mb-2">Laporan</a>
            <a href="about.php" class="nav-link text-white mb-4">About Me</a>

            <hr>

            <a href="logout.php" class="nav-link text-danger">Logout</a>
        </div>
    </div>

    <!-- Content -->
    <div class="flex-grow-1 p-4" style="min-width: 0;">

        <h2>Data Siswa</h2>
        <p class="text-muted">Daftar data siswa</p>

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Data Siswa</strong>

                <a href="tambah_siswa.php" class="btn btn-primary">
                    + Tambah Siswa
                </a>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table id="tabelSiswa" class="table table-bordered table-striped align-middle">

                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Jenis Kelamin</th>
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
                                    <td><?= $data['nis']; ?></td>
                                    <td><?= $data['nama']; ?></td>
                                    <td><?= $data['jenis_kelamin']; ?></td>

                                    <td>
                                        <a href="edit_siswa.php?id=<?= $data['id']; ?>"
                                           class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <a href="hapus_siswa.php?id=<?= $data['id']; ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Pengaturan DataTables -->
<script>
$(document).ready(function () {
    $('#tabelSiswa').DataTable({
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data siswa",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada data siswa",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        },
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        columnDefs: [
            {
                orderable: false,
                targets: [0, 4]
            }
        ]
    });
});
</script>

</body>
</html>
