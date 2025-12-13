<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

if (!isset($_GET['id'])) {
    header("Location: kelola_jenisMitra.php");
    exit;
}

$id = intval($_GET['id']);

$q = "SELECT * FROM jenis_mitra WHERE id_jenismitra = $id";
$r = pg_query($conn, $q);
$data = pg_fetch_assoc($r);

if (!$data) {
    echo "<script>
            alert('Data tidak ditemukan!');
            window.location='kelola_jenisMitra.php';
          </script>";
    exit;
}

if (isset($_POST['update'])) {

    $nama = pg_escape_string($conn, $_POST['nama_jenismitra']);

    $qUpdate = "CALL sp_update_jenismitra($1, $2)";
    $res = pg_query_params($conn, $qUpdate, [$id, $nama]);


    if ($res) {
        echo "<script>
                alert('Jenis mitra berhasil diperbarui!');
                window.location='kelola_jenisMitra.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui data!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Jenis Mitra</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="css/styleForm.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleSidebar.css">
</head>

<body class="p-4">
    <?php include 'sidebar.php'; ?>

<div class="content-area container">
    <h1 class="mb-4 fw-bold text-center">Edit Jenis Mitra</h1>

    <div class="card shadow-sm p-4 form-card">
        <form method="POST">

            <div class="mb-3">
                <label class="form-label text-white">Nama Jenis Mitra</label>
                <input type="text" name="nama_jenismitra" placeholder="Masukkan nama jenis mitra" class="form-control"
                       value="<?= htmlspecialchars($data['nama_jenismitra']); ?>" required>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                <a href="kelola_jenisMitra.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>
    </div>
</div>
<script src="js/sidebar.js"></script>
</body>
</html>
