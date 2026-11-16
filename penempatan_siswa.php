
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$current_page = basename($_SERVER['PHP_SELF']);

$role = strtolower($_SESSION['role'] ?? 'guru');

// Query data penempatan siswa
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
    die("Query gagal: " . mysqli_error($koneksi));
}

// Fungsi keamanan output
function e($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Penempatan Siswa</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

</head>

<body class="bg-light">

    <!-- Sidebar -->
    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <!-- Konten Utama -->
    <main class="p-4" style="margin-left: 275px; min-width: 0;">

        <div class="container-fluid">

            <!-- Judul Halaman -->
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h3 class="fw-bold mb-1">Data Penempatan Siswa</h3>
                    <p class="text-muted mb-0">
                        Kelola data penempatan siswa berdasarkan kelas dan tahun ajaran.
                    </p>
                </div>

                <?php if ($role === 'admin'): ?>
                    <a href="tambah_penempatan_siswa.php" class="btn btn-primary">
                        + Tambah Penempatan
                    </a>
                <?php endif; ?>

            </div>

            <!-- Card Tabel -->
            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">Daftar Penempatan Siswa</h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelPenempatan" class="table table-bordered table-striped table-hover align-middle w-100">

                            <thead class="table-dark">
                                <tr>
                                    <th width="50">No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Status</th>
                                    <th width="280">Tindakan</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php while ($row = mysqli_fetch_assoc($query)): ?>

                                    <tr>
                                        <td></td>

                                        <td>
                                            <?= e($row['nis']); ?>
                                        </td>

                                        <td>
                                            <?= e($row['nama_siswa']); ?>
                                        </td>

                                        <td>
                                            <?= e($row['nama_kelas']); ?>
                                        </td>

                                        <td>
                                            <?= e($row['nama_tahun_ajaran']); ?>
                                        </td>

                                        <td>
                                            <?php if ((int)$row['status_aktif'] === 1): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- TINDAKAN -->
                                        <td>

                                            <?php if ($role === 'admin'): ?>

                                                <div class="d-flex align-items-center gap-2 flex-nowrap">

                                                    <!-- Tombol Edit -->
                                                    <a href="edit_penempatan.php?id=<?= (int)$row['id']; ?>"
                                                       class="btn btn-warning btn-sm text-nowrap">
                                                        Edit
                                                    </a>

                                                    <!-- Tombol Hapus -->
                                                    <form action="hapus_penempatan.php"
                                                          method="POST"
                                                          class="m-0"
                                                          onsubmit="return confirm('Yakin ingin menghapus data penempatan ini?')">

                                                        <input type="hidden"
                                                               name="id"
                                                               value="<?= (int)$row['id']; ?>">

                                                        <button type="submit"
                                                                class="btn btn-danger btn-sm text-nowrap">
                                                            Hapus
                                                        </button>

                                                    </form>

                                                    <!-- Tombol Pindah Kelas -->
                                                    <a href="pindah_kelas.php?id=<?= (int)$row['id']; ?>"
                                                       class="btn btn-secondary btn-sm text-nowrap">
                                                        Pindah Kelas
                                                    </a>

                                                </div>

                                            <?php else: ?>

                                                <span class="text-muted">Tidak ada tindakan</span>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            </tbody>

                        </table>

                    </div>

                </div>
            </div>

        </div>

    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {

            $('#tabelPenempatan').DataTable({

                pageLength: 10,

                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],

                order: [
                    [1, 'asc']
                ],

                columnDefs: [
                    {
                        targets: [0, 6],
                        orderable: false,
                        searchable: false
                    }
                ],

                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    zeroRecords: "Data tidak ditemukan",
                    emptyTable: "Belum ada data penempatan siswa",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },

                drawCallback: function () {
                    let api = this.api();
                    let start = api.page.info().start;

                    api.column(0, {
                        page: 'current'
                    }).nodes().each(function (cell, i) {
                        cell.innerHTML = start + i + 1;
                    });
                }

            });

        });
    </script>

</body>
</html>
