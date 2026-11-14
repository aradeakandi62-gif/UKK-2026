
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$role = strtolower(trim($_SESSION['role'] ?? ''));

// Ambil semua data guru
$query = mysqli_query($koneksi, "
    SELECT *
    FROM t_guru
    ORDER BY id DESC
");

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

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.min.css"
        rel="stylesheet">
</head>

<body>

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <!-- KONTEN UTAMA -->
    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <h2>Data Guru</h2>
        <p class="text-muted">Kelola data guru.</p>

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Data Guru</strong>

                <?php if ($role === 'admin'): ?>
                    <a href="tambah_guru.php" class="btn btn-primary">
                        + Tambah Guru
                    </a>
                <?php endif; ?>
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
                                <th>Status</th>

                                <?php if ($role === 'admin'): ?>
                                    <th>Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>

                        <tbody>

                        <?php while ($data = mysqli_fetch_assoc($query)): ?>

                            <tr>
                                <!-- Nomor akan diatur DataTables -->
                                <td></td>

                                <td>
                                    <?= htmlspecialchars($data['nip'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['nama'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['email'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td>
                                    <?php if ((int)$data['status_aktif'] === 1): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>

                                <?php if ($role === 'admin'): ?>
                                    <td class="text-nowrap">

                                        <a href="edit_guru.php?id=<?= (int)$data['id']; ?>"
                                           class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <form action="hapus_guru.php"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus data guru ini?')">

                                            <input type="hidden"
                                                   name="id"
                                                   value="<?= (int)$data['id']; ?>">

                                            <button type="submit"
                                                    class="btn btn-danger btn-sm">
                                                Hapus
                                            </button>
                                        </form>

                                    </td>
                                <?php endif; ?>

                            </tr>

                        <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

            </div>
        </div>

    </main>

</div>

<!-- JAVASCRIPT -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    $('#tabelGuru').DataTable({

        // 10 data setiap halaman
        pageLength: 10,

        lengthMenu: [10, 25, 50, 100],

        // Urutkan berdasarkan NIP
        order: [[1, 'asc']],

        // Kolom nomor dan aksi tidak bisa diurutkan
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            },
            {
                targets: <?= $role === 'admin' ? 5 : 4 ?>,
                orderable: false,
                searchable: false
            }
        ],

        // Nomor otomatis mengikuti halaman DataTables
        drawCallback: function () {

            let api = this.api();

            let info = api.page.info();

            api.rows({
                page: 'current',
                order: 'applied',
                search: 'applied'
            }).nodes().each(function (row, index) {

                row.cells[0].textContent = info.start + index + 1;

            });

        },

        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data guru tidak ditemukan",
            emptyTable: "Belum ada data guru",

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
