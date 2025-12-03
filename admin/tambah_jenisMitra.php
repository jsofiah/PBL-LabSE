<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

if (isset($_POST['simpan'])) {

    $nama = pg_escape_string($conn, $_POST['nama_jenismitra']);

    $q = "CALL sp_create_jenismitra('$nama')";
    $res = pg_query($conn, $q);

    if ($res) {
        echo "<script>
                alert('Jenis mitra berhasil ditambahkan!');
                window.location='kelola_jenisMitra.php';
              </script>";
        exit;
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="p-4">
    <?php include 'sidebar.php'; ?>

    <div class="content-area container">
        <h1 class="mb-4 fw-bold text-center">Tambah Jenis Mitra</h1>

        <div class="card form-card shadow-sm p-4">

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label text-white">Nama Jenis Mitra</label>
                    <input type="text" name="nama_jenismitra" 
                           class="form-control" 
                           placeholder="Masukkan nama jenis mitra" required>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <a href="kelola_jenisMitra.php" class="btn btn-secondary">Kembali</a>
                </div>

            </form>
        </div>
    </div>
<script src="js/sidebar.js"></script>
</body>
</html>
