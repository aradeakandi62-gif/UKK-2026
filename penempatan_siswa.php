
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$sql = "
    SELECT
        ks.id,
        s.nis,
        s.nama AS nama_siswa,
        k.nama AS nama_kelas,
        ta.nama AS nama_tahun_ajaran
    FROM t_kelas_siswa ks
    LEFT JOIN t_siswa s ON ks.siswa_id = s.id
    LEFT JOIN t_kelas k ON ks.kelas_id = k.id
    LEFT JOIN t_tahun_ajaran ta ON ks.tahun_ajaran_id = ta.id
    ORDER BY ks.id DESC
";

$query = mysqli_query($koneksi, $sql);

if (!$query) {
    die('Gagal mengambil data penempatan: ' .
        htmlspecialchars(mysqli_error($koneksi)));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Data Penempatan Siswa</title>

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

            <h2 class="mb-1">Data Penempatan Siswa</h2>

            <p class="text-muted mb-4">
                Kelola penempatan siswa berdasarkan kelas dan tahun ajaran.
            </p>


            <!-- CARD TABEL -->
            <div class="card">

                <!-- HEADER CARD -->
                <div class="card-header bg-white d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">
                        Data Penempatan Siswa
                    </h5>

                    <a href="tambah_penempatan_siswa.php"
                       class="btn btn-primary btn-sm">

                        + Tambah Penempatan

                    </a>

                </div>


                <!-- BODY CARD -->
                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelPenempatan"
                               class="table table-bordered table-striped table-hover align-middle w-100">

                            <!-- HEADER TABEL -->
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
                                        <?= htmlspecialchars($row['nis'] ?? '-'); ?>
                                    </td>


                                    <td>
                                        <?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
                                    </td>


                                    <td>
                                        <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                    </td>


                                    <td>
                                        <?= htmlspecialchars($row['nama_tahun_ajaran'] ?? '-'); ?>
                                    </td>


                                    <td>

                                        <div class="d-flex gap-2">

                                            <!-- EDIT -->
                                            <a href="edit_penempatan.php?id=<?= (int)$row['id']; ?>"
                                                class="btn btn-warning btn-sm">
                                                 Edit
                                            </a>


                                            <!-- HAPUS -->             
                                        <form action="hapus_penempatan.php"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?')">

                                            <input type="hidden" name="id"
                                                value="<?= (int)$row['id']; ?>">

                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Hapus
                                            </button>
                                        </form>



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

    $('#tabelPenempatan').DataTable({

        // Jumlah data awal
        pageLength: 10,

        // Pilihan jumlah data
        lengthMenu: [5, 10, 25, 50, 100],

        // Mengaktifkan sortir
        ordering: true,

        // Mengaktifkan pencarian
        searching: true,

        // Mengaktifkan pagination
        paging: true,

        // Informasi jumlah data
        info: true,

        // Bahasa Indonesia
        language: {

            emptyTable: "Belum ada data penempatan siswa",

            zeroRecords: "Data penempatan siswa tidak ditemukan",

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

        // Pengaturan kolom
        columnDefs: [

            {
                targets: [0, 5],
                orderable: false,
                searchable: false
            }

        ],

        // Urutkan berdasarkan NIS
        order: [[1, 'asc']]

    });

});

</script>

</body>
</html>
