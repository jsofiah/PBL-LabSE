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
<link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
<div class='container'>
    <h1 class='mb-4 fw-bold text-center'>Edit Konten Lab</h1>

    <div class='card shadow-sm p-4'>
        <form method='POST'>

            <div class='mb-3'>
                <label class='form-label text-white'>Judul Konten</label>
                <input type='text' name='judul' class='form-control'
                       value='<?= $k['judul_konten']; ?>' required>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Isi Konten</label>
                <textarea name='isi' rows='5' class='form-control' required><?= $k['isi_konten']; ?></textarea>
            </div>

            <div class='d-flex gap-2 mt-3'>
                <button type='submit' name='update' class='btn btn-primary'>
                    <i class='fa fa-save'></i> Simpan Perubahan
                </button>
                <a href='kelola_kontenLab.php' class='btn btn-secondary'>
                    <i class='fa fa-arrow-left'></i> Kembali
                </a>
            </div>

        </form>
    </div>
</div>
</body>
</html>
