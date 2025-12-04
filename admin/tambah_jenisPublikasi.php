<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "../config.php";

if (isset($_POST['simpan'])) {

    $qInsert = "
        CALL sp_create_jenispublikasi(
            '$_POST[nama]'
        );
    ";

    pg_query($conn, $qInsert);

    echo "
        <script>
            alert('Jenis publikasi berhasil ditambahkan!');
            window.location.href = 'kelola_jenispublikasi.php';
        </script>
    ";
    exit;
}
?>

<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>Tambah Jenis Publikasi</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
    <?php include 'sidebar.php'; ?>

    <div class='content-area container'>
        <h1 class='mb-4 fw-bold text-center'>Tambah Jenis Publikasi</h1>

        <div class='card shadow-sm p-4'>
            <form method='POST'>

                <div class='mb-3'>
                    <label class='form-label text-white'>Nama Jenis Publikasi</label>
                    <input type='text' name='nama' class='form-control' placeholder="Masukkan nama jenis publikasi" required>
                </div>

                <div class='d-flex gap-2 mt-3'>
                <button type='submit' name='simpan' class='btn btn-primary'> Tambah </button>
                <a href='kelola_jenisPublikasi.php' class='btn btn-secondary'> Kembali </a>
            </div>

            </form>
        </div>
    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>
