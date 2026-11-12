
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Role pengguna
$role = strtolower(trim($_SESSION['role'] ?? ''));

// Ambil seluruh data siswa
$query = mysqli_query($koneksi, "
    SELECT *
    FROM t_siswa
    ORDER BY id DESC
");

if (!$query) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Mengamankan output
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Data Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php
    // Gunakan sidebar yang memang tersedia
    if ($role === 'guru') {
        include __DIR__ . '/layout/sidebar.php';
    } else {
        include __DIR__ . '/layout/sidebar.php';
    }
    ?>

    <!-- KONTEN UTAMA -->
    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <div class="container-fluid">

            <h2 class="fw-bold">Data Siswa</h2>

            <p class="text-muted">
                Kelola data siswa.
            </p>

            <div class="card shadow-sm">

                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">

                    <strong>Data Siswa</strong>

                    <?php if ($role === 'admin'): ?>
                        <a href="tambah_siswa.php" class="btn btn-primary">
                            + Tambah Siswa
                        </a>
                    <?php endif; ?>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelSiswa"
                               class="table table-bordered table-striped table-hover align-middle w-100">

                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Status</th>
                                    <?php if ($role === 'admin'): ?>
                                        <th>Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>

                            <tbody>

                            <?php
                            $no = 1;

                            while ($data = mysqli_fetch_assoc($query)):
                            ?>

                                <tr>
                                    <td><?= $no++; ?></td>

                                    <td><?= e($data['nis']); ?></td>

                                    <td><?= e($data['nama']); ?></td>

                                    <td><?= e($data['jenis_kelamin']); ?></td>

                                    <td>
                                        <?php if ((int)$data['status_aktif'] === 1): ?>
                                            <span class="badge bg-success">
                                                Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                Tidak Aktif
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <?php if ($role === 'admin'): ?>
                                    <td>
                                        <a href="edit_siswa.php?id=<?= (int)$data['id']; ?>"
                                           class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <a href="hapus_siswa.php?id=<?= (int)$data['id']; ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Yakin ingin menghapus data siswa ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                    <?php endif; ?>

                                </tr>

                            <?php endwhile; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    $('#tabelSiswa').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        order: [[1, 'asc']],

        columnDefs: [
            {
                orderable: false,
                targets: [0, <?= $role === 'admin' ? '5' : '0' ?>]
            }
        ],

        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data siswa tidak ditemukan",
            emptyTable: "Belum ada data siswa",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        }

    });

});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
