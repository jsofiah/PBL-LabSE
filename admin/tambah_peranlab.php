<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

if (isset($_POST['submit'])) {
    $nama = $_POST['nama_peran'];
    $desk = $_POST['deskripsi_peran'];
    $icon = $_POST['icon'];

    $q = "INSERT INTO peran_lab (nama_peran, deskripsi_peran, icon)
          VALUES ('$nama', '$desk', '$icon')";
    pg_query($conn, $q);

    header("Location: kelola_peranLab.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Peran Lab</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="container mt-4">

<h1 class="mb-4 fw-bold text-center">Tambah Peran Lab</h1>
<form method="POST">
    <div class="card shadow-sm p-4">
    <label class="form-label text-white">Nama Peran:</label>
    <input type="text" name="nama_peran" class="form-control mb-3" required>

    <label class="form-label text-white">Deskripsi Peran:</label>
    <textarea name="deskripsi_peran" class="form-control mb-3" required></textarea>

    <label class="form-label text-white">Icon (text):</label>
    <input type="text" name="icon" class="form-control mb-3">
    <div class="d-flex gap-2 mt-3">
    <button class="btn btn-primary" name="submit">Simpan</button>
    <a href="kelola_peranLab.php" class="btn btn-secondary">Kembali</a>
</div>

</form>
</div>

</body>
</html>
