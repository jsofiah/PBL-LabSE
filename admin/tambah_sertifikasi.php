<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    if (isset($_POST['simpan'])) {
        $nama  = $_POST['nama_sertifikasi'];
        $peny  = $_POST['penyelenggara'];
        $tahun = $_POST['tahun_sertifikasi'];

        $q = "
            CALL sp_create_sertifikasi(
                '$nama',
                '$peny',
                '$tahun'
            );
        ";

        pg_query($conn, $q);

        echo "
        <script>
            alert('Sertifikasi baru berhasil ditambahkan!');
            window.location.href='kelola_sertifikasi.php';
        </script>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
<title>Tambah Sertifikasi</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleSidebar.css">
<link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
    <?php include 'sidebar.php'; ?>

<div class='content-area container'>
    <h2 class='mb-4 fw-bold text-center'>Tambah Sertifikasi</h2>
    <div class='card shadow-sm p-4'>
        <form method='POST'>

            <div class='mb-3'>
                <label class='text-white'>Nama Sertifikasi</label>
                <input type='text' name='nama_sertifikasi' class='form-control' placeholder="Masukkan nama sertifikasi" required>
            </div>

            <div class='mb-3'>
                <label class='text-white'>Penyelenggara</label>
                <input type='text' name='penyelenggara' class='form-control' placeholder="Masukkan nama penyelenggara" required>
            </div>

            <div class='mb-3'>
                <label class='text-white'>Tahun Sertifikasi</label>
                <input type='number' min='1900' max='2100' name='tahun_sertifikasi' class='form-control' placeholder="Masukkan tahun sertifikasi" required>
            </div>

            <button type='submit' name='simpan' class='btn btn-primary'>
                <i class='fa fa-plus'></i> Tambah
            </button>
            <a href='kelola_sertifikasi.php' class='btn btn-secondary'>
                <i class='fa fa-arrow-left'></i> Kembali
            </a>

        </form>
    </div>
</div>
<script src="js/sidebar.js"></script>
</body>
</html>
