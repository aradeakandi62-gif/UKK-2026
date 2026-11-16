
<?php
session_start();

include 'includes/cek_session.php';
include 'config/koneksi.php';

$role = strtolower($_SESSION['role'] ?? '');

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
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

    <title>Data Kelas</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <h2 class="mb-1">Data Kelas</h2>
            <p class="text-secondary mb-4">
                Kelola data kelas siswa.
            </p>

            <!-- NOTIFIKASI -->
            <?php if (isset($_SESSION['pesan_sukses'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= e($_SESSION['pesan_sukses']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['pesan_sukses']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= e($_SESSION['pesan_error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['pesan_error']); ?>
            <?php endif; ?>

            <!-- TABEL KELAS -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">Daftar Kelas</h5>

                    <?php if ($role === 'admin'): ?>
                        <a href="tambah_kelas.php" class="btn btn-primary btn-sm">
                            + Tambah Kelas
                        </a>
                    <?php endif; ?>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelkelas"
                               class="table table-bordered table-striped table-hover align-middle w-100">

                            <thead class="table-dark">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Kelas</th>
                                    <th>Tingkat</th>
                                    <th>Jurusan</th>
                                    <th>Status</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php while ($row = mysqli_fetch_assoc($hasil)): ?>

                                    <tr>
                                        <td></td>

                                        <td><?= e($row['nama']); ?></td>

                                        <td><?= e($row['tingkat']); ?></td>

                                        <td><?= e($row['jurusan']); ?></td>

                                        <td>
                                            <?php if ((int)$row['status_aktif'] === 1): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if ($role === 'admin'): ?>
                                                <div class="d-flex align-items-center gap-2 flex-nowrap">

                                                    <a href="edit_kelas.php?id=<?= (int)$row['id']; ?>"
                                                       class="btn btn-warning btn-sm">
                                                        Edit
                                                    </a>

                                                    <form action="hapus_kelas.php"
                                                          method="POST"
                                                          class="m-0"
                                                          onsubmit="return confirm('Yakin ingin menghapus kelas ini?');">

                                                        <input type="hidden"
                                                               name="id"
                                                               value="<?= (int)$row['id']; ?>">

                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            Hapus
                                                        </button>
                                                    </form>

                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    $('#tabelkelas').DataTable({

        pageLength: 10,

        lengthMenu: [10, 25, 50, 100],

        order: [[1, 'asc']],

        columnDefs: [
            {
                targets: [0, 5],
                orderable: false,
                searchable: false
            }
        ],

        drawCallback: function () {
            let api = this.api();
            let info = api.page.info();

            api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = info.start + i + 1;
            });
        },

        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data kelas tidak ditemukan",
            emptyTable: "Belum ada data kelas",
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
