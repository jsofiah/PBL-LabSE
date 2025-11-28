<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once "../config.php";

$id = $_GET['id'] ?? 0;

$q = "SELECT * FROM jenis_artikel WHERE id_jenisartikel = $id";
$r = pg_query($conn, $q);
$data = pg_fetch_assoc($r);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); location.href='kelola_jenisartikel.php';</script>";
    exit;
}

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];

    $sql = "CALL sp_update_jenisartikel($id, '$nama')";
    pg_query($conn, $sql);

    echo "<script>
            alert('Data berhasil diperbarui!');
            window.location.href='kelola_jenisartikel.php';
          </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Jenis Artikel</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="p-4">
<div class="container">
    <h1 class="fw-bold text-center mb-4">Edit Jenis Artikel</h1>

    <div class="card shadow-sm p-4">
        <form method="POST">

            <div class="mb-3">
                <label class="form-label text-white">Nama Jenis Artikel</label>
                <input type="text" name="nama" class="form-control"
                       value="<?= htmlspecialchars($data['nama_jenisartikel']); ?>" required>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button name="update" class="btn btn-primary">
                    <i class="fa fa-save"></i> Simpan Perubahan
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
