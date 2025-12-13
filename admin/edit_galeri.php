<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require '../config.php';
require_once "upload_validator.php";

$id = $_GET['id'];


$qData = pg_query_params($conn, "SELECT * FROM vw_galeri WHERE id_galeri=$1", array($id));
$data = pg_fetch_assoc($qData);

if (isset($_POST['submit'])) {

    $desk = $_POST['deskripsi_galeri'];
    $newPath = $data['url_gambar_galeri'];

    if (!empty($_FILES['gambar']['name'])) {
        $valid = validateUpload($_FILES['gambar'], 2);

        if ($valid !== true) {
            echo "<script>alert('$valid'); history.back();</script>";
            exit;
        }

        $folder = "../img/galeri/";
        $namaFile = time() . "_" . basename($_FILES['gambar']['name']);
        $target = $folder . $namaFile;

        move_uploaded_file($_FILES['gambar']['tmp_name'], $target);

        $newPath = "img/galeri/" . $namaFile;
    }

    $query = "CALL sp_update_galeri($1, $2, $3)";
    $params = array($id, $desk, $newPath);

    $res = pg_query_params($conn, $query, $params);

    if ($res) {
        echo "<script>alert('Galeri berhasil diperbarui!');
              window.location='kelola_galeri.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui galeri!');</script>";
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Edit Galeri</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleForm.css">
    <link rel="stylesheet" href="css/styleSidebar.css">
</head>

<body class="p-4">
    <?php include 'sidebar.php'; ?>

    <div class="content-area container">
        <h1 class="mb-4 fw-bold text-center">Edit Gambar Galeri</h1>

        <form method="POST" enctype="multipart/form-data">
            <div class="card shadow-sm p-4">

                <div class="mb-3">
                    <label class="form-label text-white">Deskripsi</label>
                    <input type="text" name="deskripsi_galeri" placeholder="Masukkan deskripsi gambar" class="form-control"
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
                    <button class="btn btn-primary" name="submit">Simpan Perubahan</button>
                    <a href="kelola_galeri.php" class="btn btn-secondary">Kembali</a>

        </form>
    </div>
    </div>

    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>