
<?php
// kelola_kelas.php

session_start();

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Fungsi keamanan output
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Mengambil data kelas
$sql = "SELECT * FROM t_kelas ORDER BY id DESC";
$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die('Query gagal: ' . e(mysqli_error($koneksi)));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Kelas</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">

</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- Konten Utama -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">


            <h2 class="mb-1">Data Kelas</h2>

            <p class="text-secondary mb-4">
                Kelola data kelas siswa.
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

            <!-- TABEL KELAS -->
            <div class="card">

                <!-- HEADER -->
                <div class="card-header d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">
                        Data Kelas
                    </h5>

                    <a href="tambah_kelas.php" class="btn btn-primary btn-sm">
                        + Tambah Kelas
                    </a>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelkelas"
                               class="table table-bordered table-striped table-hover align-middle w-100">

                            <thead class="table-dark">

                                <tr>
                                    <th>No</th>
                                    <th>Nama Kelas</th>
                                    <th>Tingkat</th>
                                    <th>Jurusan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php
                                $no = 1;

                                if (mysqli_num_rows($hasil) > 0) {

                                    while ($row = mysqli_fetch_assoc($hasil)) {
                                ?>

                                <tr>

                                    <td>
                                        <?= $no++; ?>
                                    </td>

                                    <td>
                                        <?= e($row['nama'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= e($row['tingkat'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= e($row['jurusan'] ?? '-'); ?>
                                    </td>

                                    <td>

                                        <?php if ((int)($row['status_aktif'] ?? 0) === 1) { ?>

                                            <span class="badge bg-success">
                                                Aktif
                                            </span>

                                        <?php } else { ?>

                                            <span class="badge bg-secondary">
                                                Tidak Aktif
                                            </span>

                                        <?php } ?>

                                    </td>

                                    <td>

                                        <div class="d-flex gap-2">

                                            <!-- EDIT -->
                                            <a href="edit_kelas.php?id=<?= (int)$row['id']; ?>"
                                               class="btn btn-warning btn-sm">
                                                Edit
                                            </a>

                                            <!-- HAPUS -->
                                            <form action="hapus_kelas.php"
                                                  method="POST"
                                                  onsubmit="return confirm('Yakin ingin menghapus kelas ini?');"
                                                  class="d-inline">

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

                                <?php
                                    }

                                } else {
                                ?>

                                <tr>
                                    <td colspan="6" class="text-center">
                                        Belum ada data kelas.
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
