<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "../config.php";

$id = $_GET['id'] ?? 0;

$qData = "SELECT * FROM jenis_publikasi WHERE id_jenispublikasi = $id";
$rData = pg_query($conn, $qData);
$data = pg_fetch_assoc($rData);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='kelola_jenispublikasi.php';</script>";
    exit;
}

if (isset($_POST['update'])) {

    $qUpdate = "
        CALL sp_update_jenispublikasi(
            $id,
            '$_POST[nama]'
        );
    ";

    pg_query($conn, $qUpdate);

    echo "
        <script>
            alert('Jenis Publikasi berhasil diperbarui!');
            window.location.href = 'kelola_jenispublikasi.php';
        </script>
    ";
    exit;
}
?>

<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>Edit Jenis Publikasi</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
    <?php include 'sidebar.php'; ?>

    <div class='content-area container'>
        <h1 class='mb-4 fw-bold text-center'>Edit Jenis Publikasi</h1>

        <div class='card shadow-sm p-4'>
            <form method='POST'>

                <div class='mb-3'>
                    <label class='form-label text-white'>Nama Jenis Publikasi</label>
                    <input type='text' name='nama' class='form-control'
                           value='<?= htmlspecialchars($data['nama_jenispublikasi']); ?>'
                           required>
                </div>

                <div class='d-flex gap-2 mt-3'>
                    <button type='submit' name='update' class='btn btn-primary'>
                        <i class='fa fa-save'></i> Simpan Perubahan
                    </button>
                    <a href='kelola_jenispublikasi.php' class='btn btn-secondary'>
                        <i class='fa fa-arrow-left'></i> Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>
