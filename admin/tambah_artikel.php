<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "../config.php";

$qJenis = "SELECT * FROM jenis_artikel ORDER BY id_jenisartikel ASC";
$rJenis = pg_query($conn, $qJenis);

if (isset($_POST['simpan'])) {

    $fotoBaru = "";

    if (!empty($_FILES['foto']['name'])) {
        $uploadDir = "../img/artikel/";
        $filename = time() . "_" . basename($_FILES["foto"]["name"]);
        $targetFile = $uploadDir . $filename;

        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
            $fotoBaru = "img/artikel/" . $filename;
        }
    }

    $qInsert = "
        CALL sp_create_artikel(
            $_POST[id_jenisartikel],
            '$_POST[judul]',
            '$_POST[isi]',
            '$fotoBaru',
            '$_POST[tanggal]',
            '$_POST[penulis]'
        );
    ";

    pg_query($conn, $qInsert);

    echo "
    <script>
        alert('Artikel berhasil ditambahkan!');
        window.location.href='kelola_artikel.php';
    </script>
    ";
    exit;
}
?>

<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>Tambah Artikel</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
    <?php include 'sidebar.php'; ?>

<div class="content-area container">
    <h1 class='mb-4 fw-bold text-center'>Tambah Artikel</h1>

    <div class='card shadow-sm p-4'>
        <form method='POST' enctype='multipart/form-data'>

            <div class='mb-3'>
                <label class='form-label text-white'>Jenis Artikel</label>
                <select name='id_jenisartikel' class='form-control' required>
                    <option value='' disabled selected>Pilih Jenis Artikel</option>
                    <?php while($j = pg_fetch_assoc($rJenis)) : ?>
                        <option value="<?= $j['id_jenisartikel'] ?>">
                            <?= $j['nama_jenisartikel'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Judul</label>
                <input type='text' name='judul' class='form-control' placeholder="Masukkan judul artikel" required>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Isi Artikel</label>
                <textarea name='isi' class='form-control' rows='5' placeholder="Masukkan isi artikel" required></textarea>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Penulis</label>
                <input type='text' name='penulis' class='form-control' placeholder="Masukkan penulis" required>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Tanggal Terbit</label>
                <input type='date' name='tanggal' class='form-control' required>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Upload Gambar</label>
                <input type='file' name='foto' class='form-control' accept='image/*' required>
            </div>

            <div class='d-flex gap-2 mt-3'>
                <button type='submit' name='simpan' class='btn btn-primary'> Simpan </button>
                <a href='kelola_artikel.php' class='btn btn-secondary'> Kembali </a>
            </div>

        </form>
    </div>
</div>
<script src="js/sidebar.js"></script>
</body>
</html>
