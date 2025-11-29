<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once '../config.php';

if (isset($_POST['submit'])) {
    $desk = $_POST['deskripsi_galeri'];
    $url = $_POST['url_gambar_galeri'];

    $q = "INSERT INTO galeri (deskripsi_galeri, url_gambar_galeri)
          VALUES ('$desk', '$url')";
    pg_query($conn, $q);

    header("Location: kelola_galeri.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Galeri</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h3>Tambah Galeri</h3>
<form method="POST">

    <label>Deskripsi Galeri:</label>
    <input type="text" name="deskripsi_galeri" class="form-control mb-3" required>

    <label>URL Gambar:</label>
    <input type="text" name="url_gambar_galeri" class="form-control mb-3" required>

    <button class="btn btn-success" name="submit">Simpan</button>
    <a href="kelola_galeri.php" class="btn btn-secondary">Kembali</a>

</form>

</body>
</html>
