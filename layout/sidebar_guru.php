
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="col-md-3 col-lg-2 bg-dark text-white p-3
            position-fixed top-0 start-0 vh-100
            overflow-y-auto z-3">

    <h4 class="text-white mb-4">My Website</h4>

    <ul class="nav nav-pills flex-column">

        <li class="nav-item mb-2">
            <a href="dashboard_guru.php"
               class="nav-link <?= ($current_page == 'dashboard_guru.php') ? 'active' : 'text-white' ?>">
                Dashboard Guru
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="data_siswa.php"
               class="nav-link <?= ($current_page == 'data_siswa.php') ? 'active' : 'text-white' ?>">
                Data Siswa
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="catat_pelanggaran.php"
               class="nav-link <?= ($current_page == 'catat_pelanggaran.php') ? 'active' : 'text-white' ?>">
                Catat Pelanggaran
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="wali_kelas.php"
               class="nav-link <?= ($current_page == 'wali_kelas.php') ? 'active' : 'text-white' ?>">
                Wali Kelas
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="Laporan.php"
               class="nav-link <?= ($current_page == 'Laporan.php') ? 'active' : 'text-white' ?>">
                Laporan
            </a>
        </li>

        <hr class="text-secondary">

        <li class="nav-item">
            <a href="logout.php" class="nav-link text-danger">
                Logout
            </a>
        </li>

    </ul>
</div>
