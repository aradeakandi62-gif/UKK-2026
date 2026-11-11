
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$current_page = basename($_SERVER['PHP_SELF']);

// Ambil data tindakan pelanggaran
$query = mysqli_query($koneksi, "
    SELECT
        id,
        tanggal,
        nama_siswa,
        nama_kelas,
        nama_pelanggaran,
        poin,
        tindakan,
        status,
        nama_guru,
        keterangan
    FROM t_pelanggaran_siswa
    ORDER BY tanggal DESC, id DESC
");

if (!$query) {
    die("Query gagal: " . mysqli_error($koneksi));
}

function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tindakan Pelanggaran</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">

</head>

<body class="bg-light">

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php
    if (($_SESSION['role'] ?? '') === 'guru') {
        include 'layout/sidebar_guru.php';
    } else {
        include 'layout/sidebar.php';
    }
    ?>

    <!-- KONTEN UTAMA -->
    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <div class="container-fluid">

            <h3 class="fw-bold mb-1">
                Tindakan Pelanggaran
            </h3>

            <p class="text-muted mb-4">
                Daftar tindakan yang diberikan kepada siswa berdasarkan catatan pelanggaran.
            </p>

            <!-- TABEL TINDAKAN -->
            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">
                        Data Tindakan Pelanggaran Siswa
                    </h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelTindakan"
                               class="table table-bordered table-striped table-hover align-middle w-100">

                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Pelanggaran</th>
                                    <th>Poin</th>
                                    <th>Tindakan</th>
                                    <th>Guru</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php
                            $no = 1;

                            while ($data = mysqli_fetch_assoc($query)):
                            ?>

                                <tr>
                                    <td><?= $no++; ?></td>

                                    <td>
                                        <?= e($data['tanggal']); ?>
                                    </td>

                                    <td>
                                        <?= e($data['nama_siswa']); ?>
                                    </td>

                                    <td>
                                        <?= e($data['nama_kelas']); ?>
                                    </td>

                                    <td>
                                        <?= e($data['nama_pelanggaran']); ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-danger">
                                            <?= e($data['poin']); ?> poin
                                        </span>
                                    </td>

                                    <td>
                                        <?= e($data['tindakan']) ?: '-'; ?>
                                    </td>

                                    <td>
                                        <?= e($data['nama_guru']); ?>
                                    </td>

                                    <td>
                                        <?php if (strtolower($data['status'] ?? '') === 'selesai'): ?>

                                            <span class="badge bg-success">
                                                Selesai
                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-warning text-dark">
                                                <?= e($data['status']) ?: 'Belum diproses'; ?>
                                            </span>

                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= e($data['keterangan']) ?: '-'; ?>
                                    </td>

                                </tr>

                            <?php endwhile; ?>

                            </tbody>

                        </table>

                    </div>

                    <a href="dashboard_guru.php" class="btn btn-secondary mt-3">
                        Kembali
                    </a>

                </div>
            </div>

        </div>

    </main>

</div>

<!-- JAVASCRIPT -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- Pengaturan DataTables -->
<script>
$(document).ready(function () {

    $('#tabelTindakan').DataTable({

        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/id.json'
        },

        pageLength: 10,

        lengthMenu: [
            [5, 10, 25, 50, 100],
            [5, 10, 25, 50, 100]
        ],

        ordering: true,

        searching: true,

        paging: true,

        info: true,

        order: [[1, 'desc']],

        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ]

    });

});
</script>

</body>
</html>
