<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    if (isset($_POST['simpan'])) {

        $namaJenis = $_POST['nama'];

        $qInsert = "
            CALL sp_create_jenisartikel(
                '$namaJenis'
            );
        ";

        pg_query($conn, $qInsert);

        echo "
        <script>
            alert('Jenis artikel berhasil ditambahkan!');
            window.location.href = 'kelola_jenisartikel.php';
        </script>
        ";
        exit;
    }
?>

<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>Tambah Jenis Artikel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
    <div class='container'>
        <h1 class='mb-4 fw-bold text-center'>Tambah Jenis Artikel</h1>

        <div class='card shadow-sm p-4'>
            <form method='POST'>

                <div class='mb-3'>
                    <label class='form-label text-white'>Nama Jenis Artikel</label>
                    <input type='text' name='nama' class='form-control' placeholder="Masukkan nama jenis artikel" required>
                </div>

                <div class='d-flex gap-2 mt-3'>
                    <button type='submit' name='simpan' class='btn btn-primary'>
                        <i class='fa fa-plus'></i> Tambah Jenis
                    </button>
                    <a href='kelola_jenisartikel.php' class='btn btn-secondary'>
                        <i class='fa fa-arrow-left'></i> Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>

<script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js'></script>
</body>
</html>
