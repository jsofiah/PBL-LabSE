<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once '../config.php';

$id = $_GET['id'];
$q = "SELECT * FROM galeri WHERE id_galeri = $id";
$r = pg_query($conn, $q);
$data = pg_fetch_assoc($r);

if (isset($_POST['submit'])) {
    $desk = $_POST['deskripsi_galeri'];
    $url = $_POST['url_gambar_galeri'];

    $update = "UPDATE galeri 
               SET deskripsi_galeri='$desk', url_gambar_galeri='$url' 
               WHERE id_galeri=$id";

    pg_query($conn, $update);
    header("Location: kelola_galeri.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Galeri</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h3>Edit Galeri</h3>

<form method="POST">

    <label>Deskripsi Galeri:</label>
    <input type="text" name="deskripsi_galeri" 
           value="<?= $data['deskripsi_galeri']; ?>" 
           class="form-control mb-3" required>

    <label>URL Gambar:</label>
    <input type="text" name="url_gambar_galeri" 
           value="<?= $data['url_gambar_galeri']; ?>" 
           class="form-control mb-3" required>

    <button class="btn btn-warning" name="submit">Update</button>
    <a href="kelola_galeri.php" class="btn btn-secondary">Kembali</a>

</form>

</body>
</html>
