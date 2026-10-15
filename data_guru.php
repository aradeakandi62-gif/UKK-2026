<?php
include 'config/koneksi.php';

// Ambil data guru
$query = mysqli_query($koneksi, "SELECT * FROM t_guru");
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Guru</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body>

<div class="d-flex">

     <?php
     include "layout/sidebar.php";
     ?>

    <!-- CONTENT -->
    <div class="flex-grow-1 p-4">

        <h2>Data Guru</h2>

        <p class="text-muted">
            Daftar data guru
        </p>


        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Data Guru
                </strong>

                <a href="tambah_guru.php"
                   class="btn btn-primary">
                    + Tambah Guru
                </a>

            </div>


            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-striped">

                        <thead class="table-dark">

                            <tr>

                                <th>No</th>

                                <th>NIP</th>

                                <th>Nama Guru</th>

                                <th>Jenis Kelamin</th>

                                <th>Aksi</th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php

                        $no = 1;

                        while ($data = mysqli_fetch_assoc($query)) {

                        ?>

                            <tr>

                                <td>
                                    <?= $no++; ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['nip']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['nama']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['jenis_kelamin']); ?>
                                </td>

                                <td>

                                    <a href="edit_guru.php?id=<?= $data['id']; ?>"
                                       class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <a href="hapus_guru.php?id=<?= $data['id']; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus data guru ini?')">
                                        Hapus
                                    </a>

                                </td>

                            </tr>

                        <?php

                        }

                        ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>