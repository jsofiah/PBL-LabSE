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
    <link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="container mt-4">

<h1 class="mb-4 fw-bold text-center">Tambah Gambar Galeri</h1>
<form method="POST" enctype="multipart/form-data">
    <div class="card shadow-sm p-4">
    <div class="mb-3">
        <label class="form-label text-white">Deskripsi</label>
        <input type="text" name="deskripsi_galeri" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label text-white">Upload Gambar</label>
        <input type="file" name="gambar" class="form-control" accept="image/*" required>
    </div>
    <div class="d-flex gap-2 mt-3">
    <button class="btn btn-primary" name="submit">Simpan</button>
    <a href="kelola_galeri.php" class="btn btn-secondary">Kembali</a>
</form>
</div>
</div>

</body>
</html>
