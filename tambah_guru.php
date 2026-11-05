<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nip = trim($_POST['nip'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $status_aktif = (int) ($_POST['status_aktif'] ?? 1);

    if ($nip === '' || $nama === '' || $email === '' || $password === '') {
        $error = 'Semua data wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {

        // Cek NIP dan email
        $cek = mysqli_prepare($koneksi, "
            SELECT id FROM t_guru
            WHERE nip = ? OR email = ?
            LIMIT 1
        ");

        mysqli_stmt_bind_param($cek, "ss", $nip, $email);
        mysqli_stmt_execute($cek);
        $hasil = mysqli_stmt_get_result($cek);

        // Cek email di tabel user
        $cek_user = mysqli_prepare($koneksi, "
            SELECT id FROM t_users
            WHERE email = ?
            LIMIT 1
        ");

        mysqli_stmt_bind_param($cek_user, "s", $email);
        mysqli_stmt_execute($cek_user);
        $hasil_user = mysqli_stmt_get_result($cek_user);

        if (mysqli_num_rows($hasil) > 0) {

            $error = 'NIP atau email guru sudah terdaftar!';

        } elseif (mysqli_num_rows($hasil_user) > 0) {

            $error = 'Email sudah digunakan oleh akun lain!';

        } else {

            // Enkripsi password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Mulai transaksi
            mysqli_begin_transaction($koneksi);

            try {

                // Simpan akun ke t_users
                $stmt_user = mysqli_prepare($koneksi, "
                    INSERT INTO t_users
                    (name, email, password, role)
                    VALUES (?, ?, ?, 'guru')
                ");

                if (!$stmt_user) {
                    throw new Exception(mysqli_error($koneksi));
                }

                mysqli_stmt_bind_param(
                    $stmt_user,
                    "sss",
                    $nama,
                    $email,
                    $password_hash
                );

                if (!mysqli_stmt_execute($stmt_user)) {
                    throw new Exception(mysqli_stmt_error($stmt_user));
                }

                // Ambil ID user yang baru dibuat
                $user_id = mysqli_insert_id($koneksi);

                // Simpan data guru
                $stmt_guru = mysqli_prepare($koneksi, "
                    INSERT INTO t_guru
                    (nip, nama, email, status_aktif, user_id, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                ");

                if (!$stmt_guru) {
                    throw new Exception(mysqli_error($koneksi));
                }

                mysqli_stmt_bind_param(
                    $stmt_guru,
                    "sssii",
                    $nip,
                    $nama,
                    $email,
                    $status_aktif,
                    $user_id
                );

                if (!mysqli_stmt_execute($stmt_guru)) {
                    throw new Exception(mysqli_stmt_error($stmt_guru));
                }

                // Jika semuanya berhasil
                mysqli_commit($koneksi);

                mysqli_stmt_close($stmt_user);
                mysqli_stmt_close($stmt_guru);
                mysqli_stmt_close($cek);
                mysqli_stmt_close($cek_user);

                header("Location: data_guru.php");
                exit;

            } catch (Exception $e) {

                mysqli_rollback($koneksi);
                $error = "Gagal menyimpan data: " . $e->getMessage();

            }
        }

        mysqli_stmt_close($cek);
        mysqli_stmt_close($cek_user);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tambah Guru</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include 'layout/sidebar.php'; ?>

    <!-- KONTEN -->
    <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

        <h2>Tambah Guru</h2>
        <p class="text-secondary">Tambahkan data guru baru.</p>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">

            <div class="card-header">
                Form Tambah Guru
            </div>

            <div class="card-body">

                <form method="POST" action="">

                    <div class="mb-3">
                        <label class="form-label">NIP</label>
                        <input type="text"
                               name="nip"
                               class="form-control"
                               placeholder="Masukkan NIP"
                               value="<?= htmlspecialchars($_POST['nip'] ?? '') ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Guru</label>
                        <input type="text"
                               name="nama"
                               class="form-control"
                               placeholder="Masukkan nama guru"
                               value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="Masukkan email guru"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password"
                               name="password"
                               class="form-control"
                               placeholder="Masukkan password minimal 6 karakter"
                               minlength="6"
                               required>
                        <div class="form-text">
                            Password akan otomatis dienkripsi sebelum disimpan.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status_aktif" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">

                        <button type="submit" class="btn btn-primary">
                            Simpan Guru
                        </button>

                        <a href="data_guru.php" class="btn btn-secondary">
                            Kembali
                        </a>

                    </div>

                </form>

            </div>
        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>