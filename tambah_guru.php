
<?php
session_start();
include 'config/koneksi.php';
include 'includes/cek_session.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = trim($_POST['nip'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $user_id = (int) ($_POST['user_id'] ?? 0);

    if ($nip === '' || $nama === '' || $email === '' || $user_id <= 0) {
        $error = 'Semua data wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } else {
        // Periksa apakah NIP sudah digunakan
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_guru WHERE nip = ? OR email = ? LIMIT 1"
        );

        mysqli_stmt_bind_param($cek, "ss", $nip, $email);
        mysqli_stmt_execute($cek);
        $hasil = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil) > 0) {
            $error = 'NIP atau email sudah terdaftar!';
        } else {
            // Simpan data guru
            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO t_guru
                (nip, nama, email, status_aktif, user_id, created_at, updated_at)
                VALUES (?, ?, ?, 1, ?, NOW(), NOW())"
            );

            if ($stmt) {
                mysqli_stmt_bind_param(
                    $stmt,
                    "sssi",
                    $nip,
                    $nama,
                    $email,
                    $user_id
                );

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    mysqli_stmt_close($cek);

                    header('Location: data_guru.php');
                    exit;
                } else {
                    $error = 'Gagal menyimpan: ' . mysqli_stmt_error($stmt);
                    mysqli_stmt_close($stmt);
                }
            } else {
                $error = 'Query gagal: ' . mysqli_error($koneksi);
            }
        }

        mysqli_stmt_close($cek);
    }
}

// Ambil data user untuk pilihan
$query_user = mysqli_query(
    $koneksi,
    "SELECT id, name, email FROM t_users ORDER BY name ASC"
);

if (!$query_user) {
    $error = 'Gagal mengambil data user: ' . mysqli_error($koneksi);
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
<div class="container-fluid">
    <div class="row">

        <div class="col-md-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white mb-4">My Website</h4>

            <ul class="nav nav-pills flex-column gap-2">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link text-white">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="data_siswa.php" class="nav-link text-white">Data Siswa</a>
                </li>
                <li class="nav-item">
                    <a href="data_guru.php" class="nav-link active">Data Guru</a>
                </li>
                <li class="nav-item">
                    <a href="data_kelas.php" class="nav-link text-white">Kelas</a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">Logout</a>
                </li>
            </ul>
        </div>

        <div class="col-md-10 p-4">
            <h2>Tambah Guru</h2>
            <p class="text-secondary">Tambahkan data guru baru</p>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

       
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <h3 class="mb-1">Tambah Guru</h3>
                    <p class="text-secondary mb-4">
                        Tambahkan data guru baru ke dalam sistem
                    </p>

                    <form action="" method="POST">

                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text"
                                   name="nip"
                                   id="nip"
                                   class="form-control"
                                   placeholder="Masukkan NIP"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama Guru
                            </label>
                            <input type="text"
                                   name="nama"
                                   id="nama"
                                   class="form-control"
                                   placeholder="Masukkan nama guru"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control"
                                   placeholder="Masukkan email guru"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="status_aktif" class="form-label">
                                Status
                            </label>
                            <select name="status_aktif"
                                    id="status_aktif"
                                    class="form-select"
                                    required>
                                <option value="1" selected>Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="user_id" class="form-label">
                                User ID
                            </label>
                            <input type="number"
                                   name="user_id"
                                   id="user_id"
                                   class="form-control"
                                   placeholder="Masukkan User ID"
                                   min="1"
                                   required>
                            <div class="form-text">
                                Masukkan ID yang sudah ada pada tabel t_users.
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <a href="data_guru.php"
                               class="btn btn-secondary">
                                Kembali
                            </a>

                            <button type="submit"
                                    name="simpan"
                                    value="1"
                                    class="btn btn-primary">
                                Simpan Guru
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
     
</body>
</html>