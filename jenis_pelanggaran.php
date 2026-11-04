
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Fungsi keamanan output HTML
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Query data jenis pelanggaran dan kategori
$sql = "SELECT
            p.id,
            p.kode,
            p.nama,
            p.pelanggaran_kategori_id,
            p.poin,
            p.deskripsi,
            p.status_aktif,
            k.nama AS nama_kategori
        FROM t_pelanggaran AS p
        LEFT JOIN t_pelanggaran_kategori AS k
            ON p.pelanggaran_kategori_id = k.id
        ORDER BY p.id DESC";

$query = mysqli_query($koneksi, $sql);

if (!$query) {
    die('Query gagal: ' . e(mysqli_error($koneksi)));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Data Jenis Pelanggaran</title>

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

            <h2 class="mb-1">Jenis Pelanggaran</h2>

            <p class="text-secondary mb-4">
                Kelola data jenis pelanggaran siswa.
            </p>


            <!-- CARD DATA -->
            <div class="card shadow-sm">

                <!-- HEADER -->
                <div class="card-header bg-white d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">
                        Data Jenis Pelanggaran
                    </h5>

                    <a href="tambah_jenis_pelanggaran.php"
                       class="btn btn-primary btn-sm">

                        + Tambah Jenis Pelanggaran

                    </a>

                </div>


                <!-- BODY CARD -->
                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelPelanggaran"
                               class="table table-bordered table-striped table-hover align-middle w-100">

                            <!-- HEADER TABEL -->
                            <thead class="table-dark">

                                <tr>

                                    <th>No</th>

                                    <th>Kode</th>

                                    <th>Nama Pelanggaran</th>

                                    <th>Kategori</th>

                                    <th>Poin</th>

                                    <th>Deskripsi</th>

                                    <th>Status</th>

                                    <th>Aksi</th>

                                </tr>

                            </thead>


                            <!-- ISI TABEL -->
                            <tbody>

                                <?php

                                $no = 1;

                                while ($row = mysqli_fetch_assoc($query)):

                                ?>

                                <tr>

                                    <!-- NOMOR -->
                                    <td>
                                        <?= $no++; ?>
                                    </td>


                                    <!-- KODE -->
                                    <td>
                                        <?= e($row['kode']); ?>
                                    </td>


                                    <!-- NAMA PELANGGARAN -->
                                    <td>
                                        <?= e($row['nama']); ?>
                                    </td>


                                    <!-- KATEGORI -->
                                    <td>
                                        <?= e($row['nama_kategori'] ?? '-'); ?>
                                    </td>


                                    <!-- POIN -->
                                    <td>

                                        <span class="badge bg-info text-dark">

                                            <?= (int)$row['poin']; ?> poin

                                        </span>

                                    </td>


                                    <!-- DESKRIPSI -->
                                    <td>
                                        <?= e($row['deskripsi'] ?: '-'); ?>
                                    </td>


                                    <!-- STATUS -->
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


                                    <!-- AKSI -->
                                    <td>

                                        <div class="d-flex gap-2">

                                            <!-- EDIT -->
                                            <a href="edit_jenis_pelanggaran.php?id=<?= (int)$row['id']; ?>"
                                               class="btn btn-warning btn-sm">

                                                Edit

                                            </a>


                                            <!-- HAPUS -->
                                            <form action="hapus_jenis_pelanggaran.php"
                                                  method="POST"
                                                  onsubmit="return confirm('Yakin ingin menghapus jenis pelanggaran ini?');">

                                                <input type="hidden"
                                                       name="id"
                                                       value="<?= (int)$row['id']; ?>">

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

    $('#tabelPelanggaran').DataTable({

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

            emptyTable: "Belum ada data jenis pelanggaran",

            zeroRecords: "Data jenis pelanggaran tidak ditemukan",

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
                targets: [0, 7],
                orderable: false,
                searchable: false
            }

        ],

        // Urutkan berdasarkan nama pelanggaran
        order: [[2, 'asc']]

    });

});

</script>

</body>
</html>
