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

    $q = "CALL sp_create_peran_lab('$nama', '$desk', '$icon')";
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="content-area container">
    <?php include 'sidebar.php'; ?>

<h1 class="mb-4 fw-bold text-center">Tambah Peran Lab</h1>
<form method="POST">
    <div class="card shadow-sm p-4">
    <label class="form-label text-white">Nama Peran</label>
    <input type="text" name="nama_peran" class="form-control mb-3" placeholder="Masukkan nama peran" required>

    <label class="form-label text-white">Deskripsi Peran</label>
    <textarea name="deskripsi_peran" class="form-control mb-3" placeholder="Masukkan deskripsi" required></textarea>

    <label class="form-label text-white">Icon (text)</label>
    <input type="text" name="icon" class="form-control mb-3" placeholder="Masukkan icon">
    <div class="d-flex gap-2 mt-3">
    <button class="btn btn-primary" name="submit">Simpan</button>
    <a href="kelola_peranLab.php" class="btn btn-secondary">Kembali</a>
</div>

</form>
</div>

<script src="js/sidebar.js"></script>
</body>
</html>
