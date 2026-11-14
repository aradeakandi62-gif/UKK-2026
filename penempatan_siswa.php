
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$role = strtolower(trim($_SESSION['role'] ?? ''));

// Ambil data penempatan siswa
$sql = "
    SELECT
        ks.id,
        s.status_aktif AS status_aktif,
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

function e($value) {
    return htmlspecialchars(
        (string)($value ?? '-'),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Data Penempatan Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <!-- KONTEN UTAMA -->
    <main class="flex-grow-1 p-4" style="margin-left: 275px; min-width: 0;">

        <h2 class="fw-bold mb-1">Data Penempatan Siswa</h2>

        <p class="text-muted mb-4">
            Kelola penempatan siswa berdasarkan kelas dan tahun ajaran.
        </p>

        <div class="card shadow-sm">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <h5 class="mb-0">Data Penempatan Siswa</h5>

                <?php if ($role === 'admin'): ?>
                    <a href="tambah_penempatan_siswa.php"
                       class="btn btn-primary btn-sm">
                        + Tambah Penempatan
                    </a>
                <?php endif; ?>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table id="tabelPenempatan"
                           class="table table-bordered table-striped table-hover align-middle w-100">

                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Tahun Ajaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td></td>

                                    <td><?= e($row['nis']); ?></td>

                                    <td><?= e($row['nama_siswa']); ?></td>

                                    <td><?= e($row['nama_kelas']); ?></td>

                                    <td><?= e($row['nama_tahun_ajaran']); ?></td>

                                    <!-- STATUS AKTIF -->
                                    <td>
                                     <?php if ((int)$row['status_aktif'] === 1): ?>
                                        <span class="badge bg-success">
                                    Aktif
                                </span>

                            <?php else: ?>

                                <span class="badge bg-danger">
                                    Tidak Aktif
                                </span>

                            <?php endif; ?>
                        </td>

                                    <!-- AKSI -->
                                    <td>
                                        <div class="d-flex gap-2">

                                            <?php if ($role === 'admin'): ?>

                                                <a href="edit_penempatan.php?id=<?= (int)$row['id']; ?>"
                                                   class="btn btn-warning btn-sm">
                                                    Edit
                                                </a>

                                                <form action="hapus_penempatan.php"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Yakin ingin menghapus data ini?')">

                                                    <input type="hidden"
                                                           name="id"
                                                           value="<?= (int)$row['id']; ?>">

                                                    <button type="submit"
                                                            class="btn btn-danger btn-sm">
                                                        Hapus
                                                    </button>
                                                </form>

                                            <?php else: ?>

                                                <span class="text-muted">-</span>

                                            <?php endif; ?>

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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    $('#tabelPenempatan').DataTable({

        pageLength: 10,

        lengthMenu: [10, 25, 50, 100],

        order: [[1, 'asc']],

        columnDefs: [
            {
                targets: [0, 6],
                orderable: false,
                searchable: false
            }
        ],

        drawCallback: function () {

            let api = this.api();
            let info = api.page.info();

            api.column(0, {
                page: 'current'
            }).nodes().each(function (cell, index) {

                cell.innerHTML = info.start + index + 1;

            });

        },

        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada data penempatan siswa",

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
