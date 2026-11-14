
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Mengambil data tahun ajaran
$query = mysqli_query($koneksi, "
    SELECT * FROM t_tahun_ajaran
    ORDER BY id DESC
");

if (!$query) {
    die('Gagal mengambil data: ' . htmlspecialchars(mysqli_error($koneksi)));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Data Tahun Ajaran</title>

    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DATATABLES BOOTSTRAP 5 -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>


        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <h2 class="mb-1">Data Tahun Ajaran</h2>

            <p class="text-muted mb-4">
                Kelola data tahun ajaran sekolah.
            </p>


            <!-- NOTIFIKASI -->
            <?php

            if (isset($_SESSION['pesan_sukses'])) {

                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';

                echo htmlspecialchars($_SESSION['pesan_sukses']);

                echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';

                echo '</div>';

                unset($_SESSION['pesan_sukses']);
            }


            if (isset($_SESSION['pesan_error'])) {

                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';

                echo htmlspecialchars($_SESSION['pesan_error']);

                echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';

                echo '</div>';

                unset($_SESSION['pesan_error']);
            }

            ?>


            <!-- CARD TABEL -->
            <div class="card">

                <!-- HEADER -->
                <div class="card-header d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">
                        Data Tahun Ajaran
                    </h5>

                    <a href="tambah_tahun_ajaran.php"
                       class="btn btn-primary btn-sm">

                        + Tambah Tahun Ajaran

                    </a>

                </div>


                <!-- BODY -->
                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelTahunAjaran"
                               class="table table-bordered table-striped table-hover align-middle w-100">

                            <!-- HEADER TABEL -->
                            <thead class="table-dark">

                                <tr>

                                    <th>No</th>

                                    <th>Nama Tahun Ajaran</th>

                                    <th>Aksi</th>

                                </tr>

                            </thead>


                            <!-- ISI TABEL -->
                            <tbody>

                                <?php

                                $no = 1;

                                if (mysqli_num_rows($query) > 0) {

                                    while ($row = mysqli_fetch_assoc($query)) {

                                ?>

                                <tr>

                                    <td>
                                        <?= $no++; ?>
                                    </td>


                                    <td>
                                        <?= htmlspecialchars($row['nama'] ?? ''); ?>
                                    </td>


                                    <td>

                                        <div class="d-flex gap-2">

                                            <!-- EDIT -->
                                            <a href="edit_tahun_ajaran.php?id=<?= (int)$row['id']; ?>"
                                               class="btn btn-warning btn-sm">

                                                Edit

                                            </a>


                                            <!-- HAPUS -->
                                            <a href="hapus_tahun_ajaran.php?id=<?= (int)$row['id']; ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Yakin ingin menghapus tahun ajaran ini?')">

                                                Hapus

                                            </a>

                                        </div>

                                    </td>

                                </tr>

                                <?php

                                    }

                                }

                                ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </main>

    </div>

</div>


<!-- BOOTSTRAP JAVASCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>


<!-- JQUERY -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<!-- DATATABLES -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>


<!-- DATATABLES BOOTSTRAP 5 -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>


<!-- KONFIGURASI DATATABLES -->
<script>


$(document).ready(function () {

    $('#tabelSiswa').DataTable({

        pageLength: 10,

        lengthMenu: [10, 25, 50, 100],

        // Urutan data berdasarkan NIS
        order: [[1, 'asc']],

        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            },
            {
                targets: <?= $role === 'admin' ? '5' : '4' ?>,
                orderable: false
            }
        ],

        // Nomor selalu dimulai dari 1 pada halaman pertama
        drawCallback: function () {

            let api = this.api();

            let info = api.page.info();

            api.rows({
                page: 'current',
                order: 'applied',
                search: 'applied'
            }).nodes().each(function (row, index) {

                $(row).find('td:first').text(
                    info.start + index + 1
                );

            });

        },

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

</body>
</html>
