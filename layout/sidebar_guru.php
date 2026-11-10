
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="col-md-3 col-lg-2 bg-dark text-white p-3
            position-fixed top-0 start-0 vh-100
            overflow-y-auto z-3">

    <h4 class="text-white mb-4">Pelanggaran Siswa</h4>

    <ul class="nav nav-pills flex-column">

        <li class="nav-item mb-2">
            <a href="dashboard_guru.php"
               class="nav-link <?= ($current_page == 'dashboard_guru.php') ? 'active' : 'text-white' ?>">
                Dashboard 
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="catat_pelanggaran.php"
               class="nav-link <?= ($current_page == 'catat_pelanggaran.php') ? 'active' : 'text-white' ?>">
                Catat Pelanggaran
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="riwayat_pelanggaran.php"
               class="nav-link <?= ($current_page == 'riwayat_pelanggaran.php') ? 'active' : 'text-white' ?>">
                riwayat pelanggaran
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
