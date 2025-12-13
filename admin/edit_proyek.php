<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require '../config.php';
require_once "upload_validator.php";

$id = intval($_GET['id']);

$q = "SELECT * FROM proyek WHERE id_proyek = $1";
$r = pg_query_params($conn, $q, [$id]);
$data = pg_fetch_assoc($r);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='kelola_proyek.php';</script>";
    exit;
}

if (isset($_POST['update'])) {

    $judul   = pg_escape_string($conn, $_POST['judul_proyek']);
    $isi     = pg_escape_string($conn, $_POST['isi_proyek']);
    $tanggal = $_POST['tanggal_terbit_proyek'];
    $penulis = pg_escape_string($conn, $_POST['penulis_proyek']);

    $folder = "../img/proyek/";
    if (!file_exists($folder)) mkdir($folder, 0777, true);

    $inputs = ['gambar1', 'gambar2', 'gambar3'];

    foreach ($inputs as $inp) {
        if (!empty($_FILES[$inp]['name'])) {
            $valid = validateUpload($_FILES[$inp], 2);
            if ($valid !== true) {
                echo "<script>alert('$valid'); history.back();</script>";
                exit;
            }
        }
    }

    function updateFile($input, $folder, $oldFile)
    {
        if (!empty($_FILES[$input]['name'])) {

            if ($_FILES[$input]['error'] !== 0) {
                return $oldFile;
            }

            $cleanName = preg_replace('/[^A-Za-z0-9.\-_]/', '', basename($_FILES[$input]["name"]));
            $newName = time() . "_" . $cleanName;

            $path = $folder . $newName;

            if (move_uploaded_file($_FILES[$input]["tmp_name"], $path)) {

                if (!empty($oldFile)) {
                    $lama = "../" . $oldFile;
                    if (file_exists($lama)) unlink($lama);
                }

                return "img/proyek/" . $newName;
            }
        }

        return $oldFile;
    }

    $g1 = updateFile("gambar1", $folder, $data['url_gambar_proyek1']);
    $g2 = updateFile("gambar2", $folder, $data['url_gambar_proyek2']);
    $g3 = updateFile("gambar3", $folder, $data['url_gambar_proyek3']);

    $qUpdate = "CALL sp_update_proyek($1,$2,$3,$4,$5,$6,$7,$8)";
    $params  = array($id, $judul, $isi, $tanggal, $penulis, $g1, $g2, $g3);

    $res = pg_query_params($conn, $qUpdate, $params);

    if ($res) {
        echo "<script>alert('Proyek berhasil diperbarui!'); window.location='kelola_proyek.php';</script>";
        exit;
    } else {
        die("PG ERROR: " . pg_last_error($conn));
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Proyek</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="css/styleForm.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleSidebar.css">
</head>

<body class="p-4">
    <?php include 'sidebar.php'; ?>

<div class="content-area container">
    <h1 class="mb-4 fw-bold text-center">Edit Proyek</h1>

    <div class="card shadow-sm p-4 form-card">

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label text-white">Judul Proyek</label> 
                <input type="text" name="judul_proyek" class="form-control" placeholder="Masukkan judul proyek"
                       value="<?= htmlspecialchars($data['judul_proyek']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Isi Proyek</label>
                <textarea name="isi_proyek" class="form-control" rows="5" placeholder="Masukkan isi proyek"
                          required><?= htmlspecialchars($data['isi_proyek']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Tanggal Terbit</label>
                <input type="date" name="tanggal_terbit_proyek" class="form-control"
                       value="<?= $data['tanggal_terbit_proyek']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Penulis</label>
                <input type="text" name="penulis_proyek" class="form-control" placeholder="Masukkan penulis"
                       value="<?= htmlspecialchars($data['penulis_proyek']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Upload Gambar 1</label>
                <input type="file" name="gambar1" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Upload Gambar 2</label>
                <input type="file" name="gambar2" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Upload Gambar 3</label>
                <input type="file" name="gambar3" class="form-control">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                <a href="kelola_proyek.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>
    </div>
</div>
<script src="js/sidebar.js"></script>
</body>
</html>
