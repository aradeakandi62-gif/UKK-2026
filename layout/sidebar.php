
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$role = strtolower(trim($_SESSION['role'] ?? ''));

// MENU BERDASARKAN ROLE
if ($role === 'admin') {

    $menu = [
        ['Dashboard', 'dashboard.php'],
        ['Data Siswa', 'data_siswa.php'],
        ['Data Guru', 'data_guru.php'],
        ['Kelas', 'data_kelas.php'],
        ['Tahun Ajaran', 'tahun_ajaran.php'],
        ['Penempatan Siswa', 'penempatan_siswa.php'],
        ['Wali Kelas', 'wali_kelas.php'],
        ['Kategori Pelanggaran', 'kategori_pelanggaran.php'],
        ['Jenis Pelanggaran', 'jenis_pelanggaran.php'],
        ['Laporan', 'laporan.php'],
        ['About Me', 'about_me.php']
    ];

} elseif ($role === 'guru') {

    $menu = [
        ['Dashboard', 'dashboard.php'],
        ['Catat Pelanggaran', 'catat_pelanggaran.php'],
        ['Tindakan', 'tindakan_pelanggaran.php'],
        ['Rekap Poin', 'rekap_poin.php'],
        ['Riwayat', 'riwayat_pelanggaran.php']
    ];

} elseif ($role === 'wali_kelas') {

    $menu = [
        ['Dashboard', 'dashboard.php'],
        ['Catat Pelanggaran', 'catat_pelanggaran.php'],
        ['Tindakan', 'tindakan_pelanggaran.php'],
        ['Rekap Poin', 'rekap_poin.php'],
        ['Riwayat', 'riwayat_pelanggaran.php'],
        ['Laporan', 'laporan.php']
    ];

} else {

    $menu = [];

}
?>

<div class="bg-dark text-white min-vh-100 d-flex flex-column p-3"
     style="width:275px; position:fixed; top:0; left:0; overflow-y:auto; z-index:1000;">

    <!-- JUDUL -->
    <h5 class="text-white fw-bold mb-4">
        ⚙️ Sistem Sekolah
    </h5>

    <!-- INFORMASI PENGGUNA -->
    <div class="mb-3 border-bottom border-secondary pb-3">

        <div class="fw-bold">
            <?= htmlspecialchars($_SESSION['name'] ?? 'Pengguna') ?>
        </div>

        <small class="text-secondary">
            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $role))) ?>
        </small>

    </div>

    <!-- MENU -->
    <ul class="nav nav-pills flex-column gap-2">

        <?php foreach ($menu as $item): ?>

            <li class="nav-item">

                <a href="<?= htmlspecialchars($item[1]) ?>"
                   class="nav-link <?= ($current_page === $item[1]) ? 'active' : 'text-white' ?>">

                    <?= htmlspecialchars($item[0]) ?>

                </a>

            </li>

        <?php endforeach; ?>

    </ul>

    <!-- LOGOUT -->
    <div class="mt-auto pt-3 border-top border-secondary">

        <a href="logout.php" class="nav-link text-danger">
            Logout
        </a>

    </div>

</div>

<style>
    .nav-link {
        padding: 10px 15px;
        transition: 0.2s;
    }

    .nav-link:hover {
        background-color: #343a40;
        color: white !important;
    }

    .nav-link.active {
        background-color: #0d6efd !important;
        color: white !important;
    }
</style>
