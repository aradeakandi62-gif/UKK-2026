
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$error = '';

$nama = '';
$tingkat = '';
$jurusan = '';
$status_aktif = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $tingkat = trim($_POST['tingkat'] ?? '');
    $jurusan = strtoupper(trim($_POST['jurusan'] ?? ''));

    if ($nama === '' || $tingkat === '' || $jurusan === '') {
        $error = "Semua data wajib diisi.";
    } elseif (!in_array($tingkat, ['X', 'XI', 'XII'], true)) {
        $error = "Tingkat kelas tidak valid.";
    } else {
        $stmt = $koneksi->prepare("
            INSERT INTO t_kelas
                (nama, tingkat, jurusan, status_aktif)
            VALUES (?, ?, ?, ?)
        ");

        if (!$stmt) {
            $error = "Gagal menyiapkan query: " . $koneksi->error;
        } else {
            $stmt->bind_param(
                "sssi",
                $nama,
                $tingkat,
                $jurusan,
                $status_aktif
            );

            if ($stmt->execute()) {
                $stmt->close();
                $_SESSION['pesan_sukses'] = "Data kelas berhasil ditambahkan.";
                header("Location: data_kelas.php");
                exit;
            } else {
                $error = "Data gagal disimpan: " . $stmt->error;
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <div class="container-fluid">

                <h2 class="mb-1">Tambah Kelas</h2>
                <p class="text-secondary mb-4">Tambahkan data kelas baru.</p>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm">

                    <div class="card-header fw-bold">
                        Form Tambah Kelas
                    </div>

                    <div class="card-body">

                        <form method="POST" action="">

                            <!-- NAMA KELAS -->
                            <div class="mb-3">
                                <label class="form-label">Nama Kelas</label>
                                <input
                                    type="text"
                                    name="nama"
                                    class="form-control"
                                    placeholder="Contoh: XII TKR 3"
                                    value="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?>"
                                    required>
                            </div>

                            <!-- TINGKAT -->
                            <div class="mb-3">
                                <label class="form-label">Tingkat Kelas</label>
                                <select name="tingkat" class="form-select" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    <option value="X" <?= $tingkat === 'X' ? 'selected' : ''; ?>>X</option>
                                    <option value="XI" <?= $tingkat === 'XI' ? 'selected' : ''; ?>>XI</option>
                                    <option value="XII" <?= $tingkat === 'XII' ? 'selected' : ''; ?>>XII</option>
                                </select>
                            </div>

                            <!-- JURUSAN -->
                            <div class="mb-3">
                                <label class="form-label">Jurusan</label>
                                <input
                                    type="text"
                                    name="jurusan"
                                    class="form-control"
                                    placeholder="Contoh: RPL, TKR, TKJ"
                                    value="<?= htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8'); ?>"
                                    required>
                                <div class="form-text">
                                    Isi sesuai jurusan kelas, misalnya RPL atau TKR.
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Simpan Kelas
                            </button>

                            <a href="data_kelas.php" class="btn btn-secondary">
                                Kembali
                            </a>

                        </form>

                    </div>
                </div>

            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
