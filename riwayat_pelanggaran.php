
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Pastikan hanya guru yang bisa mengakses
if (strtolower(trim($_SESSION['role'] ?? '')) !== 'guru') {
    header("Location: dashboard.php");
    exit;
}

// ID akun yang sedang login (t_users.id)
$user_id = (int)($_SESSION['id'] ?? 0);

// Cari ID guru berdasarkan user_id
$stmtGuru = mysqli_prepare($koneksi, "
    SELECT id, nama
    FROM t_guru
    WHERE user_id = ?
    LIMIT 1
");

if (!$stmtGuru) {
    die("Query guru gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmtGuru, "i", $user_id);
mysqli_stmt_execute($stmtGuru);

$resultGuru = mysqli_stmt_get_result($stmtGuru);
$dataGuru = mysqli_fetch_assoc($resultGuru);

mysqli_stmt_close($stmtGuru);

// Pastikan akun terhubung dengan data guru
if (!$dataGuru) {
    die("Data guru tidak ditemukan. Pastikan t_guru.user_id sesuai dengan akun login.");
}

$guru_id = (int)$dataGuru['id'];

// Ambil riwayat berdasarkan guru_id
$stmt = mysqli_prepare($koneksi, "
    SELECT
        id,
        nama_siswa,
        nama_kelas,
        nama_pelanggaran,
        tanggal,
        keterangan,
        poin,
        tindakan,
        status
    FROM t_pelanggaran_siswa
    WHERE guru_id = ?
    ORDER BY tanggal DESC, id DESC
");

if (!$stmt) {
    die("Query riwayat gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt, "i", $guru_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$totalRiwayat = mysqli_num_rows($result);

function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
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

    <title>Riwayat Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="d-flex">

    <?php include 'layout/sidebar_guru.php'; ?>

    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <div class="container-fluid">

            <div class="mb-4">
                <h3 class="fw-bold">Riwayat Pelanggaran</h3>

                <p class="text-muted">
                    Daftar riwayat pelanggaran siswa yang telah dicatat oleh
                    <?= e($dataGuru['nama']) ?>.
                </p>
            </div>

            <!-- TOTAL RIWAYAT -->
            <div class="row mb-4">

                <div class="col-md-4">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <h6 class="text-muted">
                                Total Riwayat Pelanggaran
                            </h6>

                            <h2 class="fw-bold text-primary mb-0">
                                <?= $totalRiwayat ?>
                            </h2>

                        </div>

                    </div>

                </div>

            </div>

            <!-- TABEL RIWAYAT -->
            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">

                    <h5 class="fw-bold mb-0">
                        Data Riwayat Pelanggaran Siswa
                    </h5>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelRiwayat"
                               class="table table-bordered table-striped table-hover align-middle">

                            <thead class="table-dark">

                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Tanggal</th>
                                    <th>Poin</th>
                                    <th>Keterangan</th>
                                    <th>Tindakan</th>
                                    <th>Status</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php
                                $no = 1;

                                while ($row = mysqli_fetch_assoc($result)):
                                ?>

                                <tr>

                                    <td><?= $no++ ?></td>

                                    <td><?= e($row['nama_siswa']) ?></td>

                                    <td><?= e($row['nama_kelas']) ?></td>

                                    <td><?= e($row['nama_pelanggaran']) ?></td>

                                    <td>
                                        <?= !empty($row['tanggal'])
                                            ? date('d-m-Y', strtotime($row['tanggal']))
                                            : '-' ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-danger">
                                            <?= (int)$row['poin'] ?> Poin
                                        </span>
                                    </td>

                                    <td><?= e($row['keterangan']) ?></td>

                                    <td><?= e($row['tindakan']) ?></td>

                                    <td>
                                        <?php
                                        $status = strtolower(trim($row['status']));

                                        if ($status === 'selesai') {
                                            $warna = 'success';
                                        } elseif (
                                            $status === 'diproses' ||
                                            $status === 'ditindaklanjuti'
                                        ) {
                                            $warna = 'warning';
                                        } else {
                                            $warna = 'secondary';
                                        }
                                        ?>

                                        <span class="badge bg-<?= $warna ?>">
                                            <?= e($row['status']) ?>
                                        </span>
                                    </td>

                                </tr>

                                <?php endwhile; ?>

                            </tbody>

                        </table>

                    </div>

                    <a href="dashboard_guru.php"
                       class="btn btn-secondary mt-3">
                        Kembali ke Dashboard
                    </a>

                </div>

            </div>

        </div>

    </main>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    $('#tabelRiwayat').DataTable({

        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            zeroRecords: "Riwayat pelanggaran tidak ditemukan",
            emptyTable: "Belum ada riwayat pelanggaran",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },

        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true

    });

});
</script>

</body>
</html>

<?php
mysqli_stmt_close($stmt);
?>
