<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$sql = "
    SELECT
        ks.id,
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Data Penempatan Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="d-flex">
    <?php include 'layout/sidebar.php'; ?>

    <main class="flex-grow-1 p-4">

        <h2>Data Penempatan Siswa</h2>
        <p class="text-muted">Kelola penempatan siswa berdasarkan kelas dan tahun ajaran.</p>

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Data Penempatan siswa</strong>

                <a href="tambah_penempatan_siswa.php"
                   class="btn btn-primary">
                    + Tambah Penempatan
                </a>
            </div>

            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-6 d-flex align-items-center gap-2">
                        <label for="jumlahData">Tampilkan</label>

                        <select id="jumlahData" class="form-select"
                                style="width: 90px;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">Semua</option>
                        </select>

                        <span>data</span>
                    </div>

                    <div class="col-md-6 d-flex justify-content-md-end align-items-center gap-2 mt-2 mt-md-0">
                        <label for="cariData">Cari:</label>

                        <input type="text" id="cariData"
                               class="form-control"
                               placeholder="Cari penempatan siswa..."
                               style="max-width: 250px;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">

                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Tahun Ajaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="tabelPenempatan">
                            <?php
                            $no = 1;

                            while ($row = mysqli_fetch_assoc($query)):
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>

                                    <td>
                                        <?= htmlspecialchars($row['nis'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['nama_tahun_ajaran'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <a href="edit_penempatan_siswa.php?id=<?= (int)$row['id']; ?>"
                                           class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <a href="hapus_penempatan_siswa.php?id=<?= (int)$row['id']; ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Yakin ingin menghapus penempatan siswa ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>

                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <small id="infoData" class="text-muted"></small>

                    <div class="btn-group">
                        <button type="button" id="sebelumnya"
                                class="btn btn-outline-primary btn-sm">
                            Sebelumnya
                        </button>

                        <button type="button" id="berikutnya"
                                class="btn btn-outline-primary btn-sm">
                            Berikutnya
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </main>
</div>

<script>
const tabel = document.getElementById('tabelPenempatan');
const jumlahData = document.getElementById('jumlahData');
const cariData = document.getElementById('cariData');
const infoData = document.getElementById('infoData');
const sebelumnya = document.getElementById('sebelumnya');
const berikutnya = document.getElementById('berikutnya');

let halaman = 1;

function tampilkanData() {
    const semuaBaris = Array.from(tabel.querySelectorAll('tr'));

    const hasil = semuaBaris.filter(baris =>
        baris.textContent.toLowerCase()
            .includes(cariData.value.toLowerCase())
    );

    const batas = jumlahData.value === 'all'
        ? Math.max(hasil.length, 1)
        : parseInt(jumlahData.value, 10);

    const totalHalaman = Math.max(1, Math.ceil(hasil.length / batas));

    halaman = Math.min(Math.max(halaman, 1), totalHalaman);

    semuaBaris.forEach(baris => baris.hidden = true);

    const awal = (halaman - 1) * batas;
    const akhir = Math.min(awal + batas, hasil.length);

    hasil.forEach((baris, index) => {
        baris.hidden = index < awal || index >= akhir;

        if (!baris.hidden) {
            baris.cells[0].textContent = index + 1;
        }
    });

    infoData.textContent = hasil.length === 0
        ? 'Tidak ada data ditemukan.'
        : `Menampilkan ${awal + 1} sampai ${akhir} dari ${hasil.length} data`;

    sebelumnya.disabled = halaman <= 1;
    berikutnya.disabled = halaman >= totalHalaman;
}

jumlahData.addEventListener('change', () => {
    halaman = 1;
    tampilkanData();
});

cariData.addEventListener('input', () => {
    halaman = 1;
    tampilkanData();
});

sebelumnya.addEventListener('click', () => {
    halaman--;
    tampilkanData();
});

berikutnya.addEventListener('click', () => {
    halaman++;
    tampilkanData();
});

tampilkanData();
</script>

</body>
</html>
