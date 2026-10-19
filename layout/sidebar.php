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


               
<!-- Menu Data Siswa dan Data Guru khusus Admin -->
<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>

    <li class="nav-item mb-2">
        <a href="data_siswa.php" 
        class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : 'text-white' ?>">
            Data Siswa
        </a>
    </li>

    <li class="nav-item mb-2">
        <a href="Data Guru" 
        class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : 'text-white' ?>">
            Data Guru
        </a>
    </li>

<?php endif; ?>
                
        <li class="nav-item mb-2">
            <a href="data_kelas.php" 
            class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : 'text-white' ?>">
                Kelas
            </a>
        </li>

                <li class="nav-item mb-2">
                    <a href="Laporan" class="nav-link text-white">
                        Laporan
                    </a>
                </li>

                <li class="nav-item mb-2">
    <a href="about.php" class="nav-link text-white">
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