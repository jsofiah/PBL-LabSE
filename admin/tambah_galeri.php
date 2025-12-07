<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require '../config.php';
require_once "upload_validator.php";

if (isset($_POST['submit'])) {
    $valid = validateUpload($_FILES['gambar'], 2);

    if ($valid !== true) {
        echo "<script>alert('$valid'); history.back();</script>";
        exit;
    }

    $desk = $_POST['deskripsi_galeri'];

    $folder = "../img/galeri/";
    $namaFile = time() . "_" . basename($_FILES['gambar']['name']);
    $target = $folder . $namaFile;

    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {

        $pathDb = "img/galeri/" . $namaFile;

        $query = "CALL sp_create_galeri($1, $2)";
        $params = array($desk, $pathDb);

        $res = pg_query_params($conn, $query, $params);

        if ($res) {
            echo "<script>alert('Gambar berhasil ditambahkan!'); 
                  window.location='kelola_galeri.php';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal menambahkan galeri.');</script>";
        }

    } else {
        echo "<script>alert('Upload gambar gagal!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Galeri</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class="p-4"> <?php include 'sidebar.php'; ?>

    <div class="content-area container">

        <h1 class="mb-4 fw-bold text-center">Tambah Gambar Galeri</h1>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="card shadow-sm p-4">
                
                <div class="mb-3">
                    <label class="form-label text-white">Deskripsi</label>
                    <input type="text" name="deskripsi_galeri" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Upload Gambar</label>
                    <input type="file" name="gambar" class="form-control" accept="image/*" required>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary" name="submit">Simpan</button>
                    <a href="kelola_galeri.php" class="btn btn-secondary">Kembali</a>
                </div> </div> </form> </div> <script src="js/sidebar.js"></script>
</body>
</html>