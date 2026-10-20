
<?php
session_start();

include 'config/koneksi.php';
include 'includes/cek_session.php';

$error = '';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: data_guru.php');
    exit;
}

// Ambil data guru berdasarkan ID
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, nip, nama, email, status_aktif
     FROM t_guru
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$guru = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$guru) {
    die("Data guru tidak ditemukan.");
}

// Proses simpan perubahan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nip = trim($_POST['nip'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $status = (int) ($_POST['status_aktif'] ?? 1);

    if ($nip === '' || $nama === '' || $email === '') {
        $error = "Semua kolom wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (!in_array($status, [0, 1], true)) {
        $error = "Status tidak valid.";
    } else {

        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_guru
             SET nip = ?,
                 nama = ?,
                 email = ?,
                 status_aktif = ?,
                 updated_at = NOW()
             WHERE id = ?"
        );

        if (!$stmt) {
            $error = "Gagal menyiapkan query: " .
                     mysqli_error($koneksi);
        } else {
            mysqli_stmt_bind_param(
                $stmt,
                "sssii",
                $nip,
                $nama,
                $email,
                $status,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                header('Location: data_guru.php');
                exit;
            } else {
                $error = "Gagal mengubah data: " .
                         mysqli_stmt_error($stmt);

                mysqli_stmt_close($stmt);
            }
        }
    }

    // Pertahankan input jika terjadi kesalahan
    $guru['nip'] = $nip;
    $guru['nama'] = $nama;
    $guru['email'] = $email;
    $guru['status_aktif'] = $status;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Guru</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>

<div class="container py-4">

    <h2>Edit Guru</h2>
    <p>Ubah data guru.</p>

    <div class="card">
        <div class="card-header">
            <strong>Form Edit Guru</strong>
        </div>

        <div class="card-body">

            <?php if ($error !== '') { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php } ?>

            <form method="POST"
                  action="edit_guru.php?id=<?= $id ?>">

                <div class="mb-3">
                    <label class="form-label">NIP</label>
                    <input type="text"
                           name="nip"
                           class="form-control"
                           value="<?= htmlspecialchars($guru['nip'] ?? '') ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Guru</label>
                    <input type="text"
                           name="nama"
                           class="form-control"
                           value="<?= htmlspecialchars($guru['nama'] ?? '') ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           value="<?= htmlspecialchars($guru['email'] ?? '') ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>

                    <select name="status_aktif" class="form-select">
                        <option value="1"
                            <?= $guru['status_aktif'] == 1 ? 'selected' : '' ?>>
                            Aktif
                        </option>

                        <option value="0"
                            <?= $guru['status_aktif'] == 0 ? 'selected' : '' ?>>
                            Tidak Aktif
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Simpan Perubahan
                </button>

                <a href="data_guru.php" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>
    </div>

</div>

</body>
</html>
