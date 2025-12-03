<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

if (isset($_POST['submit'])) {
    $nama = $_POST['nama_jenisfasilitas'];

    $qInsert = "CALL sp_create_jenis_fasilitas($1)";
    $result = pg_query_params($conn, $qInsert, [$nama]);

    if ($result) {
        echo "
        <script>
            alert('Jenis Fasilitas baru berhasil ditambahkan!');
            window.location.href = 'kelola_jenisFasilitas.php';
        </script>
        ";
        exit;
    } else {
        echo "
        <script>
            alert('Gagal menambahkan jenis fasilitas! Error: " . pg_last_error($conn) . "');
            window.location.href = 'kelola_jenisFasilitas.php'; // Atau tetap di halaman ini jika ingin menampilkan form lagi
        </script>
        ";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Jenis Fasilitas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="content-area container">
    <?php include 'sidebar.php'; ?>

<h1 class="mb-4 fw-bold text-center">Tambah Jenis Fasilitas</h1>

<form method="POST">
    <div class="card shadow-sm p-4">
    <div class="mb-3">
        <label class="form-label text-white">Nama Jenis Fasilitas</label>
        <input type="text" name="nama_jenisfasilitas" placeholder="Masukkan jenis fasilitas" class="form-control" required>
    </div>
    <div class="d-flex gap-2 mt-3">
    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
    <a href="kelola_jenisFasilitas.php" class="btn btn-secondary">Kembali</a>
</div>
</form>
</div>

<script src="js/sidebar.js"></script>
</body>
</html>