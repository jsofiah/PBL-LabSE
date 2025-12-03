<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

if (isset($_POST['simpan'])) {

    $logo = "";

    if (!empty($_FILES['logo']['name'])) {
        $dir = "../img/logocta/";
        if (!is_dir($dir)) mkdir($dir);

        $filename = time() . "_" . basename($_FILES['logo']['name']);
        $target = $dir . $filename;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
            $logo = "img/logocta/" . $filename;
        }
    }

    $q = "CALL sp_create_logocta($1, $2, $3)";
    $params = array($_POST['judul'], $_POST['link'], $logo);

    pg_query_params($conn, $q, $params);

    echo "<script>alert('Logo CTA berhasil ditambahkan!'); window.location='kelola_logoCTA.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Logo CTA</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleForm.css">
</head>
<body class="p-4">
    <?php include 'sidebar.php'; ?>

    <div class="content-area container">
        <h1 class="mb-4 fw-bold text-center">Tambah Logo CTA</h1>

        <div class="card shadow-sm p-4 form-card">

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label text-white">Upload Logo</label>
                    <input type="file" name="logo" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Judul CTA</label>
                    <input type="text" name="judul" class="form-control" 
                    placeholder="Masukkan judul logo cta" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Link CTA</label>
                    <input type="text" name="link" class="form-control" 
                    placeholder="Masukkan link logo cta" required>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <a href="kelola_logoCTA.php" class="btn btn-secondary">Kembali</a>
                </div>

            </form>

        </div>
    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>
