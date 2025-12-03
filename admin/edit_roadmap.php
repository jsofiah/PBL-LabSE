<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

if (!isset($_GET['id'])) {
    header("Location: kelola_roadmap.php");
    exit;
}

$id = intval($_GET['id']);

$qSelect = "SELECT * FROM roadmap WHERE id_roadmap = $1";
$rSelect = pg_query_params($conn, $qSelect, [$id]);

$data = pg_fetch_assoc($rSelect);
if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='kelola_roadmap.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $judul = $_POST['judul_roadmap'];
    $deskripsi = $_POST['deskripsi_roadmap'];
    $tanggal = $_POST['tanggal_roadmap'];

    $qUpdate = "CALL sp_update_roadmap($1, $2, $3, $4)";
    $params  = array($id, $judul, $deskripsi, $tanggal);

    $result = pg_query_params($conn, $qUpdate, $params);

    if ($result) {
        echo "<script>alert('Roadmap berhasil diperbarui!'); window.location='kelola_roadmap.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui roadmap!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Roadmap</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="p-4">

<div class="content-area container">
    <h1 class="mb-4 fw-bold text-center">Edit Roadmap</h1>

    <div class="card p-4 shadow-sm form-card">

        <form action="" method="POST">

            <div class="mb-3">
                <label class="form-label text-white">Judul Roadmap</label>
                <input type="text" name="judul_roadmap" class="form-control" placeholder="Masukkan judul roadmap"
                       value="<?= htmlspecialchars($data['judul_roadmap']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Deskripsi Roadmap</label>
                <textarea name="deskripsi_roadmap" class="form-control" rows="4" placeholder="Masukkan deskripsi roadmap" required><?= 
                    htmlspecialchars($data['deskripsi_roadmap']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Tanggal Roadmap</label>
                <input type="date" name="tanggal_roadmap" class="form-control"
                       value="<?= htmlspecialchars($data['tanggal_roadmap']); ?>" required>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                <a href="kelola_roadmap.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>
</div>

<script src="js/sidebar.js"></script>

</body>
</html>
