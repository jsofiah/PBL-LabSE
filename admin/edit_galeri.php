<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require '../config.php';

$id = intval($_GET['id']);

$qData = pg_query_params($conn, "SELECT * FROM galeri WHERE id_galeri=$1", [$id]);
$data = pg_fetch_assoc($qData);

if (!$data) {
    die("Data tidak ditemukan!");
}

if (isset($_POST['submit'])) {

    $desk = $_POST['deskripsi_galeri'];
    $gambarLama = $data['url_gambar_galeri'];

    if (!empty($_FILES['gambar']['name'])) {
        $filePath = "../" . $gambarLama;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $folder = "../img/galeri/";
        $namaFile = time() . "_" . basename($_FILES['gambar']['name']);
        $target = $folder . $namaFile;

        move_uploaded_file($_FILES['gambar']['tmp_name'], $target);

        $pathDb = "img/galeri/" . $namaFile;

        $qUpdate = "UPDATE galeri 
                    SET deskripsi_galeri=$1, url_gambar_galeri=$2
                    WHERE id_galeri=$3";

        pg_query_params($conn, $qUpdate, [$desk, $pathDb, $id]);

    } else {

        $qUpdate = "UPDATE galeri 
                    SET deskripsi_galeri=$1
                    WHERE id_galeri=$2";

        pg_query_params($conn, $qUpdate, [$desk, $id]);
    }

    header("Location: kelola_galeri.php");
    exit;
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Edit Galeri</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="container mt-4">

<h1 class="mb-4 fw-bold text-center">Edit Gambar Galeri</h1>

<form method="POST" enctype="multipart/form-data">
    <div class="card shadow-sm p-4">

    <div class="mb-3">
        <label class="form-label text-white">Deskripsi</label>
        <input type="text" name="deskripsi_galeri" class="form-control" placeholder="Masukkan deskripsi"
               value="<?= htmlspecialchars($data['deskripsi_galeri']); ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label text-white">Upload Gambar Baru (opsional)</label>
        <input type="file" name="gambar" class="form-control" accept="image/*">
    </div>
    <div class="d-flex gap-2 mt-3">
    <button class="btn btn-primary" name="submit">Simpan Perbaruan</button>
    <a href="kelola_galeri.php" class="btn btn-secondary">Kembali</a>
    
</form>
</div>
</div>

</body>
</html>