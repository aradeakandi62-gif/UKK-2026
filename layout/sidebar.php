
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="bg-dark text-white min-vh-100 d-flex flex-column p-3"
     style="width: 275px; position: fixed; top: 0; left: 0;">

    <!-- JUDUL SIDEBAR -->
    <h5 class="text-white fw-bold mb-4">
        ⚙️ Sistem Sekolah
    </h5>

    <!-- MENU SIDEBAR -->
    <ul class="nav nav-pills flex-column gap-2">

        <li class="nav-item">
            <a href="dashboard.php"
               class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : 'text-white' ?>">
                Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a href="data_siswa.php"
               class="nav-link <?= ($current_page == 'data_siswa.php') ? 'active' : 'text-white' ?>">
                Data Siswa
            </a>
        </li>

        <li class="nav-item">
            <a href="data_guru.php"
               class="nav-link <?= ($current_page == 'data_guru.php') ? 'active' : 'text-white' ?>">
                Data Guru
            </a>
        </li>

        <li class="nav-item">
            <a href="data_kelas.php"
               class="nav-link <?= ($current_page == 'data_kelas.php') ? 'active' : 'text-white' ?>">
                Kelas
            </a>
        </li>

        <li class="nav-item">
            <a href="tahun_ajaran.php"
               class="nav-link <?= ($current_page == 'tahun_ajaran.php') ? 'active' : 'text-white' ?>">
                Tahun Ajaran
            </a>
        </li>

        <li class="nav-item">
            <a href="penempatan_siswa.php"
               class="nav-link <?= ($current_page == 'penempatan_siswa.php') ? 'active' : 'text-white' ?>">
                Penempatan Siswa
            </a>
        </li>

        <li class="nav-item">
            <a href="wali_kelas.php"
               class="nav-link <?= ($current_page == 'wali_kelas.php') ? 'active' : 'text-white' ?>">
                Wali Kelas
            </a>
        </li>

        <li class="nav-item">
            <a href="kategori_pelanggaran.php"
               class="nav-link <?= ($current_page == 'kategori_pelanggaran.php') ? 'active' : 'text-white' ?>">
                Kategori Pelanggaran
            </a>
        </li>

        <li class="nav-item">
            <a href="jenis_pelanggaran.php"
               class="nav-link <?= ($current_page == 'jenis_pelanggaran.php') ? 'active' : 'text-white' ?>">
                Jenis Pelanggaran
            </a>
        </li>

        <li class="nav-item">
            <a href="laporan.php"
               class="nav-link <?= ($current_page == 'laporan.php') ? 'active' : 'text-white' ?>">
                Laporan
            </a>
        </li>

        <li class="nav-item">
            <a href="about_me.php"
               class="nav-link <?= ($current_page == 'about_me.php') ? 'active' : 'text-white' ?>">
                About Me
            </a>
        </li>

    </ul>

    <!-- LOGOUT DI BAGIAN BAWAH -->
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
