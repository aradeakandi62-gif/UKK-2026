<?php
session_start();
include "config/koneksi.php";

// Cek apakah sudah login dan role-nya adalah guru
if (!isset($_SESSION["login"]) || $_SESSION["role"] !== "guru") {
    header("Location: login.php");
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Ambil daftar kolom yang benar-benar ada di tabel t_pelanggaran_siswa secara otomatis
$table_columns = [];
$col_query = mysqli_query($koneksi, "SHOW COLUMNS FROM t_pelanggaran_siswa");
if ($col_query) {
    while($c = mysqli_fetch_assoc($col_query)){
        $table_columns[] = $c['Field'];
    }
}

// --- PROSES SIMPAN / TAMBAH PELANGGARAN ---
if (isset($_POST['simpan_pelanggaran'])) {
    $input_nama         = $_POST['nama_siswa'] ?? '';
    $input_pelanggaran = $_POST['pelanggaran'] ?? '';
    $input_tanggal     = $_POST['tanggal'] ?? date('Y-m-d');
    $input_poin        = $_POST['poin'] ?? 0;
    $input_tindakan    = $_POST['tindakan'] ?? '';
    $input_guru        = $_SESSION['name'] ?? 'Guru';

    $data_kandidat = [
        'nama_siswa'        => $input_nama,
        'nama'              => $input_nama,
        'pelanggaran'       => $input_pelanggaran,
        'jenis_pelanggaran' => $input_pelanggaran,
        'keterangan'        => $input_pelanggaran,
        'tanggal'           => $input_tanggal,
        'poin'              => $input_poin,
        'tindakan'          => $input_tindakan,
        'guru'              => $input_guru
    ];

    $fields = [];
    $values = [];

    foreach ($data_kandidat as $col_name => $val) {
        if (in_array($col_name, $table_columns)) {
            $fields[] = $col_name;
            $values[] = "'" . mysqli_real_escape_string($koneksi, $val) . "'";
        }
    }

    if (!empty($fields)) {
        $sql_insert = "INSERT INTO t_pelanggaran_siswa (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $query_tambah = mysqli_query($koneksi, $sql_insert);
        
        if($query_tambah) {
            header("Location: dashboard_guru.php?page=riwayat");
            exit;
        } else {
            $db_error = mysqli_error($koneksi);
        }
    } else {
        $db_error = "Tidak ada kolom yang cocok antara form dan tabel database.";
    }
}

// --- PROSES HAPUS PELANGGARAN ---
if (isset($_GET['id_hapus'])) {
    $id = $_GET['id_hapus'];
    mysqli_query($koneksi, "DELETE FROM t_pelanggaran_siswa WHERE id='$id'");
    header("Location: dashboard_guru.php?page=riwayat");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - Sistem Informasi Pelanggaran Siswa</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Sidebar Navigasi Menggunakan Flexbox & Utility Bootstrap -->
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white position-fixed top-0 bottom-0 start-0" style="width: 250px; z-index: 100;">
        <h4 class="fs-5 fw-bold mb-4 ps-2">Pelanggaran Siswa</h4>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-1">
                <a href="dashboard_guru.php?page=dashboard" class="nav-link text-white <?= ($page == 'dashboard') ? 'active' : ''; ?>">Dashboard</a>
            </li>
            <li class="nav-item mb-1">
                <a href="dashboard_guru.php?page=catat" class="nav-link text-white <?= ($page == 'catat') ? 'active' : ''; ?>">Catat Pelanggaran</a>
            </li>
            <li class="nav-item mb-1">
                <a href="dashboard_guru.php?page=riwayat" class="nav-link text-white <?= ($page == 'riwayat') ? 'active' : ''; ?>">Riwayat Pelanggaran</a>
            </li>
            <li class="nav-item mb-1">
                <a href="dashboard_guru.php?page=tindakan" class="nav-link text-white <?= ($page == 'tindakan') ? 'active' : ''; ?>">Referensi Tindakan</a>
            </li>
            <li class="nav-item mb-1">
                <a href="dashboard_guru.php?page=rekap" class="nav-link text-white <?= ($page == 'rekap') ? 'active' : ''; ?>">Rekap Poin Siswa</a>
            </li>
        </ul>
        <hr>
        <a href="logout.php" class="nav-link text-danger fw-semibold ps-2">Logout</a>
    </div>

    <!-- Konten Utama (Diberi margin-left agar tidak tertutup sidebar) -->
    <div style="margin-left: 250px;" class="p-4 p-md-5">
        <?php if (!empty($db_error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal Menyimpan!</strong> <?= htmlspecialchars($db_error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($page == 'dashboard'): ?>
            <!-- DASHBOARD -->
            <h2 class="fw-bold mb-2">Dashboard Guru</h2>
            <p class="text-secondary mb-4">Selamat datang, <?= htmlspecialchars($_SESSION['name'] ?? ''); ?>. Berikut ringkasan sistem pelanggaran siswa.</p>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border shadow-sm p-3">
                        <h5 class="text-secondary fs-6 mb-3">Total Siswa Terdaftar</h5>
                        <h3 class="text-primary fw-normal mb-0">50</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border shadow-sm p-3">
                        <h5 class="text-secondary fs-6 mb-3">Total Guru</h5>
                        <h3 class="fw-normal mb-0">25</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border shadow-sm p-3">
                        <h5 class="text-secondary fs-6 mb-3">Jumlah Kelas</h5>
                        <h3 class="fw-normal mb-0">12</h3>
                    </div>
                </div>
            </div>

        <?php elseif ($page == 'catat'): ?>
            <!-- FORM CATAT PELANGGARAN -->
            <a href="dashboard_guru.php?page=dashboard" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>
            <h2 class="fw-bold mb-1">Form Catat Pelanggaran Siswa</h2>
            <p class="text-secondary mb-4">Masukkan data pelanggaran siswa dengan benar.</p>
            
            <div class="card border shadow-sm p-4">
                <form action="" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Siswa</label>
                            <input type="text" name="nama_siswa" class="form-control" placeholder="Contoh: Budi Santoso" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bentuk Pelanggaran</label>
                            <input type="text" name="pelanggaran" class="form-control" placeholder="Contoh: Terlambat hadir" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Poin Pelanggaran</label>
                            <input type="number" name="poin" class="form-control" placeholder="Contoh: 10" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tindakan / Sanksi</label>
                        <input type="text" name="tindakan" class="form-control" placeholder="Contoh: Teguran lisan" required>
                    </div>
                    <button type="submit" name="simpan_pelanggaran" class="btn btn-primary">Simpan Pelanggaran</button>
                </form>
            </div>

        <?php elseif ($page == 'riwayat'): ?>
            <!-- RIWAYAT PELANGGARAN -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold mb-0">Riwayat Pelanggaran Siswa</h2>
                <a href="dashboard_guru.php?page=catat" class="btn btn-primary">+ Tambah Pelanggaran Baru</a>
            </div>
            
            <a href="dashboard_guru.php?page=dashboard" class="btn btn-secondary mb-4">Kembali ke Dashboard</a>
            
            <div class="card border shadow-sm p-4">
                <?php
                $batas = isset($_GET['batas']) ? (int)$_GET['batas'] : 10;
                $halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
                $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

                $cari = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : '';
                
                $where_sql = "";
                if (!empty($cari)) {
                    $where_sql = "WHERE nama_siswa LIKE '%$cari%' OR nama LIKE '%$cari%' OR pelanggaran LIKE '%$cari%' OR jenis_pelanggaran LIKE '%$cari%' OR keterangan LIKE '%$cari%' OR tindakan LIKE '%$cari%' OR guru LIKE '%$cari%'";
                }

                $query_jumlah = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa $where_sql");
                $jumlah_data = mysqli_num_rows($query_jumlah);
                $total_halaman = ceil($jumlah_data / $batas);

                $data_sql = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa $where_sql ORDER BY id DESC LIMIT $halaman_awal, $batas");
                $nomor = $halaman_awal + 1;
                ?>

                <!-- Filter & Search Bar -->
                <form method="GET" action="dashboard_guru.php" class="row mb-3 align-items-center">
                    <input type="hidden" name="page" value="riwayat">
                    <div class="col-md-6 d-flex align-items-center">
                        <span class="me-2">Tampilkan</span>
                        <select name="batas" class="form-select form-select-sm w-auto d-inline-block" onchange="this.form.submit()">
                            <option value="10" <?= ($batas == 10) ? 'selected' : ''; ?>>10</option>
                            <option value="25" <?= ($batas == 25) ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?= ($batas == 50) ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?= ($batas == 100) ? 'selected' : ''; ?>>100</option>
                        </select>
                        <span class="ms-2">data</span>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end align-items-center">
                        <label class="me-2 mb-0">Cari:</label>
                        <input type="text" name="cari" class="form-control form-control-sm w-50" value="<?= htmlspecialchars($cari); ?>" onchange="this.form.submit()">
                    </div>
                </form>

                <!-- Tabel Data -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Pelanggaran</th>
                                <th>Tanggal</th>
                                <th>Poin</th>
                                <th>Tindakan</th>
                                <th>Pelapor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($data_sql && mysqli_num_rows($data_sql) > 0) {
                                while($d = mysqli_fetch_assoc($data_sql)){
                                    $id_data     = $d['id'] ?? '';
                                    $nama_siswa  = $d['nama_siswa'] ?? ($d['nama'] ?? '-');
                                    $pelanggaran = $d['pelanggaran'] ?? ($d['jenis_pelanggaran'] ?? ($d['keterangan'] ?? '-'));
                                    $tanggal     = $d['tanggal'] ?? '-';
                                    $poin        = $d['poin'] ?? '0';
                                    $tindakan    = $d['tindakan'] ?? '-';
                                    $guru        = $d['guru'] ?? '-';

                                    echo "<tr>
                                        <td>{$nomor}</td>
                                        <td>{$nama_siswa}</td>
                                        <td>{$pelanggaran}</td>
                                        <td>{$tanggal}</td>
                                        <td><span class='badge bg-danger'>{$poin}</span></td>
                                        <td>{$tindakan}</td>
                                        <td>{$guru}</td>
                                        <td>
                                            <a href='dashboard_guru.php?page=edit&id={$id_data}' class='btn btn-warning btn-sm'>Edit</a>
                                            <a href='dashboard_guru.php?id_hapus={$id_data}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin menghapus data ini?\")'>Hapus</a>
                                        </td>
                                    </tr>";
                                    $nomor++;
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center text-muted'>Belum ada data pelanggaran.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Info & Navigasi Halaman (Pagination) -->
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                    <div class="text-secondary small mb-2 mb-md-0">
                        Menampilkan <?= ($jumlah_data > 0) ? $halaman_awal + 1 : 0; ?> sampai <?= min($halaman_awal + $batas, $jumlah_data); ?> dari <?= $jumlah_data; ?> data
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="dashboard_guru.php?page=riwayat&halaman=1&batas=<?= $batas; ?>&cari=<?= urlencode($cari); ?>">Pertama</a>
                            </li>
                            <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="dashboard_guru.php?page=riwayat&halaman=<?= max(1, $halaman - 1); ?>&batas=<?= $batas; ?>&cari=<?= urlencode($cari); ?>">Sebelumnya</a>
                            </li>

                            <?php 
                            $start_p = max(1, $halaman - 2);
                            $end_p = min($total_halaman, $halaman + 2);
                            for($i = $start_p; $i <= $end_p; $i++): 
                            ?>
                                <li class="page-item <?= ($halaman == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="dashboard_guru.php?page=riwayat&halaman=<?= $i; ?>&batas=<?= $batas; ?>&cari=<?= urlencode($cari); ?>"><?= $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= ($halaman >= $total_halaman || $total_halaman == 0) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="dashboard_guru.php?page=riwayat&halaman=<?= min($total_halaman, $halaman + 1); ?>&batas=<?= $batas; ?>&cari=<?= urlencode($cari); ?>">Berikutnya</a>
                            </li>
                            <li class="page-item <?= ($halaman >= $total_halaman || $total_halaman == 0) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="dashboard_guru.php?page=riwayat&halaman=<?= max(1, $total_halaman); ?>&batas=<?= $batas; ?>&cari=<?= urlencode($cari); ?>">Terakhir</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

        <?php elseif ($page == 'edit'): ?>
            <!-- HALAMAN EDIT DATA -->
            <?php
            $id_edit = $_GET['id'] ?? '';
            $q_edit = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa WHERE id='$id_edit'");
            $data_edit = mysqli_fetch_assoc($q_edit);

            if(isset($_POST['update_pelanggaran'])) {
                $ns  = mysqli_real_escape_string($koneksi, $_POST['nama_siswa']);
                $pel = mysqli_real_escape_string($koneksi, $_POST['pelanggaran']);
                $tgl = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
                $pn  = mysqli_real_escape_string($koneksi, $_POST['poin']);
                $tnd = mysqli_real_escape_string($koneksi, $_POST['tindakan']);

                $update_cols = [];
                if(in_array('nama_siswa', $table_columns)) $update_cols[] = "nama_siswa='$ns'";
                elseif(in_array('nama', $table_columns)) $update_cols[] = "nama='$ns'";

                if(in_array('pelanggaran', $table_columns)) $update_cols[] = "pelanggaran='$pel'";
                elseif(in_array('jenis_pelanggaran', $table_columns)) $update_cols[] = "jenis_pelanggaran='$pel'";
                elseif(in_array('keterangan', $table_columns)) $update_cols[] = "keterangan='$pel'";

                if(in_array('tanggal', $table_columns)) $update_cols[] = "tanggal='$tgl'";
                if(in_array('poin', $table_columns)) $update_cols[] = "poin='$pn'";
                if(in_array('tindakan', $table_columns)) $update_cols[] = "tindakan='$tnd'";

                if(!empty($update_cols)) {
                    mysqli_query($koneksi, "UPDATE t_pelanggaran_siswa SET " . implode(', ', $update_cols) . " WHERE id='$id_edit'");
                }

                header("Location: dashboard_guru.php?page=riwayat");
                exit;
            }
            ?>
            <a href="dashboard_guru.php?page=riwayat" class="btn btn-secondary mb-3">Kembali ke Riwayat</a>
            <h2 class="fw-bold mb-3">Edit Data Pelanggaran Siswa</h2>
            <div class="card border shadow-sm p-4">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Siswa</label>
                        <input type="text" name="nama_siswa" class="form-control" value="<?= $data_edit['nama_siswa'] ?? ($data_edit['nama'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bentuk Pelanggaran</label>
                        <input type="text" name="pelanggaran" class="form-control" value="<?= $data_edit['pelanggaran'] ?? ($data_edit['jenis_pelanggaran'] ?? ($data_edit['keterangan'] ?? '')); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= $data_edit['tanggal'] ?? ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Poin</label>
                        <input type="number" name="poin" class="form-control" value="<?= $data_edit['poin'] ?? ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tindakan</label>
                        <input type="text" name="tindakan" class="form-control" value="<?= $data_edit['tindakan'] ?? ''; ?>" required>
                    </div>
                    <button type="submit" name="update_pelanggaran" class="btn btn-success">Perbarui Data</button>
                    <a href="dashboard_guru.php?page=riwayat" class="btn btn-secondary">Batal</a>
                </form>
            </div>

        <?php elseif ($page == 'tindakan'): ?>
            <!-- REFERENSI TINDAKAN -->
            <a href="dashboard_guru.php?page=dashboard" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>
            <h2 class="fw-bold mb-3">Referensi Tindakan Berdasarkan Poin</h2>
            <div class="card border shadow-sm p-4">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Akumulasi Poin</th>
                            <th>Jenis Tindakan / Sanksi</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>1</td><td>10 - 25</td><td>Teguran Lisan</td><td>Diberikan langsung oleh guru piket atau wali kelas.</td></tr>
                        <tr><td>2</td><td>26 - 50</td><td>Pembinaan Khusus</td><td>Surat pernyataan dan pembinaan kedisiplinan.</td></tr>
                        <tr><td>3</td><td>> 50</td><td>Pemanggilan Orang Tua / Wali</td><td>Pemanggilan resmi orang tua murid ke sekolah.</td></tr>
                    </tbody>
                </table>
            </div>

        <?php elseif ($page == 'rekap'): ?>
            <!-- REKAP POIN -->
            <a href="dashboard_guru.php?page=dashboard" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>
            <h2 class="fw-bold mb-3">Rekap Akumulasi Poin Siswa</h2>
            <div class="card border shadow-sm p-4">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Total Poin Terakumulasi</th>
                            <th>Status Kedisiplinan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>1</td><td>Siswa 100</td><td>20</td><td><span class="badge bg-warning text-dark">Aman / Dalam Pengawasan</span></td></tr>
                        <tr><td>2</td><td>Siswa 99</td><td>35</td><td><span class="badge bg-danger">Peringatan / Pembinaan</span></td></tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>