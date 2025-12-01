<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$id = $_GET['id'];
$q = "SELECT * FROM peran_lab WHERE id_peran = $id";
$r = pg_query($conn, $q);
$data = pg_fetch_assoc($r);

if (isset($_POST['submit'])) {
    $nama = $_POST['nama_peran'];
    $desk = $_POST['deskripsi_peran'];
    $icon = $_POST['icon'];

    $update = "UPDATE peran_lab 
               SET nama_peran='$nama', deskripsi_peran='$desk', icon='$icon'
               WHERE id_peran=$id";

    pg_query($conn, $update);

    header("Location: kelola_peranLab.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Peran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="container mt-4">

<h1 class="mb-4 fw-bold text-center">Edit Peran Lab</h1>

<form method="POST">
<div class="card shadow-sm p-4">
    <label class="form-label text-white">Nama Peran:</label>
    <input type="text" name="nama_peran" value="<?= $data['nama_peran'] ?>" class="form-control mb-3" required>

    <label class="form-label text-white">Deskripsi Peran:</label>
    <textarea name="deskripsi_peran" class="form-control mb-3" required><?= $data['deskripsi_peran'] ?></textarea>

    <label class="form-label text-white">Icon (text):</label>
    <input type="text" name="icon" value="<?= $data['icon'] ?>" class="form-control mb-3">
    <div class="d-flex gap-2 mt-3">
    <button class="btn btn-primary" name="submit">Update</button>
    <a href="kelola_peranLab.php" class="btn btn-secondary">Kembali</a>
</div>

</form>

</body>
</html>
