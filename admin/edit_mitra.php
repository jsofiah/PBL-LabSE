<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require '../config.php';

$id = intval($_GET['id']);

$qData = "SELECT * FROM mitra WHERE id_mitra = $1";
$rData = pg_query_params($conn, $qData, [$id]);
$data = pg_fetch_assoc($rData);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='kelola_mitra.php';</script>";
    exit;
}

$qJenis = pg_query($conn, "SELECT * FROM jenis_mitra ORDER BY id_jenismitra ASC");

if (isset($_POST['update'])) {

    $jenis = $_POST['id_jenismitra'];
    $nama  = pg_escape_string($conn, $_POST['nama_mitra']);
    $isi   = pg_escape_string($conn, $_POST['isi_mitra']);

    $gambarBaru = $data['url_gambar_mitra'];

    if (!empty($_FILES['gambar']['name'])) {

        $folder = "../img/mitra/";
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $namaFile = time() . "_" . basename($_FILES['gambar']['name']);
        $pathSimpan = $folder . $namaFile;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $pathSimpan)) {

            if (!empty($data['url_gambar_mitra'])) {
                $pathLama = "../" . $data['url_gambar_mitra'];
                if (file_exists($pathLama)) {
                    unlink($pathLama);
                }
            }

            $gambarBaru = "img/mitra/" . $namaFile;
        }
    }

    $qUpdate = "CALL sp_update_mitra($1, $2, $3, $4, $5)";
    $params = array($id, $jenis, $nama, $gambarBaru, $isi);

    $res = pg_query_params($conn, $qUpdate, $params);

    if ($res) {
        echo "<script>
                alert('Data mitra berhasil diperbarui!');
                window.location='kelola_mitra.php';
              </script>";
        exit;
    } else {
        $error = "Gagal update data!";
    }
}
?>



<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Mitra</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleSidebar.css">
<link href="css/styleForm.css" rel="stylesheet">
</head>

<body class="p-4">
    <?php include 'sidebar.php'; ?>
    
<div class="content-area container">
    <h1 class="mb-4 fw-bold text-center">Edit Mitra</h1>

    <div class="card shadow-sm p-4 form-card">

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label text-white">Jenis Mitra</label>
                <select name="id_jenismitra" class="form-control" required>
                    <?php while($j = pg_fetch_assoc($qJenis)): ?>
                        <option value="<?= $j['id_jenismitra'] ?>"
                            <?= $j['id_jenismitra'] == $data['id_jenismitra'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($j['nama_jenismitra']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Nama Mitra</label>
                <input type="text" name="nama_mitra" placeholder="Masukkan nama mitra" class="form-control"
                       value="<?= htmlspecialchars($data['nama_mitra']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Upload Gambar Baru</label>
                <input type="file" name="gambar" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Deskripsi</label>
                <textarea name="isi_mitra" placeholder="Masukkan deskripsi" class="form-control" rows="4"><?= 
                    htmlspecialchars($data['isi_mitra']); ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                <a href="kelola_mitra.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>
</div>

<script src="js/sidebar.js"></script>
</body>
</html>
