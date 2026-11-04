
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$error = '';
$success = '';

// Mengambil ID penempatan
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    die("ID penempatan tidak valid.");
}

// Mengambil data penempatan berdasarkan ID
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_kelas_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    die("Data penempatan siswa tidak ditemukan.");
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $siswa_id = (int) ($_POST['siswa_id'] ?? 0);
    $kelas_id = (int) ($_POST['kelas_id'] ?? 0);
    $tahun_ajaran_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);

    if ($siswa_id <= 0 || $kelas_id <= 0 || $tahun_ajaran_id <= 0) {

        $error = "Semua pilihan wajib diisi.";

    } else {

        // Cek agar siswa tidak memiliki penempatan ganda
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_kelas_siswa
             WHERE siswa_id = ?
             AND tahun_ajaran_id = ?
             AND id != ?"
        );

        mysqli_stmt_bind_param(
            $cek,
            "iii",
            $siswa_id,
            $tahun_ajaran_id,
            $id
        );

        mysqli_stmt_execute($cek);
        $cek_result = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($cek_result) > 0) {

            $error = "Siswa sudah ditempatkan pada tahun ajaran tersebut.";

        } else {

            $update = mysqli_prepare(
                $koneksi,
                "UPDATE t_kelas_siswa
                 SET siswa_id = ?,
                     kelas_id = ?,
                     tahun_ajaran_id = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update,
                "iiii",
                $siswa_id,
                $kelas_id,
                $tahun_ajaran_id,
                $id
            );

            if (mysqli_stmt_execute($update)) {

                mysqli_stmt_close($update);
                mysqli_stmt_close($cek);

                header("Location: penempatan_siswa.php");
                exit;

            } else {

                $error = "Gagal mengubah data: " . mysqli_stmt_error($update);
                mysqli_stmt_close($update);

            }
        }

        mysqli_stmt_close($cek);
    }

    // Pertahankan pilihan yang dikirim
    $data['siswa_id'] = $siswa_id;
    $data['kelas_id'] = $kelas_id;
    $data['tahun_ajaran_id'] = $tahun_ajaran_id;
}

// Mengambil data siswa
$siswa = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama FROM t_siswa ORDER BY nama ASC"
);

// Mengambil data kelas
$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_kelas ORDER BY nama ASC"
);

// Mengambil data tahun ajaran
$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Penempatan Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <h2 class="mb-1">Edit Penempatan Siswa</h2>

            <p class="text-muted">
                Ubah data penempatan siswa berdasarkan kelas dan tahun ajaran.
            </p>

            <div class="card shadow-sm">

                <div class="card-header bg-light fw-bold">
                    Form Edit Penempatan Siswa
                </div>

                <div class="card-body">

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST"
                          action="edit_penempatan.php?id=<?= $id ?>">

                        <input type="hidden" name="id" value="<?= $id ?>">

                        <!-- SISWA -->
                        <div class="mb-3">

                            <label class="form-label">Siswa</label>

                            <select name="siswa_id" class="form-select" required>

                                <option value="">-- Pilih Siswa --</option>

                                <?php while ($row = mysqli_fetch_assoc($siswa)): ?>

                                    <option
                                        value="<?= (int) $row['id'] ?>"
                                        <?= ($data['siswa_id'] == $row['id']) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($row['nis'] . ' - ' . $row['nama'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>

                                <?php endwhile; ?>

                            </select>

                        </div>

                        <!-- KELAS -->
                        <div class="mb-3">

                            <label class="form-label">Kelas</label>

                            <select name="kelas_id" class="form-select" required>

                                <option value="">-- Pilih Kelas --</option>

                                <?php while ($row = mysqli_fetch_assoc($kelas)): ?>

                                    <option
                                        value="<?= (int) $row['id'] ?>"
                                        <?= ($data['kelas_id'] == $row['id']) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>

                                <?php endwhile; ?>

                            </select>

                        </div>

                        <!-- TAHUN AJARAN -->
                        <div class="mb-3">

                            <label class="form-label">Tahun Ajaran</label>

                            <select name="tahun_ajaran_id" class="form-select" required>

                                <option value="">-- Pilih Tahun Ajaran --</option>

                                <?php while ($row = mysqli_fetch_assoc($tahun)): ?>

                                    <option
                                        value="<?= (int) $row['id'] ?>"
                                        <?= ($data['tahun_ajaran_id'] == $row['id']) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>

                                <?php endwhile; ?>

                            </select>

                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>

                        <a href="penempatan_siswa.php" class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>

            </div>

        </main>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
