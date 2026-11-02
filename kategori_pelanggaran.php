
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Fungsi keamanan output
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Mengambil seluruh data kategori pelanggaran
$query = mysqli_query($koneksi, "
    SELECT *
    FROM t_pelanggaran_kategori
    ORDER BY id DESC
");

if (!$query) {
    die("Query gagal: " . e(mysqli_error($koneksi)));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Kategori Pelanggaran</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>


        <!-- KONTEN UTAMA -->
        <main class="col-md-10 p-4">

            <h2>Kategori Pelanggaran</h2>

            <p class="text-secondary">
                Kelola kategori pelanggaran siswa.
            </p>


            <!-- CARD DATA KATEGORI -->
            <div class="card border mb-3">


                <!-- HEADER -->
                <div class="card-header bg-light d-flex justify-content-between align-items-center">

                    <strong>Data Kategori Pelanggaran</strong>

                    <a href="tambah_kategori_pelanggaran.php"
                       class="btn btn-primary">

                        + Tambah Kategori

                    </a>

                </div>


                <div class="card-body">


                    <!-- PENCARIAN DAN JUMLAH DATA -->
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">


                        <!-- JUMLAH DATA -->
                        <div>

                            Tampilkan

                            <select
                                id="jumlahData"
                                class="form-select d-inline-block w-auto"
                            >

                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>

                            </select>

                            data

                        </div>


                        <!-- PENCARIAN -->
                        <div class="d-flex align-items-center gap-2">

                            <label for="cariKategori">
                                Cari:
                            </label>

                            <input
                                type="text"
                                id="cariKategori"
                                class="form-control"
                                placeholder="Cari kategori..."
                            >

                        </div>

                    </div>



                    <!-- TABEL KATEGORI -->
                    <div class="table-responsive">

                        <table
                            class="table table-bordered table-striped table-hover align-middle"
                            id="tabelKategori"
                        >

                            <thead class="table-dark">

                                <tr>

                                    <th>No</th>
                                    <th>Nama Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php
                                $no = 1;

                                if (mysqli_num_rows($query) > 0):

                                    while ($data = mysqli_fetch_assoc($query)):
                                ?>

                                    <tr>

                                        <!-- NOMOR -->
                                        <td>
                                            <?= $no++; ?>
                                        </td>


                                        <!-- NAMA KATEGORI -->
                                        <td>
                                            <?= e($data['nama'] ?? ''); ?>
                                        </td>


                                        <!-- DESKRIPSI -->
                                        <td>
                                            <?= e($data['deskripsi'] ?? '-'); ?>
                                        </td>


                                        <!-- STATUS -->
                                        <td>

                                            <?php if (($data['status_aktif'] ?? 0) == 1): ?>

                                                <span class="badge bg-success">
                                                    Aktif
                                                </span>

                                            <?php else: ?>

                                                <span class="badge bg-secondary">
                                                    Tidak Aktif
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- AKSI -->
                                        <td>

                                            <div class="d-flex gap-1">

                                                <!-- EDIT -->
                                                <a
                                                    href="edit_kategori_pelanggaran.php?id=<?= (int)$data['id']; ?>"
                                                    class="btn btn-warning btn-sm"
                                                >
                                                    Edit
                                                </a>


                                                <!-- HAPUS -->
                                                <a
                                                    href="hapus_kategori_pelanggaran.php?id=<?= (int)$data['id']; ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menghapus kategori ini?')"
                                                >
                                                    Hapus
                                                </a>

                                            </div>

                                        </td>

                                    </tr>

                                <?php
                                    endwhile;

                                else:
                                ?>

                                    <tr>

                                        <td colspan="5" class="text-center py-4">

                                            Belum ada data kategori pelanggaran.

                                        </td>

                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>



                    <!-- INFORMASI DATA DAN PAGINATION -->
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">


                        <!-- INFORMASI -->
                        <div id="infoData">

                            Menampilkan data

                        </div>


                        <!-- TOMBOL HALAMAN -->
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>



<!-- JAVASCRIPT TABEL -->
<script>

document.addEventListener("DOMContentLoaded", function () {

    // Mengambil elemen HTML
    const tabel = document.getElementById("tabelKategori");

    const jumlahData = document.getElementById("jumlahData");

    const cariKategori = document.getElementById("cariKategori");

    const infoData = document.getElementById("infoData");

    const sebelumnya = document.getElementById("sebelumnya");

    const berikutnya = document.getElementById("berikutnya");


    // Mengambil tbody
    const tbody = tabel.querySelector("tbody");


    // Mengambil seluruh baris data
    const semuaBaris = Array.from(
        tbody.querySelectorAll("tr")
    ).filter(row => row.cells.length === 5);


    // Variabel halaman
    let halaman = 1;

    let dataFilter = [...semuaBaris];



    // FUNGSI MENAMPILKAN DATA
    function tampilkanData() {

        const perHalaman = parseInt(jumlahData.value);

        const totalData = dataFilter.length;

        const totalHalaman = Math.ceil(
            totalData / perHalaman
        );


        // Memastikan halaman tetap benar
        if (halaman > totalHalaman) {

            halaman = Math.max(1, totalHalaman);

        }


        // Sembunyikan seluruh baris
        semuaBaris.forEach(row => {

            row.hidden = true;

        });


        // Menghitung posisi data
        const awal = (halaman - 1) * perHalaman;

        const akhir = awal + perHalaman;


        // Menampilkan data sesuai pilihan
        dataFilter.slice(awal, akhir).forEach(row => {

            row.hidden = false;

        });


        // Menghitung informasi data
        const dari = totalData === 0 ? 0 : awal + 1;

        const sampai = Math.min(akhir, totalData);


        // Menampilkan informasi
        infoData.textContent =
            `Menampilkan ${dari} sampai ${sampai} dari ${totalData} data`;


        // Mengatur tombol pagination
        sebelumnya.disabled = halaman <= 1;

        berikutnya.disabled = halaman >= totalHalaman;

    }



    // DROPDOWN JUMLAH DATA
    jumlahData.addEventListener("change", function () {

        halaman = 1;

        tampilkanData();

    });



    // PENCARIAN KATEGORI
    cariKategori.addEventListener("input", function () {

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
