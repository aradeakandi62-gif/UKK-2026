<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Ambil data kelas
$sql = "SELECT *
        FROM t_kelas
        ORDER BY id DESC";

$query = mysqli_query($koneksi, $sql);

if (!$query) {
    die('Query gagal: ' . e(mysqli_error($koneksi)));
}

$data_kelas = [];

while ($row = mysqli_fetch_assoc($query)) {
    $data_kelas[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Kelas</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <aside class="col-md-2 bg-dark text-white min-vh-100 p-3">

            <h4 class="mb-4">My Website</h4>

            <nav class="nav nav-pills flex-column gap-2">
                <a href="dashboard.php" class="nav-link text-white">Dashboard</a>
                <a href="data_siswa.php" class="nav-link text-white">Data siswa</a>
                <a href="data_guru.php" class="nav-link text-white">Data guru</a>

                <a href="data_kelas.php" class="nav-link active"
                   aria-current="page">Kelas</a>

                <a href="tahun_ajaran.php" class="nav-link text-white">Tahun ajaran</a>
                <a href="penempatan_siswa.php" class="nav-link text-white">Penempatan siswa</a>
                <a href="wali_kelas.php" class="nav-link text-white">Wali Kelas</a>
                <a href="kategori_pelanggaran.php" class="nav-link text-white">Kategori pelanggaran</a>
                <a href="jenis_pelanggaran.php" class="nav-link text-white">Jenis pelanggaran</a>
                <a href="laporan.php" class="nav-link text-white">Laporan</a>
                <a href="about_me.php" class="nav-link text-white">About Me</a>

                <hr>

                <a href="logout.php" class="nav-link text-danger">Logout</a>
            </nav>

        </aside>

        <!-- KONTEN -->
        <main class="col-md-10 p-4">

            <h2>Data Kelas</h2>

            <p class="text-secondary">
                Kelola data kelas siswa.
            </p>

            <div class="card">

                <!-- HEADER -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Data Kelas</strong>

                    <a href="tambah_kelas.php" class="btn btn-primary">
                        + Tambah Kelas
                    </a>
                </div>

                <div class="card-body">

                    <!-- PENCARIAN DAN JUMLAH DATA -->
                    <div class="row mb-3 align-items-center">

                        <div class="col-md-6">
                            <label for="jumlahData" class="form-label">
                                Tampilkan
                            </label>

                            <select id="jumlahData"
                                    class="form-select d-inline-block w-auto">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="all">Semua</option>
                            </select>

                            <span class="ms-2">data</span>
                        </div>

                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <label for="cariData" class="form-label">
                                Cari:
                            </label>

                            <input type="search"
                                   id="cariData"
                                   class="form-control d-inline-block w-auto"
                                   placeholder="Cari data kelas...">
                        </div>

                    </div>

                    <!-- TABEL -->
                    <div class="table-responsive">

                        <table class="table table-bordered table-striped table-hover align-middle">

                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kelas</th>
                                    <th>Tingkat</th>
                                    <th>Jurusan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody id="tabelKelas">

                                <?php if (count($data_kelas) > 0): ?>

                                    <?php $no = 1; ?>

                                    <?php foreach ($data_kelas as $row): ?>

                                        <tr>
                                            <td><?= $no++; ?></td>

                                            <td>
                                                <?= e($row['nama'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= e($row['tingkat'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= e($row['jurusan'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?php if ((int)($row['status_aktif'] ?? 0) === 1): ?>
                                                    <span class="badge bg-success">
                                                        Aktif
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">
                                                        Tidak Aktif
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <div class="d-flex gap-1">

                                                    <a href="edit_kelas.php?id=<?= (int)$row['id']; ?>"
                                                       class="btn btn-warning btn-sm">
                                                        Edit
                                                    </a>

                                                    <form action="hapus_kelas.php"
                                                          method="POST"
                                                          onsubmit="return confirm('Yakin ingin menghapus kelas ini?');">

                                                        <input type="hidden"
                                                               name="id"
                                                               value="<?= (int)$row['id']; ?>">

                                                        <button type="submit"
                                                                class="btn btn-danger btn-sm">
                                                            Hapus
                                                        </button>

                                                    </form>

                                                </div>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            Belum ada data kelas.
                                        </td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                        <p id="infoData" class="text-secondary small"></p>

                    </div>

                </div>
            </div>

        </main>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Pencarian dan pilihan jumlah data -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cariData = document.getElementById('cariData');
    const jumlahData = document.getElementById('jumlahData');
    const tabel = document.getElementById('tabelKelas');
    const infoData = document.getElementById('infoData');

    const semuaBaris = Array.from(tabel.querySelectorAll('tr'));
    const barisData = semuaBaris.filter(function (baris) {
        return baris.cells.length === 6;
    });

    function tampilkanData() {
        const kataKunci = cariData.value.toLowerCase().trim();
        const batas = jumlahData.value === 'all'
            ? Infinity
            : parseInt(jumlahData.value, 10);

        let cocok = 0;
        let ditampilkan = 0;

        barisData.forEach(function (baris) {
            const sesuai = baris.textContent.toLowerCase().includes(kataKunci);

            if (sesuai) {
                cocok++;

                if (ditampilkan < batas) {
                    baris.hidden = false;
                    baris.cells[0].textContent = ++ditampilkan;
                } else {
                    baris.hidden = true;
                }
            } else {
                baris.hidden = true;
            }
        });

        infoData.textContent =
            'Menampilkan ' + ditampilkan + ' dari ' + cocok +
            ' data yang cocok (total ' + barisData.length + ' data).';
    }

    cariData.addEventListener('input', tampilkanData);
    jumlahData.addEventListener('change', tampilkanData);

    tampilkanData();
});
</script>

</body>
</html>
