<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Ambil data wali kelas
$query = mysqli_query($koneksi, "
    SELECT
        w.id,
        w.guru_id,
        g.nip,
        g.nama AS nama_guru
    FROM t_wali_kelas w
    LEFT JOIN t_guru g ON w.guru_id = g.id
    ORDER BY w.id DESC
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="d-flex">

    <?php
if ($_SESSION['role'] == 'guru') {
    include 'layout/sidebar_guru.php';
} else {
    include 'layout/sidebar.php';
}
?>

    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <h2>Data Wali Kelas</h2>
        <p class="text-muted">Kelola data wali kelas sekolah.</p>

        <div class="card shadow-sm">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <strong>Daftar Wali Kelas</strong>

                <a href="tambah_wali_kelas.php" class="btn btn-primary">
                    + Tambah Wali Kelas
                </a>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table id="tabelWaliKelas"
                           class="table table-bordered table-striped table-hover w-100">

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

                                <td>
                                    <?= htmlspecialchars($data['nip'] ?? '-'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['nama_guru'] ?? '-'); ?>
                                </td>

                                <td>

                                    <a href="edit_wali_kelas.php?id=<?= (int)$data['id']; ?>"
                                       class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="hapus_wali_kelas.php"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus wali kelas ini?');">

                                        <input type="hidden"
                                               name="id"
                                               value="<?= (int)$data['id']; ?>">

                                        <button type="submit"
                                                class="btn btn-danger btn-sm">
                                            Hapus
                                        </button>

                                    </form>

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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>

$(document).ready(function () {

    $('#tabelWaliKelas').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        order: [[0, 'asc']],

        columnDefs: [
            {
                targets: 0,
                className: 'text-start',
                orderable: false,
                searchable: false
            }
        ],

        language: {

            search: "Cari:",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Data tidak ditemukan",

            emptyTable: "Belum ada data wali kelas",

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

</body>
</html>
