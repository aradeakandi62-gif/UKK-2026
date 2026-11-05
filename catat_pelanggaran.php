
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

// Khusus guru
if ($_SESSION['role'] != 'guru') {
    header("Location: dashboard.php");
    exit;
}

// ===============================
// DATA SISWA
// ===============================
$siswa = mysqli_query($koneksi, "
    SELECT id, nis, nama
    FROM t_siswa
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

// ===============================
// DATA TAHUN AJARAN
// ===============================
$tahun = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_tahun_ajaran
    WHERE status_aktif = 1
    ORDER BY id DESC
");

// ===============================
// DATA KELAS
// ===============================
$kelas = mysqli_query($koneksi, "
    SELECT id, nama, tingkat, jurusan
    FROM t_kelas
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

// ===============================
// DATA JENIS PELANGGARAN
// ===============================
$pelanggaran = mysqli_query($koneksi, "
    SELECT id, nama, poin, pelanggaran_kategori_id
    FROM t_pelanggaran
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

// ===============================
// DATA GURU
// ===============================
$guru = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_guru
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

// ===============================
// DATA CATATAN PELANGGARAN
// ===============================
$sql = "
    SELECT
        ps.*,
        s.nama AS nama_siswa,
        s.nis,
        k.nama AS nama_kelas,
        ta.nama AS tahun_ajaran,
        p.nama AS nama_pelanggaran,
        g.nama AS nama_guru

    FROM t_pelanggaran_siswa ps

    LEFT JOIN t_siswa s
        ON ps.siswa_id = s.id

    LEFT JOIN t_kelas k
        ON ps.kelas_id = k.id

    LEFT JOIN t_tahun_ajaran ta
        ON ps.tahun_ajaran_id = ta.id

    LEFT JOIN t_pelanggaran p
        ON ps.pelanggaran_id = p.id

    LEFT JOIN t_guru g
        ON ps.guru_id = g.id

    ORDER BY ps.id DESC
";

$hasil = mysqli_query($koneksi, $sql);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Catat Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR GURU -->
        <?php include 'layout/sidebar_guru.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 ms-auto p-4">

            <!-- JUDUL -->
            <div class="mb-4">
                <h2>Catat Pelanggaran Siswa</h2>
                <p class="text-muted">
                    Form pencatatan pelanggaran siswa sekolah.
                </p>
            </div>

            <!-- PESAN SUKSES -->
            <?php if (isset($_SESSION['pesan_sukses'])) { ?>

                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['pesan_sukses']); ?>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="alert"></button>
                </div>

                <?php unset($_SESSION['pesan_sukses']); ?>

            <?php } ?>

            <!-- PESAN ERROR -->
            <?php if (isset($_SESSION['pesan_error'])) { ?>

                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="alert"></button>
                </div>

                <?php unset($_SESSION['pesan_error']); ?>

            <?php } ?>


            <!-- FORM CATAT PELANGGARAN -->
            <div class="card shadow-sm mb-4">

                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Form Catat Pelanggaran</h5>
                </div>

                <div class="card-body">

                    <form action="proses_simpan_pelanggaran.php"
                          method="POST">

                        <div class="row">

                            <!-- SISWA -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Siswa</label>

                                <select name="siswa_id"
                                        class="form-select" required>

                                    <option value="">-- Pilih Siswa --</option>

                                    <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>

                                        <option value="<?= $s['id']; ?>">
                                            <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>
                                        </option>

                                    <?php } ?>

                                </select>
                            </div>


                            <!-- TAHUN AJARAN -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun Ajaran</label>

                                <select name="tahun_ajaran_id"
                                        class="form-select" required>

                                    <option value="">-- Pilih Tahun Ajaran --</option>

                                    <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>

                                        <option value="<?= $ta['id']; ?>">
                                            <?= htmlspecialchars($ta['nama']); ?>
                                        </option>

                                    <?php } ?>

                                </select>
                            </div>


                            <!-- KELAS -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kelas</label>

                                <select name="kelas_id"
                                        class="form-select" required>

                                    <option value="">-- Pilih Kelas --</option>

                                    <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>

                                        <option value="<?= $k['id']; ?>">
                                            <?= htmlspecialchars(
                                                $k['nama'] . ' - ' .
                                                $k['tingkat'] . ' - ' .
                                                $k['jurusan']
                                            ); ?>
                                        </option>

                                    <?php } ?>

                                </select>
                            </div>


                            <!-- JENIS PELANGGARAN -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Pelanggaran</label>

                                <select name="pelanggaran_id"
                                        id="pelanggaran"
                                        class="form-select" required>

                                    <option value="">-- Pilih Pelanggaran --</option>

                                    <?php while ($p = mysqli_fetch_assoc($pelanggaran)) { ?>

                                        <option
                                            value="<?= $p['id']; ?>"
                                            data-poin="<?= $p['poin']; ?>"
                                            data-kategori="<?= $p['pelanggaran_kategori_id']; ?>">

                                            <?= htmlspecialchars(
                                                $p['nama'] . ' (' .
                                                $p['poin'] . ' poin)'
                                            ); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                                <input type="hidden"
                                       name="pelanggaran_kategori_id"
                                       id="kategori">
                            </div>


                            <!-- GURU PELAPOR -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guru Pelapor</label>

                                <select name="guru_id"
                                        class="form-select" required>

                                    <option value="">-- Pilih Guru --</option>

                                    <?php while ($g = mysqli_fetch_assoc($guru)) { ?>

                                        <option value="<?= $g['id']; ?>">
                                            <?= htmlspecialchars($g['nama']); ?>
                                        </option>

                                    <?php } ?>

                                </select>
                            </div>


                            <!-- TANGGAL -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal</label>

                                <input type="date"
                                       name="tanggal"
                                       class="form-control"
                                       value="<?= date('Y-m-d'); ?>"
                                       required>
                            </div>


                            <!-- POIN -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Poin Pelanggaran</label>

                                <input type="number"
                                       name="poin"
                                       id="poin"
                                       class="form-control"
                                       min="0"
                                       required>
                            </div>


                            <!-- STATUS -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>

                                <select name="status"
                                        class="form-select" required>

                                    <option value="Ditindaklanjuti">
                                        Ditindaklanjuti
                                    </option>

                                    <option value="Proses">
                                        Proses
                                    </option>

                                    <option value="Selesai">
                                        Selesai
                                    </option>

                                </select>
                            </div>


                            <!-- KETERANGAN -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Keterangan</label>

                                <textarea name="keterangan"
                                          class="form-control"
                                          rows="3"></textarea>
                            </div>


                            <!-- TINDAKAN -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tindakan</label>

                                <textarea name="tindakan"
                                          class="form-control"
                                          rows="3"></textarea>
                            </div>

                        </div>


                        <!-- TOMBOL -->
                        <button type="submit" class="btn btn-primary">
                            Simpan Pelanggaran
                        </button>

                        <a href="dashboard_guru.php"
                           class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>
            </div>


            <!-- TABEL DATA PELANGGARAN -->
            <div class="card shadow-sm">

                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Data Catatan Pelanggaran</h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-striped table-hover align-middle">

                            <thead class="table-dark">

                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Pelanggaran</th>
                                    <th>Guru</th>
                                    <th>Tanggal</th>
                                    <th>Poin</th>
                                    <th>Tindakan</th>
                                    <th>Status</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php
                                $no = 1;

                                if ($hasil && mysqli_num_rows($hasil) > 0) {

                                    while ($row = mysqli_fetch_assoc($hasil)) {
                                ?>

                                    <tr>

                                        <td><?= $no++; ?></td>

                                        <td>
                                            <?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['tahun_ajaran'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['nama_guru'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['tanggal'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <span class="badge bg-primary">
                                                <?= (int) $row['poin']; ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['tindakan'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?php
                                            $status = $row['status'] ?? '';

                                            if ($status == 'Selesai') {
                                                echo '<span class="badge bg-success">Selesai</span>';
                                            } elseif ($status == 'Proses') {
                                                echo '<span class="badge bg-warning text-dark">Proses</span>';
                                            } else {
                                                echo '<span class="badge bg-info text-dark">Ditindaklanjuti</span>';
                                            }
                                            ?>
                                        </td>

                                    </tr>

                                <?php
                                    }

                                } else {
                                ?>

                                    <tr>
                                        <td colspan="10" class="text-center text-muted">
                                            Belum ada data catatan pelanggaran.
                                        </td>
                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                </div>
            </div>

        </main>

    </div>
</div>


<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>


<!-- OTOMATIS MENGISI POIN DAN KATEGORI -->
<script>

const pilihanPelanggaran = document.getElementById('pelanggaran');

const inputPoin = document.getElementById('poin');

const inputKategori = document.getElementById('kategori');

pilihanPelanggaran.addEventListener('change', function () {

    const opsi = this.options[this.selectedIndex];

    inputPoin.value = opsi.dataset.poin || '';

    inputKategori.value = opsi.dataset.kategori || '';

});

</script>

</body>
</html>
