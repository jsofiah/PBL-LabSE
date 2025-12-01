<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

if (isset($_POST['submit'])) {

    // FIX deprecated issue
    $nama = pg_escape_string($conn, $_POST['nama_jenismitra']);

    // Harus sesuai procedure (1 parameter)
    $q = "CALL sp_create_jenismitra('$nama');";

    $res = pg_query($conn, $q);

    if ($res) {
        echo "<script>
                alert('Jenis mitra berhasil ditambahkan!');
                window.location='kelola_jenisMitra.php';
              </script>";
    } else {
        echo "<script>alert('Gagal menambahkan jenis mitra!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Jenis Mitra</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/styleSidebar.css">
</head>

<body>
<?php include 'sidebar.php'; ?>

<div class="content-area container">
    <h3 class="mb-4">Tambah Jenis Mitra</h3>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Nama Jenis Mitra</label>
            <input type="text" name="nama_jenismitra" class="form-control" required>
        </div>

        <button name="submit" class="btn btn-success">Simpan</button>
        <a href="kelola_jenisMitra.php" class="btn btn-secondary">Kembali</a>

    </form>
</div>

<script src="js/sidebar.js"></script>
</body>
</html>
