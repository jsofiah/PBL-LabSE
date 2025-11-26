<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

if (isset($_POST['submit'])) {
    $jenis = $_POST['id_jenismitra'];
    $nama  = $_POST['nama_mitra'];
    $gambar = $_POST['url_gambar_mitra'];
    $isi = $_POST['isi_mitra'];

    $qInsert = "SELECT sp_create_mitra($1, $2, $3, $4)";
    $result = pg_query_params($conn, $qInsert, [$jenis, $nama, $gambar, $isi]);

    if ($result) {
        header("Location: kelola_mitra.php?msg=success");
        exit;
    } else {
        $error = "Gagal menambah data mitra!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Mitra</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h3>Tambah Mitra</h3>

<form method="POST">

    <label>Jenis Mitra</label>
    <input type="number" name="id_jenismitra" class="form-control" required>

    <label>Nama Mitra</label>
    <input type="text" name="nama_mitra" class="form-control" required>

    <label>URL Gambar</label>
    <input type="text" name="url_gambar_mitra" class="form-control">

    <label>Deskripsi</label>
    <textarea name="isi_mitra" class="form-control" rows="4"></textarea>

    <br>
    <button name="submit" class="btn btn-success">Simpan</button>
    <a href="kelola_mitra.php" class="btn btn-secondary">Kembali</a>

</form>

</body>
</html>
