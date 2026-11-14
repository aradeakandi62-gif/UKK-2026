
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Fungsi keamanan output
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Mengambil data kategori
$query = mysqli_query($koneksi, "
    SELECT *
    FROM t_pelanggaran_kategori
    ORDER BY id DESC
");

if (!$query) {
    die("Query gagal: " . e(mysqli_error($koneksi)));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Kategori Pelanggaran</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- DataTables -->
    <link
        href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
        rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <h2 class="mb-1">Kategori Pelanggaran</h2>

            <p class="text-secondary mb-4">
                Kelola kategori pelanggaran siswa.
            </p>

            <div class="card shadow-sm">

                <div class="card-header bg-white d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">
                        Data Kategori Pelanggaran
                    </h5>

                    <a href="tambah_kategori_pelanggaran.php"
                       class="btn btn-primary btn-sm">
                        + Tambah Kategori
                    </a>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelKategori"
                               class="table table-bordered table-striped table-hover align-middle w-100">

                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php while ($data = mysqli_fetch_assoc($query)): ?>

                                    <tr>

                                        <!-- Nomor otomatis -->
                                        <td></td>

                                        <td>
                                            <?= e($data['nama'] ?? '') ?>
                                        </td>

                                        <td>
                                            <?= e($data['deskripsi'] ?? '-') ?>
                                        </td>

                                        <td>
                                            <?php if ((int)($data['status_aktif'] ?? 0) === 1): ?>

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
                                            <div class="d-flex gap-2">

                                                <a href="edit_kategori_pelanggaran.php?id=<?= (int)$data['id'] ?>"
                                                   class="btn btn-warning btn-sm">
                                                    Edit
                                                </a>

                                                <a href="hapus_kategori_pelanggaran.php?id=<?= (int)$data['id'] ?>"
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                                    Hapus
                                                </a>

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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    $('#tabelKategori').DataTable({

        // Hanya 6 data setiap halaman
        pageLength: 6,

        // Pilihan jumlah data
        lengthMenu: [6, 12, 25, 50],

        // Fitur tabel
        ordering: true,
        searching: true,
        paging: true,
        info: true,

        // Urutkan berdasarkan nama kategori
        order: [[1, 'asc']],

        // Kolom nomor dan aksi tidak bisa diurutkan
        columnDefs: [
            {
                targets: [0, 4],
                orderable: false,
                searchable: false
            }
        ],

        // Membuat nomor 1 sampai 6
        drawCallback: function () {

            var api = this.api();

            api.rows({
                page: 'current'
            }).every(function (rowIdx, tableLoop, rowLoop) {

                $(this.node()).find('td:first').text(rowLoop + 1);

            });

        },

        language: {

            emptyTable: "Belum ada data kategori pelanggaran",

            zeroRecords: "Data kategori tidak ditemukan",

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

        }

    });

});
</script>

</body>
</html>
