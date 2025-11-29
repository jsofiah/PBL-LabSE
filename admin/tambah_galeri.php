<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once '../config.php';

if (isset($_POST['submit'])) {

    $desk = $_POST['deskripsi_galeri'];

    // HANDLE UPLOAD
    $folder = "../img/galeri/";
    $namaFile = time() . "_" . basename($_FILES['gambar']['name']);
    $target = $folder . $namaFile;

    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {

        // simpan relative path KE DATABASE
        $pathDb = "img/galeri/" . $namaFile;

        $q = "INSERT INTO galeri(deskripsi_galeri, url_gambar_galeri)
              VALUES ('$desk', '$pathDb')";

        pg_query($conn, $q);

        header("Location: kelola_galeri.php");
        exit;
    } else {
        echo "Upload gagal.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Galeri</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h3>Tambah Gambar Galeri</h3>
<form method="POST" enctype="multipart/form-data">

    <div class="mb-3">
        <label>Deskripsi</label>
        <input type="text" name="deskripsi_galeri" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Upload Gambar</label>
        <input type="file" name="gambar" class="form-control" accept="image/*" required>
    </div>

    <button class="btn btn-primary" name="submit">Simpan</button>
</form>

</body>
</html>
