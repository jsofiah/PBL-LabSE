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

// Ambil data lama
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

if (isset($_POST['submit'])) {
    $nama = pg_escape_string($conn, $_POST['nama_jenismitra']);

    $qUpdate = "CALL sp_update_jenismitra($id, '$nama')";
    $res = pg_query($conn, $qUpdate);

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

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container">
    <h3>Edit Jenis Mitra</h3>

    <form method="POST">
        <label class="form-label">Nama Jenis Mitra</label>
        <input type="text" name="nama_jenismitra" class="form-control"
               value="<?= htmlspecialchars($data['nama_jenismitra']) ?>" required>

        <br>

        <button name="submit" class="btn btn-warning">Update</button>
        <a href="kelola_jenisMitra.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script src="js/sidebar.js"></script>
</body>
</html>
