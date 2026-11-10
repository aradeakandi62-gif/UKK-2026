
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Fungsi menghitung jumlah data
function hitungData($koneksi, $tabel) {
    $daftarTabel = [
        't_siswa',
        't_kelas',
        't_pelanggaran',
        't_pelanggaran_siswa'
    ];

    if (!in_array($tabel, $daftarTabel, true)) {
        return 0;
    }

    $query = mysqli_query(
        $koneksi,
        "SELECT COUNT(*) AS total FROM `$tabel`"
    );

    if (!$query) {
        return 0;
    }

    $data = mysqli_fetch_assoc($query);
    return (int) $data['total'];
}

// Mengambil jumlah data
$totalSiswa = hitungData($koneksi, 't_siswa');
$totalKelas = hitungData($koneksi, 't_kelas');
$totalJenisPelanggaran = hitungData($koneksi, 't_pelanggaran');
$totalCatatan = hitungData($koneksi, 't_pelanggaran_siswa');

// Informasi akun
$nama = $_SESSION['name'] ?? 'Guru';
$email = $_SESSION['email'] ?? '-';
$role = $_SESSION['role'] ?? 'guru';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard Guru</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include 'layout/sidebar_guru.php'; ?>

    <!-- KONTEN UTAMA -->
    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <div class="container-fluid">

            <h3 class="fw-bold mb-1">Dashboard Guru</h3>

            <p class="text-muted mb-4">
                Selamat datang di Sistem Informasi Pelanggaran Siswa.
            </p>

            <!-- INFORMASI AKUN -->
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">Informasi Akun</h5>

                    <div class="row align-items-center">

                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <div class="rounded-circle bg-light border d-inline-flex
                                        align-items-center justify-content-center"
                                 style="width: 85px; height: 85px; font-size: 40px;">
                                👨‍🏫
                            </div>
                        </div>

                        <div class="col-md-10">

                            <h5 class="fw-bold mb-2">
                                <?= htmlspecialchars($nama) ?>
                            </h5>

                            <span class="badge bg-success mb-2">
                                <?= htmlspecialchars(ucfirst($role)) ?>
                            </span>

                            <p class="text-muted mb-1">
                                Email: <?= htmlspecialchars($email) ?>
                            </p>

                            <p class="text-muted mb-0">
                                Tanggal: <?= date('d-m-Y') ?>
                            </p>

                        </div>

                    </div>

                </div>

            </div>

            <!-- RINGKASAN DATA -->
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Ringkasan Data</h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-striped mb-0">

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
                                    <td><?= $totalSiswa ?> siswa</td>
                                </tr>

                                <tr>
                                    <td>2</td>
                                    <td>Data Kelas</td>
                                    <td><?= $totalKelas ?> kelas</td>
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td>Jenis Pelanggaran</td>
                                    <td><?= $totalJenisPelanggaran ?> jenis</td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td>Catatan Pelanggaran</td>
                                    <td><?= $totalCatatan ?> catatan</td>
                                </tr>
                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <!-- GRAFIK DI BAGIAN BAWAH -->
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white py-3">

                    <h5 class="fw-bold mb-1">
                        Grafik Statistik Data
                    </h5>

                    <small class="text-muted">
                        Grafik jumlah data pada sistem pelanggaran siswa
                    </small>

                </div>

                <div class="card-body">

                    <div style="height: 350px; position: relative;">
                        <canvas id="grafikStatistik"></canvas>
                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('grafikStatistik');

new Chart(ctx, {
    type: 'bar',

    data: {
        labels: [
            'Siswa',
            'Kelas',
            'Jenis Pelanggaran',
            'Catatan Pelanggaran'
        ],

        datasets: [{
            label: 'Jumlah Data',

            data: [
                <?= $totalSiswa ?>,
                <?= $totalKelas ?>,
                <?= $totalJenisPelanggaran ?>,
                <?= $totalCatatan ?>
            ],

            backgroundColor: [
                '#0d6efd',
                '#198754',
                '#ffc107',
                '#dc3545'
            ],

            borderWidth: 1,
            borderRadius: 5
        }]
    },

    options: {
        responsive: true,
        maintainAspectRatio: false,

        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },

        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});
</script>

</body>
</html>
