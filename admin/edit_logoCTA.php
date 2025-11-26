<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

$id = $_GET['id'] ?? 0;

$q = "SELECT * FROM logo_cta WHERE id_logo_cta = $id";
$r = pg_query($conn, $q);
$data = pg_fetch_assoc($r);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location='kelola_logoCTA.php';</script>";
    exit;
}

if (isset($_POST['update'])) {

    $logoLama = $_POST['logo_lama'];
    $logoBaru = $logoLama;

    if (!empty($_FILES['logo']['name'])) {
        $targetDir = "../img/logocta/";
        if (!is_dir($targetDir)) mkdir($targetDir);

        $filename = time() . "_" . basename($_FILES["logo"]["name"]);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFile)) {
            if (!empty($logoLama) && file_exists("../" . $logoLama)) {
                unlink("../" . $logoLama);
            }
            $logoBaru = "img/logocta/" . $filename;
        }
    }

    $qUpdate = "
    CALL sp_update_logocta(
        $id,
        '$_POST[judul]',
        '$_POST[link]',
        '$logoBaru'
    );
    ";
    
    pg_query($conn, $qUpdate);

    echo "<script>alert('Data Logo CTA berhasil diperbarui!'); window.location='kelola_logoCTA.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
<title>Edit Logo CTA</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
<div class='container'>
    <h1 class='mb-4 fw-bold text-center'>Edit Logo CTA</h1>

    <div class='card p-4 shadow-sm'>
        <form method='POST' enctype='multipart/form-data'>

            <div class='text-center mb-3'>
                <label class='form-label text-white'>Logo Saat Ini</label><br>
                <img src="../<?= $data['url_logo']; ?>" style="max-height:80px;">
                <input type="hidden" name="logo_lama" value="<?= $data['url_logo']; ?>">
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Upload Logo Baru</label>
                <input type='file' name='logo' class='form-control'>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Judul CTA</label>
                <input type='text' name='judul' class='form-control'
                       value="<?= $data['judul_cta']; ?>" required>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Link CTA</label>
                <input type='text' name='link' class='form-control'
                       value="<?= $data['link_cta']; ?>" required>
            </div>

            <button type='submit' name='update' class='btn btn-primary mt-3'>Update</button>
            <a href='kelola_logoCTA.php' class='btn btn-secondary mt-3'>Kembali</a>

        </form>
    </div>
</div>
</body>
</html>