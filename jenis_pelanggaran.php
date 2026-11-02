
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Fungsi keamanan output HTML
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Query data jenis pelanggaran dan kategori
$sql = "SELECT
            p.id,
            p.kode,
            p.nama,
            p.pelanggaran_kategori_id,
            p.poin,
            p.deskripsi,
            p.status_aktif,
            k.nama AS nama_kategori
        FROM t_pelanggaran AS p
        LEFT JOIN t_pelanggaran_kategori AS k
            ON p.pelanggaran_kategori_id = k.id
        ORDER BY p.id DESC";

$query = mysqli_query($koneksi, $sql);

if (!$query) {
    die('Query gagal: ' . e(mysqli_error($koneksi)));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Data Jenis Pelanggaran</title>

    <!-- Bootstrap 5 -->
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

                <a href="dashboard.php" class="nav-link text-white">
                    Dashboard
                </a>

                <a href="data_siswa.php" class="nav-link text-white">
                    Data siswa
                </a>

                <a href="data_guru.php" class="nav-link text-white">
                    Data guru
                </a>

                <a href="data_kelas.php" class="nav-link text-white">
                    Kelas
                </a>

                <a href="tahun_ajaran.php" class="nav-link text-white">
                    Tahun ajaran
                </a>

                <a href="penempatan_siswa.php" class="nav-link text-white">
                    Penempatan siswa
                </a>

                <a href="wali_kelas.php" class="nav-link text-white">
                    Wali kelas
                </a>

                <a href="kategori_pelanggaran.php"
                   class="nav-link text-white">
                    Kategori pelanggaran
                </a>

                <a href="jenis_pelanggaran.php"
                   class="nav-link active"
                   aria-current="page">
                    Jenis pelanggaran
                </a>

                <a href="laporan.php" class="nav-link text-white">
                    Laporan
                </a>

                <a href="about_me.php" class="nav-link text-white">
                    About me
                </a>

                <hr>

                <a href="logout.php" class="nav-link text-danger">
                    Logout
                </a>

            </nav>

        </aside>


        <!-- KONTEN UTAMA -->
        <main class="col-md-10 p-4">

            <h2>Jenis Pelanggaran</h2>

            <p class="text-secondary">
                Kelola data jenis pelanggaran siswa.
            </p>


            <!-- CARD DATA -->
            <div class="card border mb-3">

                <!-- HEADER -->
                <div class="card-header bg-light d-flex justify-content-between align-items-center">

                    <strong>Data Jenis Pelanggaran</strong>

                    <a href="tambah_jenis_pelanggaran.php"
                       class="btn btn-primary">

                        + Tambah Jenis Pelanggaran

                    </a>

                </div>


                <div class="card-body">


                    <!-- PENCARIAN DAN JUMLAH DATA -->
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

                        <div>

                            Tampilkan

                            <select id="jumlahData"
                                    class="form-select d-inline-block w-auto">

                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>

                            </select>

                            data

                        </div>


                        <div class="d-flex align-items-center gap-2">

                            <label for="cariData">Cari:</label>

                            <input
                                type="text"
                                id="cariData"
                                class="form-control"
                                placeholder="Cari jenis pelanggaran..."
                            >

                        </div>

                    </div>


                    <!-- TABEL -->
                    <div class="table-responsive">

                        <table
                            class="table table-bordered table-striped table-hover align-middle"
                            id="tabelPelanggaran"
                        >

                            <thead class="table-dark">

                                <tr>

                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Pelanggaran</th>
                                    <th>Kategori</th>
                                    <th>Poin</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php
                                $no = 1;

                                if (mysqli_num_rows($query) > 0):

                                    while ($row = mysqli_fetch_assoc($query)):
                                ?>

                                    <tr>

                                        <td><?= $no++; ?></td>


                                        <td>
                                            <?= e($row['kode']); ?>
                                        </td>


                                        <td>
                                            <?= e($row['nama']); ?>
                                        </td>


                                        <td>
                                            <?= e($row['nama_kategori'] ?? '-'); ?>
                                        </td>


                                        <td>

                                            <span class="badge bg-info text-dark">

                                                <?= (int)$row['poin']; ?> poin

                                            </span>

                                        </td>


                                        <td>
                                            <?= e($row['deskripsi'] ?: '-'); ?>
                                        </td>


                                        <td>

                                            <?php if ((int)$row['status_aktif'] === 1): ?>

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

                                                <!-- EDIT -->
                                                <a
                                                    href="edit_jenis_pelanggaran.php?id=<?= (int)$row['id']; ?>"
                                                    class="btn btn-warning btn-sm"
                                                >
                                                    Edit
                                                </a>


                                                <!-- HAPUS -->
                                                <form
                                                    action="hapus_jenis_pelanggaran.php"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus jenis pelanggaran ini?');"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= (int)$row['id']; ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn btn-danger btn-sm"
                                                    >
                                                        Hapus
                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                <?php
                                    endwhile;

                                else:
                                ?>

                                    <tr>
                                        <td colspan="8" class="text-center py-4">

                                            Belum ada data jenis pelanggaran.

                                        </td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>


                    <!-- INFORMASI DATA DAN PAGINATION -->
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">

                        <div id="infoData">
                            Menampilkan data
                        </div>


                        <div>

                            <button
                                type="button"
                                class="btn btn-outline-primary btn-sm"
                                id="sebelumnya"
                            >
                                Sebelumnya
                            </button>


                            <button
                                type="button"
                                class="btn btn-outline-primary btn-sm"
                                id="berikutnya"
                            >
                                Berikutnya
                            </button>

                        </div>

                    </div>


                </div>

            </div>


        </main>

    </div>

</div>


<!-- BOOTSTRAP JAVASCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<!-- JAVASCRIPT TABEL -->
<script>

document.addEventListener("DOMContentLoaded", function () {

    const tabel = document.getElementById("tabelPelanggaran");
    const jumlahData = document.getElementById("jumlahData");
    const cariData = document.getElementById("cariData");
    const infoData = document.getElementById("infoData");

    const sebelumnya = document.getElementById("sebelumnya");
    const berikutnya = document.getElementById("berikutnya");

    const tbody = tabel.querySelector("tbody");

    // Mengambil semua baris data
    const semuaBaris = Array.from(
        tbody.querySelectorAll("tr")
    ).filter(row => row.cells.length === 8);

    let dataFilter = [...semuaBaris];

    let halaman = 1;


    // FUNGSI MENAMPILKAN DATA
    function tampilkanData() {

        const perHalaman = parseInt(jumlahData.value);

        const totalData = dataFilter.length;

        const totalHalaman = Math.ceil(
            totalData / perHalaman
        );

        // Memastikan halaman tetap valid
        if (halaman > totalHalaman) {

            halaman = Math.max(1, totalHalaman);

        }


        // Sembunyikan semua baris
        semuaBaris.forEach(row => {

            row.hidden = true;

        });


        // Menghitung batas data
        const awal = (halaman - 1) * perHalaman;

        const akhir = awal + perHalaman;


        // Menampilkan data sesuai pilihan
        dataFilter.slice(awal, akhir).forEach(row => {

            row.hidden = false;

        });


        // Informasi jumlah data
        const dari = totalData === 0 ? 0 : awal + 1;

        const sampai = Math.min(akhir, totalData);


        infoData.textContent =
            `Menampilkan ${dari} sampai ${sampai} dari ${totalData} data`;


        // Tombol pagination
        sebelumnya.disabled = halaman <= 1;

        berikutnya.disabled = halaman >= totalHalaman;

    }


    // DROPDOWN JUMLAH DATA
    jumlahData.addEventListener("change", function () {

        halaman = 1;

        tampilkanData();

    });


    // PENCARIAN DATA
    cariData.addEventListener("input", function () {

        const keyword = this.value.toLowerCase();

        dataFilter = semuaBaris.filter(row => {

            return row.textContent.toLowerCase().includes(keyword);

        });

        halaman = 1;

        tampilkanData();

    });


    // TOMBOL SEBELUMNYA
    sebelumnya.addEventListener("click", function () {

        if (halaman > 1) {

            halaman--;

            tampilkanData();

        }

    });


    // TOMBOL BERIKUTNYA
    berikutnya.addEventListener("click", function () {

        const totalHalaman = Math.ceil(
            dataFilter.length / parseInt(jumlahData.value)
        );

        if (halaman < totalHalaman) {

            halaman++;

            tampilkanData();

        }

    });


    // TAMPILKAN DATA PERTAMA KALI
    tampilkanData();

});

</script>

</body>
</html>
