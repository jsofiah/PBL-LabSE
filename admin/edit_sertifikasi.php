<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id = $_GET['id'] ?? 0;

    $q = "SELECT * FROM vw_sertifikasi WHERE id_sertifikasi = $id";
    $r = pg_query($conn, $q);
    $data = pg_fetch_assoc($r);

    if (!$data) {
        echo "<script>
            alert('Sertifikasi tidak ditemukan!');
            window.location.href='kelola_sertifikasi.php';
        </script>";
        exit;
    }

    if (isset($_POST['simpan'])) {
        $nama  = $_POST['nama_sertifikasi'];
        $peny  = $_POST['penyelenggara'];
        $tahun = $_POST['tahun_sertifikasi'];

        $qUpdate = "
            CALL sp_update_sertifikasi(
                $id,
                '$nama',
                '$peny',
                '$tahun'
            );
        ";

        pg_query($conn, $qUpdate);

        echo "<script>
            alert('Sertifikasi berhasil diperbarui!');
            window.location.href='kelola_sertifikasi.php';
        </script>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
<title>Edit Sertifikasi</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel='stylesheet' href='css/styleForm.css'>
<link rel="stylesheet" href="css/styleSidebar.css">
</head>

<body class='p-4'>
    <?php include 'sidebar.php'; ?>

<div class='content-area container'>
    <h1 class='mb-4 fw-bold text-center'>Edit Sertifikasi</h1>
    <div class='card shadow-sm p-4'>
        <form method='POST'>

            <div class='mb-3'>
                <label class='text-white'>Nama Sertifikasi</label>
                <input type='text' name='nama_sertifikasi' value='<?= $data['nama_sertifikasi'] ?>' class='form-control' placeholder="Masukkan nama sertifikasi"  required>
            </div>

            <div class='mb-3'>
                <label class='text-white'>Penyelenggara</label>
                <input type='text' name='penyelenggara' value='<?= $data['penyelenggara'] ?>' class='form-control' placeholder="Masukkan nama penyelenggara" required>
            </div>

            <div class='mb-3'>
                <label class='text-white'>Tahun Sertifikasi</label>
                <input type='number' min='1900' max='2100' name='tahun_sertifikasi' placeholder="Masukkan tahun sertifikasi" value='<?= $data['tahun_sertifikasi'] ?>' class='form-control' required>
            </div>

            <button type='submit' name='simpan' class='btn btn-primary'>Simpan Perubahan</button>
            <a href='kelola_sertifikasi.php' class='btn btn-secondary'>Kembali</a>

        </form>
    </div>
</div>
<script src="js/sidebar.js"></script>
</body>
</html>