
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$current_page = basename($_SERVER['PHP_SELF']);

// Menghitung jumlah data
function hitungData($koneksi, $tabel) {
    $tabelDiizinkan = [
        't_siswa',
        't_guru',
        't_kelas',
        't_pelanggaran'
    ];

    if (!in_array($tabel, $tabelDiizinkan, true)) {
        return 0;
    }

    $query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM `$tabel`");

    if (!$query) {
        return 0;
    }

    $data = mysqli_fetch_assoc($query);
    return (int) $data['total'];
}

$totalSiswa = hitungData($koneksi, 't_siswa');
$totalGuru = hitungData($koneksi, 't_guru');
$totalKelas = hitungData($koneksi, 't_kelas');
$totalPelanggaran = hitungData($koneksi, 't_pelanggaran');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan - Aplikasi Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="d-flex">
    <?php include 'layout/sidebar.php'; ?>

    <main class="flex-grow-1 p-4">

        <h2>Laporan</h2>
        <p class="text-muted">
            Ringkasan data Aplikasi Pelanggaran Siswa
        </p>

        <div class="row g-3 mb-4">

            <div class="col-md-6 col-xl-3">
                <div class="card text-bg-primary h-100">
                    <div class="card-body">
                        <h5>Total Siswa</h5>
                        <h2><?= $totalSiswa; ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card text-bg-success h-100">
                    <div class="card-body">
                        <h5>Total Guru</h5>
                        <h2><?= $totalGuru; ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card text-bg-warning h-100">
                    <div class="card-body">
                        <h5>Total Kelas</h5>
                        <h2><?= $totalKelas; ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card text-bg-danger h-100">
                    <div class="card-body">
                        <h5>Total Pelanggaran</h5>
                        <h2><?= $totalPelanggaran; ?></h2>
                    </div>
                </div>
            </div>

        </div>

        <div class="card">
            <div class="card-header">
                <strong>Ringkasan Laporan</strong>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Jenis Data</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Data Siswa</td>
                            <td><?= $totalSiswa; ?> siswa</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Data Guru</td>
                            <td><?= $totalGuru; ?> guru</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Data Kelas</td>
                            <td><?= $totalKelas; ?> kelas</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Data Pelanggaran</td>
                            <td><?= $totalPelanggaran; ?> pelanggaran</td>
                        </tr>
                    </tbody>
                </table>

                <button type="button"
                        class="btn btn-primary"
                        onclick="window.print()">
                    Cetak Laporan
                </button>

                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali
                </a>
            </div>
        </div>

    </main>
</div>

</body>
</html>
