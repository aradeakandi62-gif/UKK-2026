
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

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

    <!-- Sidebar -->
    <?php include 'layout/sidebar.php'; ?>

    <!-- Konten Utama -->
    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <h2>Data Guru</h2>
        <p class="text-muted">Daftar data guru sekolah.</p>

        <div class="card">

            <!-- Header -->
            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>Data Guru</strong>

                <a href="tambah_guru.php" class="btn btn-primary">
                    + Tambah Guru
                </a>

            </div>

            <!-- Isi Card -->
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
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php
                        $no = 1;

                        while ($data = mysqli_fetch_assoc($query)) {
                        ?>

                            <tr>

                                <!-- Nomor -->
                                <td><?= $no++; ?></td>

                                <!-- NIP -->
                                <td>
                                    <?= htmlspecialchars($data['nip'] ?? ''); ?>
                                </td>

                                <!-- Nama Guru -->
                                <td>
                                    <?= htmlspecialchars($data['nama'] ?? ''); ?>
                                </td>

                                <!-- Email -->
                                <td>
                                    <?= htmlspecialchars($data['email'] ?? '-'); ?>
                                </td>

                                <!-- Status -->
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

                                <!-- Aksi -->
                                <td class="text-nowrap">

                                    <a href="edit_guru.php?id=<?= (int)$data['id']; ?>"
                                       class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="hapus_guru.php" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus data guru ini?')">

                                        <input type="hidden" name="id"
                                                value="<?= (int)$data['id']; ?>">

                                        <button type="submit" class="btn btn-danger btn-sm">
                                             Hapus
                                        </button>
                                    </form>

                                </td>

                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>
        </div>

    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.min.js"></script>

<!-- Pengaturan DataTables -->
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
                targets: [0, 5],
                orderable: false,
                searchable: false
            }
        ]

    });

});
</script>

</body>
</html>
