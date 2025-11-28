<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once "../config.php";

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];

    $sql = "CALL sp_create_jenisartikel('$nama')";
    pg_query($conn, $sql);

    echo "
    <script>
        alert('Jenis Artikel berhasil ditambahkan!');
        window.location.href = 'kelola_jenisartikel.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Jenis Artikel</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class="p-4">
<div class="container">
    <h1 class="fw-bold text-center mb-4">Tambah Jenis Artikel</h1>

    <div class="card shadow-sm p-4">
        <form method="POST">

            <div class="mb-3">
                <label class="form-label text-white">Nama Jenis Artikel</label>
                <input type="text" name="nama" class="form-control" required>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary" name="simpan">
                    <i class="fa fa-save"></i> Simpan
                </button>
                <a href="kelola_jenisartikel.php" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>

        </form>
    </div>
</div>
</body>
</html>
