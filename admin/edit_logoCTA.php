<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";
require_once "upload_validator.php";

$id = intval($_GET['id']);

$q = "SELECT * FROM logo_cta WHERE id_logo_cta = $1";
$r = pg_query_params($conn, $q, [$id]);
$data = pg_fetch_assoc($r);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location='kelola_logoCTA.php';</script>";
    exit;
}

if (isset($_POST['update'])) {

    $logoLama = $_POST['logo_lama'];
    $logoBaru = $logoLama;

    if (!empty($_FILES['logo']['name'])) {
        $valid = validateUpload($_FILES['logo'], 2);

        if ($valid !== true) {
            echo "<script>alert('$valid'); history.back();</script>";
            exit;
        }

        $targetDir = "../img/logocta/";
        if (!is_dir($targetDir)) mkdir($targetDir);

        $filename = time() . "_" . basename($_FILES["logo"]["name"]);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFile)) {

            if (!empty($logoLama) && file_exists("../".$logoLama)) {
                unlink("../".$logoLama);
            }

            $logoBaru = "img/logocta/" . $filename;
        }
    }

    $qUpdate = "CALL sp_update_logocta($1, $2, $3, $4)";
    $params  = array(
        $id,
        $_POST['judul'],
        $_POST['link'],
        $logoBaru
    );

    pg_query_params($conn, $qUpdate, $params);

    echo "<script>alert('Logo CTA berhasil diperbarui!'); window.location='kelola_logoCTA.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Logo CTA</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleForm.css">
</head>
<body class="p-4">
    <?php include 'sidebar.php'; ?>

    <div class="content-area container">
        <h1 class="mb-4 fw-bold text-center">Edit Logo CTA</h1>

        <div class="card shadow-sm p-4 form-card">

            <form method="POST" enctype="multipart/form-data">

                <div class="text-center mb-3">
                    <label class="form-label text-white">Logo Saat Ini</label><br>
                    <img src="../<?= $data['url_logo']; ?>" style="max-height:90px; border-radius:5px;">
                    <input type="hidden" name="logo_lama" value="<?= $data['url_logo']; ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Upload Logo Baru</label>
                    <input type="file" name="logo" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Judul CTA</label>
                    <input type="text" name="judul" class="form-control"
                        value="<?= htmlspecialchars($data['judul_cta']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Link CTA</label>
                    <input type="text" name="link" class="form-control"
                        value="<?= htmlspecialchars($data['link_cta']); ?>" required>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="kelola_logoCTA.php" class="btn btn-secondary">Kembali</a>
                </div>

            </form>
        </div>
    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>
