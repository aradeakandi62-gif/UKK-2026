
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$current_page = basename($_SERVER['PHP_SELF']);

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM t_guru ORDER BY id DESC"
);

if (!$query) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Guru</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link
        href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.min.css"
        rel="stylesheet">
</head>

<body>

<div class="d-flex">

    <?php include 'layout/sidebar.php'; ?>

    <div class="flex-grow-1 p-4">

        <h2>Data Guru</h2>
        <p class="text-muted">Daftar data guru</p>

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Data Guru</strong>

                <a href="tambah_guru.php" class="btn btn-primary">
                    + Tambah Guru
                </a>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table id="tabelGuru"
                           class="table table-bordered table-striped table-hover align-middle w-100">

                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama Guru</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php
                        $no = 1;

                        while ($data = mysqli_fetch_assoc($query)):
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>

                                <td>
                                    <?= htmlspecialchars($data['nip'] ?? ''); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['nama'] ?? ''); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['email'] ?? ''); ?>
                                </td>

                                <td class="text-nowrap">

                                    <a href="edit_guru.php?id=<?= (int) $data['id']; ?>"
                                       class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <a href="hapus_guru.php?id=<?= (int) $data['id']; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus data guru ini?')">
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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.min.js"></script>

<!-- Pengaturan Tabel -->
<script>
$(document).ready(function () {

    $('#tabelGuru').DataTable({

        pageLength: 10,

        lengthMenu: [10, 25, 50, 100],

        ordering: true,

        searching: true,

        language: {
            emptyTable: "Belum ada data guru",
            zeroRecords: "Data guru tidak ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            lengthMenu: "Tampilkan _MENU_ data",
            search: "Cari:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        },

        columnDefs: [
            {
                targets: [0, 4],
                orderable: false,
                searchable: false
            }
        ]

    });

});
</script>

</body>
</html>
