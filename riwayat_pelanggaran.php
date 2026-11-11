
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$role = strtolower(trim($_SESSION['role'] ?? ''));

if (!in_array($role, ['guru', 'wali_kelas'], true)) {
    header("Location: dashboard.php");
    exit;
}

$user_id = (int)($_SESSION['id'] ?? 0);

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Ambil data guru yang login
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

if (!$dataGuru) {
    die("Data guru tidak ditemukan.");
}

$guru_id = (int)$dataGuru['id'];

// Ambil hanya pelanggaran yang sudah selesai
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
      AND status = 'Selesai'
    ORDER BY tanggal DESC, id DESC
");

if (!$stmt) {
    die("Query riwayat gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt, "i", $guru_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="d-flex">

    <?php include 'layout/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">
        <div class="container-fluid">

            <h3 class="fw-bold mb-1">Riwayat Pelanggaran</h3>
            <p class="text-muted mb-4">
                Daftar pelanggaran siswa yang sudah selesai ditangani.
            </p>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Data Riwayat Pelanggaran</h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Poin</th>
                                    <th>Tindakan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php
                            $no = 1;

                            if (mysqli_num_rows($result) > 0):
                                while ($row = mysqli_fetch_assoc($result)):
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= e($row['nama_siswa']) ?></td>
                                    <td><?= e($row['nama_kelas']) ?></td>
                                    <td><?= e($row['nama_pelanggaran']) ?></td>
                                    <td><?= e($row['tanggal']) ?></td>
                                    <td><?= e($row['keterangan']) ?></td>
                                    <td>
                                        <span class="badge bg-danger">
                                            <?= (int)$row['poin'] ?> Poin
                                        </span>
                                    </td>
                                    <td><?= e($row['tindakan']) ?></td>
                                    <td>
                                        <span class="badge bg-success">
                                            <?= e($row['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        Belum ada riwayat pelanggaran yang selesai.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <a href="dashboard.php" class="btn btn-secondary">
                        Kembali ke Dashboard
                    </a>

                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>

<?php
mysqli_stmt_close($stmt);
?>
