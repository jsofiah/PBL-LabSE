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
</head>

<body class="container mt-4">

<h3>Edit Peran Lab</h3>

<form method="POST">

    <label>Nama Peran:</label>
    <input type="text" name="nama_peran" value="<?= $data['nama_peran'] ?>" class="form-control mb-3" required>

    <label>Deskripsi Peran:</label>
    <textarea name="deskripsi_peran" class="form-control mb-3" required><?= $data['deskripsi_peran'] ?></textarea>

    <label>Icon (text):</label>
    <input type="text" name="icon" value="<?= $data['icon'] ?>" class="form-control mb-3">

    <button class="btn btn-warning" name="submit">Update</button>
    <a href="kelola_peranLab.php" class="btn btn-secondary">Kembali</a>

</form>

</body>
</html>
