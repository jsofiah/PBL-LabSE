<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$id = $_GET['id'];

// Ambil data lama
$qSelect = "SELECT * FROM jenis_fasilitas WHERE id_jenisfasilitas = $1";
$rSelect = pg_query_params($conn, $qSelect, [$id]);
$data = pg_fetch_assoc($rSelect);

if (!$data) {
    die("Data tidak ditemukan!");
}

// Update data
if (isset($_POST['submit'])) {
    $nama = $_POST['nama_jenisfasilitas'];

    $qUpdate = "UPDATE jenis_fasilitas SET nama_jenisfasilitas = $1 WHERE id_jenisfasilitas = $2";
    $result = pg_query_params($conn, $qUpdate, [$nama, $id]);

    if ($result) {
        header("Location: kelola_jenisFasilitas.php");
        exit;
    } else {
        echo "Gagal mengupdate data!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Jenis Fasilitas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h3>Edit Jenis Fasilitas</h3>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Nama Jenis Fasilitas</label>
        <input type="text" name="nama_jenisfasilitas" class="form-control" value="<?= htmlspecialchars($data['nama_jenisfasilitas']); ?>" required>
    </div>

    <button type="submit" name="submit" class="btn btn-warning">Update</button>
    <a href="kelola_jenisFasilitas.php" class="btn btn-secondary">Kembali</a>
</form>

</body>
</html>
