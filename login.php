<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Sistem Pelanggaran Siswa</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-dark text-white">

    <div class="container min-vh-100 d-flex justify-content-center align-items-center">

        <!-- KOTAK LOGIN -->
        <div class="bg-black border border-white border-2 rounded-4 shadow p-5"
             style="width: 400px;">

            <!-- Judul -->
            <h2 class="text-center fw-bold mb-4">
                Login
            </h2>

            <!-- Pesan -->
            <?php if (isset($_GET['pesan'])): ?>

                <?php if ($_GET['pesan'] == 'gagal'): ?>

                    <div class="alert alert-danger">
                        Email atau password salah!
                    </div>

                <?php elseif ($_GET['pesan'] == 'logout'): ?>

                    <div class="alert alert-success">
                        Anda berhasil logout.
                    </div>

                <?php endif; ?>

            <?php endif; ?>


            <!-- FORM LOGIN -->
            <form action="proses_login.php" method="POST">

                <!-- Email -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Masukkan email"
                        required
                    >

                </div>


                <!-- Password -->
                <div class="mb-4">

                    <label class="form-label fw-bold">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        required
                    >

                </div>


                <!-- Tombol -->
                <button
                    type="submit"
                    class="btn btn-dark border border-white border-2 w-100 fw-bold py-2"
                >
                    Login
                </button>

            </form>

        </div>

    </div>

</body>

</html>