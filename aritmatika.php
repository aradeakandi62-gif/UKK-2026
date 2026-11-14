
<?php
// Nilai 1 dan Nilai 2
$nilai1 = 10;
$nilai2 = 5;

// Perhitungan pertambahan
$pertambahan = $nilai1 + $nilai2;

// Perhitungan pengurangan
$pengurangan = $nilai1 - $nilai2;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Operasi Aritmatika</title>
</head>
<body>

    <h2>Program Aritmatika PHP</h2>

    <p>Nilai 1 = <?= $nilai1; ?></p>
    <p>Nilai 2 = <?= $nilai2; ?></p>

    <hr>

    <h3>Hasil Pertambahan</h3>
    <p>
        <?= $nilai1; ?> + <?= $nilai2; ?> = <?= $pertambahan; ?>
    </p>

    <h3>Hasil Pengurangan</h3>
    <p>
        <?= $nilai1; ?> - <?= $nilai2; ?> = <?= $pengurangan; ?>
    </p>

</body>
</html>
