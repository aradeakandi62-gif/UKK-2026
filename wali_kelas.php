<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$query = mysqli_query($koneksi, "
    SELECT
        w.*,
        g.nama AS nama_guru,
        g.nip AS nip_guru
    FROM t_wali_kelas w
    LEFT JOIN t_guru g ON w.guru_id = g.id
    ORDER BY w.guru_id ASC
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

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- DataTables Bootstrap 5 -->
    <link
        href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="d-flex">

    <!-- Sidebar -->
    <?php include 'layout/sidebar.php'; ?>

    <!-- Konten -->
    <main class="flex-grow-1 p-4">

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

                <div class="card-body table-responsive">

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
                        <?= htmlspecialchars($data['nip_guru'] ?? '-'); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($data['nama_guru'] ?? '-'); ?>
                    </td>

                    <td>
                        <a
                            href="edit_wali_kelas.php?id=<?= (int)$data['guru_id']; ?>"
                            class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        <a
                            href="hapus_wali_kelas.php?id=<?= (int)$data['guru_id']; ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin ingin menghapus data wali kelas ini?')">
                            Hapus
                        </a>
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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelWaliKelas').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[1, 'asc']],

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
