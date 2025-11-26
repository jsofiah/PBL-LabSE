<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

if (isset($_POST['simpan'])) {

    $logoBaru = "";

    if (!empty($_FILES['logo']['name'])) {
        $targetDir = "../img/logocta/";
        if (!is_dir($targetDir)) mkdir($targetDir);

        $filename = time() . "_" . basename($_FILES["logo"]["name"]);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFile)) {
            $logoBaru = "img/logocta/" . $filename;
        }
    }

    $query = "CALL sp_create_logocta('$_POST[judul]', '$_POST[link]', '$logoBaru');";

    pg_query($conn, $query);

    echo "<script>alert('Logo CTA berhasil ditambahkan!'); window.location='kelola_logoCTA.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
<title>Tambah Logo CTA</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
<div class='container'>
    <h1 class='mb-4 fw-bold text-center'>Tambah Logo CTA</h1>

    <div class='card shadow-sm p-4'>
        <form method='POST' enctype='multipart/form-data'>

            <div class='mb-3'>
                <label class='form-label text-white'>Upload Logo</label>
                <input type='file' name='logo' class='form-control' required>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Judul CTA</label>
                <input type='text' name='judul' class='form-control' required>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Link CTA</label>
                <input type='text' name='link' class='form-control' required>
            </div>

            <button type='submit' name='simpan' class='btn btn-primary mt-3'>
                <i class='fa fa-save'></i> Simpan
            </button>
            <a href='kelola_logoCTA.php' class='btn btn-secondary mt-3'>Kembali</a>
        </form>
    </div>
</div>
</body>
</html>
