
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$role = strtolower(trim($_SESSION['role'] ?? ''));

// Tentukan dashboard berdasarkan role
$dashboard = ($role === 'guru') ? 'dashboard_guru.php' : 'dashboard.php';
?>

<div class="col-md-3 col-lg-2 bg-dark text-white p-3
            position-fixed top-0 start-0 vh-100
            overflow-y-auto z-3">

    <h4 class="text-white mb-4">
        ⚙️Sistem Sekolah
    </h4>

    <ul class="nav nav-pills flex-column">

        <!-- DASHBOARD -->
        <li class="nav-item mb-2">
            <a href="<?= $dashboard ?>"
               class="nav-link <?= ($current_page == $dashboard) ? 'active' : 'text-white' ?>">
                Dashboard
            </a>
        </li>

        <?php if ($role === 'admin'): ?>

            <!-- MENU KHUSUS ADMIN -->

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
                    Tahun Ajaran
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
                    Kategori Pelanggaran
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="jenis_pelanggaran.php"
                   class="nav-link <?= ($current_page == 'jenis_pelanggaran.php') ? 'active' : 'text-white' ?>">
                    Jenis Pelanggaran
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
                   class="nav-link <?= ($current_page == 'about.php') ? 'active' : 'text-white' ?>">
                    About Me
                </a>
            </li>
            
        <?php elseif ($role === 'guru'): ?>

            <!-- MENU KHUSUS GURU -->

            <li class="nav-item mb-2">
                <a href="about.php"
                   class="nav-link <?= ($current_page == 'about.php') ? 'active' : 'text-white' ?>">
                    About Me
                </a>
            </li>

        <?php endif; ?>

        <hr class="border-secondary">

        <!-- LOGOUT -->
        <li class="nav-item">
            <a href="logout.php" class="nav-link text-danger">
                Logout
            </a>
        </li>

    </ul>
</div>
