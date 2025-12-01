<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

if (isset($_POST['submit'])) {
    $nama = $_POST['nama_jenisfasilitas'];

    $qInsert = "INSERT INTO jenis_fasilitas (nama_jenisfasilitas) VALUES ($1)";
    $result = pg_query_params($conn, $qInsert, [$nama]);

    if ($result) {
        header("Location: kelola_jenisFasilitas.php");
        exit;
    } else {
        echo "Gagal menambahkan jenis fasilitas!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Jenis Fasilitas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="container mt-4">

<h1 class="mb-4 fw-bold text-center">Tambah Jenis Fasilitas</h1>

<form method="POST">
    <div class="card shadow-sm p-4">
    <div class="mb-3">
        <label class="form-label text-white">Nama Jenis Fasilitas</label>
        <input type="text" name="nama_jenisfasilitas" class="form-control" required>
    </div>
    <div class="d-flex gap-2 mt-3">
    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
    <a href="kelola_jenisFasilitas.php" class="btn btn-secondary">Kembali</a>
</div>
</form>
</div>


</body>
</html>
