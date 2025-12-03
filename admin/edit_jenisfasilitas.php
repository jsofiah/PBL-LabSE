<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$id = $_GET['id'];

$qSelect = "SELECT * FROM jenis_fasilitas WHERE id_jenisfasilitas = $1";
$rSelect = pg_query_params($conn, $qSelect, [$id]);
$data = pg_fetch_assoc($rSelect);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='kelola_jenisFasilitas.php';</script>";
    exit;
}

if (isset($_POST['submit'])) {
    $nama = $_POST['nama_jenisfasilitas'];

    $qUpdate = "CALL sp_update_jenis_fasilitas($1, $2)";

    $result = pg_query_params($conn, $qUpdate, [$id, $nama]);

    if ($result) {
        echo "
        <script>
            alert('Jenis Fasilitas berhasil diperbarui!');
            window.location.href = 'kelola_jenisFasilitas.php';
        </script>
        ";
        exit;
    } else {
        echo "
        <script>
            alert('Gagal mengupdate data! Error: " . pg_last_error($conn) . "');
            window.location.href = 'kelola_jenisFasilitas.php';
        </script>
        ";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Jenis Fasilitas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="p-4">
    <?php include 'sidebar.php'; ?>

    <div class="content-area container">
        <h1 class="mb-4 fw-bold text-center">Edit Jenis Fasilitas</h1>

        <form method="POST">
            <div class="card shadow-sm p-4">
                <div class="mb-3">
                    <label class="form-label text-white">Nama Jenis Fasilitas</label>
                    <input type="text" name="nama_jenisfasilitas" class="form-control"
                        placeholder="Masukkan jenis fasilitas"
                        value="<?= htmlspecialchars($data['nama_jenisfasilitas']); ?>" required>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" name="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="kelola_jenisFasilitas.php" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </form>
    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>