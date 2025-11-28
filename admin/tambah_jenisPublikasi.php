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
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
    <div class='container'>
        <h1 class='mb-4 fw-bold text-center'>Tambah Jenis Publikasi</h1>

        <div class='card shadow-sm p-4'>
            <form method='POST'>

                <div class='mb-3'>
                    <label class='form-label text-white'>Nama Jenis Publikasi</label>
                    <input type='text' name='nama' class='form-control' placeholder="Masukkan nama jenis publikasi" required>
                </div>

                <div class='d-flex gap-2 mt-3'>
                    <button type='submit' name='simpan' class='btn btn-primary'>
                        <i class='fa fa-plus'></i> Tambah
                    </button>
                    <a href='kelola_jenispublikasi.php' class='btn btn-secondary'>
                        <i class='fa fa-arrow-left'></i> Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>
</body>
</html>
