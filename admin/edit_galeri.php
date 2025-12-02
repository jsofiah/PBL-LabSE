<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require '../config.php';

if (!isset($_GET['id'])) {
    header("Location: kelola_galeri.php");
    exit;
}

$id = $_GET['id'];

$qData = pg_query_params($conn, "SELECT * FROM galeri WHERE id_galeri=$1", [$id]);
$data = pg_fetch_assoc($qData);

if (!$data) {
    echo "Data galeri tidak ditemukan.";
    exit;
}

if (isset($_POST['submit'])) {
    $desk = $_POST['deskripsi_galeri'];
    $gambarLama = $data['url_gambar_galeri']; 
    $pathDb = $gambarLama;

    if (!empty($_FILES['gambar']['name'])) {
        
        $folder = "../img/galeri/";
        
        
        $namaFile = time() . "_" . basename($_FILES['gambar']['name']);
        $target = $folder . $namaFile;
        
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
            
            $pathDb = "img/galeri/" . $namaFile;
            
            if (!empty($gambarLama)) {
                $pathLama = '../' . $gambarLama; 
                
                if (file_exists($pathLama)) {
                    unlink($pathLama); 
                }
            }
        } else {
             echo "<script>alert('Gagal mengunggah gambar baru!');</script>";
        }
    }
    
    $qUpdate = "UPDATE galeri 
                SET deskripsi_galeri=$1, url_gambar_galeri=$2
                WHERE id_galeri=$3";
    
    $result = pg_query_params($conn, $qUpdate, [$desk, $pathDb, $id]);

    if ($result) {
        header("Location: kelola_galeri.php");
        exit;
    } else {
        echo "Gagal memperbarui data galeri di database.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Galeri</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="container mt-4">

<h1 class="mb-4 fw-bold text-center">Edit Gambar Galeri</h1>

<form method="POST" enctype="multipart/form-data">
    <div class="card shadow-sm p-4">

    <div class="mb-3">
        <label class="form-label text-white">Deskripsi</label>
        <input type="text" name="deskripsi_galeri" class="form-control"
               value="<?= htmlspecialchars($data['deskripsi_galeri']); ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label text-white">Gambar Saat Ini</label><br>
        <img src="../<?= $data['url_gambar_galeri']; ?>" style="width:150px;">
    </div>

    <div class="mb-3">
        <label class="form-label text-white">Upload Gambar Baru (opsional)</label>
        <input type="file" name="gambar" class="form-control" accept="image/*">
    </div>
    <div class="d-flex gap-2 mt-3">
    <button class="btn btn-primary" name="submit">Update</button>
    <a href="kelola_galeri.php" class="btn btn-secondary">Kembali</a>
    
</form>
</div>
</div>

</body>
</html>
