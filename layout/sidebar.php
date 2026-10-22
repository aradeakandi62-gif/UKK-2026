
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- SIDEBAR -->
<div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">

    <h4 class="text-white mb-4">
        My Website
    </h4>

    <ul class="nav nav-pills flex-column">

        <li class="nav-item mb-2">
            <a href="dashboard.php"
               class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : 'text-white' ?>">
                Dashboard
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="data_siswa.php"
               class="nav-link <?= ($current_page == 'data_siswa.php') ? 'active' : 'text-white' ?>">
                Data Siswa
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="data_guru.php"
               class="nav-link <?= ($current_page == 'data_guru.php') ? 'active' : 'text-white' ?>">
                Data Guru
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="data_kelas.php"
               class="nav-link <?= ($current_page == 'data_kelas.php') ? 'active' : 'text-white' ?>">
                Kelas
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="tahun_ajaran.php"
               class="nav-link <?= ($current_page == 'tahun_ajaran.php') ? 'active' : 'text-white' ?>">
                Tahun ajaran
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="penempatan_siswa.php"
                class="nav-link <?= ($current_page == 'penempatan_siswa.php') ? 'active' : 'text-white' ?>">
                Penempatan Siswa
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="wali_kelas.php"
                class="nav-link <?= ($current_page == 'wali_kelas.php') ? 'active' : 'text-white' ?>">
                Wali Kelas
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="kategori_pelanggaran.php"
                class="nav-link <?= ($current_page == 'kategori_pelanggaran.php') ? 'active' : 'text-white' ?>">
                Kategori pelanggaran
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="jenis_pelanggaran.php"
                class="nav-link <?= ($current_page == 'jenis_pelanggaran.php') ? 'active' : 'text-white' ?>">
                Jenis pelanggaran
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="Laporan.php"
               class="nav-link <?= ($current_page == 'Laporan.php') ? 'active' : 'text-white' ?>">
                Laporan
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="about_me.php"
               class="nav-link <?= ($current_page == 'about_me.php') ? 'active' : 'text-white' ?>">
                About Me
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
