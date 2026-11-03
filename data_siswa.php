
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

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

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- DataTables CSS -->
    <link
        href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
        rel="stylesheet">

</head>

<body>

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php
if ($_SESSION['role'] == 'guru') {
    include 'layout/sidebar_guru.php';
} else {
    include 'layout/sidebar.php';
}
?>

    <!-- KONTEN UTAMA -->
    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <h2>Data Siswa</h2>
        <p class="text-muted">Daftar data siswa sekolah.</p>

        <div class="card shadow-sm">

            <!-- HEADER -->
            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <strong>Data Siswa</strong>

                <a href="tambah_siswa.php" class="btn btn-primary">
                    + Tambah Siswa
                </a>

            </div>

            <!-- BODY -->
            <div class="card-body">

                <div class="table-responsive">

                    <table id="tabelSiswa"
                           class="table table-bordered table-striped table-hover align-middle w-100">

                        <!-- HEADER TABEL -->
                        <thead class="table-dark">

                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Jenis Kelamin</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>

                        </thead>

                        <!-- ISI TABEL -->
                        <tbody>

                            <?php
                            $no = 1;

                            while ($data = mysqli_fetch_assoc($query)) {
                            ?>

                                <tr>

                                    <td><?= $no++; ?></td>

                                    <td>
                                        <?= e($data['nis']); ?>
                                    </td>

                                    <td>
                                        <?= e($data['nama']); ?>
                                    </td>

                                    <td>
                                        <?= e($data['jenis_kelamin']); ?>
                                    </td>

                                    <!-- STATUS SISWA -->
                                    <td>

                                        <?php if ((int)$data['status_aktif'] === 1) { ?>

                                            <span class="badge bg-success">
                                                Aktif
                                            </span>

                                        <?php } else { ?>

                                            <span class="badge bg-danger">
                                                Tidak Aktif
                                            </span>

                                        <?php } ?>

                                    </td>

                                    <!-- AKSI -->
                                    <td>

                                        <a
                                            href="edit_siswa.php?id=<?= (int)$data['id']; ?>"
                                            class="btn btn-warning btn-sm">

                                            Edit

                                        </a>

                                        <a
                                            href="hapus_siswa.php?id=<?= (int)$data['id']; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus data siswa ini?')">

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

<!-- JQUERY -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DATATABLES JS -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<!-- PENGATURAN DATATABLES -->
<script>

$(document).ready(function () {

    $('#tabelSiswa').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        order: [[1, 'asc']],

        columnDefs: [
            {
                orderable: false,
                targets: [0, 5]
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

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
