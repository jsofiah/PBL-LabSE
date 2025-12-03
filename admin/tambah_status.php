<?php
session_start();
require_once '../config.php';

if (isset($_POST['submit'])) {
    $nama = $_POST['nama_status'];

    pg_query_params($conn, "CALL sp_create_status($1)", [$nama]);

    header("Location: kelola_statusPendaftaran.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Status</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="p-4">
    <?php include 'sidebar.php'; ?>

    <div class="content-area container">

        <h1 class="mb-4 fw-bold text-center">Tambah Status Pendaftaran</h1>

        <form action="" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label class="form-label text-white">Nama Status</label>
                <input type="text" name="nama_status" class="form-control" placeholder="Masukkan Nama Status" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                <a href="kelola_statusPendaftaran.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
    <script src="js/sidebar.js"></script>
</body>

</html>