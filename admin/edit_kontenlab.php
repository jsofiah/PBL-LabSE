<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id = $_GET['id'] ?? 0;

    $qKonten = "SELECT * FROM vw_konten_lab WHERE id_konten = $id";
    $rKonten = pg_query($conn, $qKonten);
    $k = pg_fetch_assoc($rKonten);

    if (!$k) {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href='kelola_kontenLab.php';</script>";
        exit;
    }

    if (isset($_POST['update'])) {

        $judul = $_POST['judul'];
        $isi = $_POST['isi'];

        $qUpdate = "
            CALL sp_update_konten_lab(
                $id,
                '$judul',
                '$isi'
            );
        ";

        pg_query($conn, $qUpdate);

        echo "
        <script>
            alert('Konten berhasil diperbarui!');
            window.location.href = 'kelola_kontenLab.php';
        </script>
        ";
        exit;
    }
?>

<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>Edit Konten Lab</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel='stylesheet' href='css/styleForm.css'>
    <link rel="stylesheet" href="css/styleSidebar.css">
</head>

<body class='p-4'>
    <?php include 'sidebar.php'; ?>

<div class='content-area container'>
    <h1 class='mb-4 fw-bold text-center'>Edit Konten Lab</h1>

    <div class='card shadow-sm p-4'>
        <form method='POST'>

            <div class='mb-3'>
                <label class='form-label text-white'>Judul Konten</label>
                <input type='text' name='judul' class='form-control' placeholder="Masukkan judul konten"
                       value='<?= $k['judul_konten']; ?>' required>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Isi Konten</label>
                <textarea name='isi' rows='5' class='form-control' placeholder="Masukkan isi konten" required><?= $k['isi_konten']; ?></textarea>
            </div>

            <div class='d-flex gap-2 mt-3'>
                <button type='submit' name='update' class='btn btn-primary'>Simpan Perubahan</button>
                <a href='kelola_kontenLab.php' class='btn btn-secondary'>Kembali</a>
            </div>

        </form>
    </div>
</div>
<script src="js/sidebar.js"></script>
</body>
</html>
