<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "../config.php";

$id = $_GET['id'] ?? 0;

$qArtikel = "SELECT * FROM vw_artikel WHERE id_artikel = $id";
$rArtikel = pg_query($conn, $qArtikel);
$artikel = pg_fetch_assoc($rArtikel);

if (!$artikel) {
    echo "<script>alert('Artikel tidak ditemukan!'); window.location.href='kelola_artikel.php';</script>";
    exit;
}

$qJenis = "SELECT * FROM jenis_artikel ORDER BY id_jenisartikel ASC";
$rJenis = pg_query($conn, $qJenis);

if (isset($_POST['update'])) {

    $fotoLama = $_POST['foto_lama'];
    $fotoBaru = $fotoLama;

    if (!empty($_FILES['foto']['name'])) {
        $uploadDir = "../img/artikel/";
        $filename = time() . "_" . basename($_FILES["foto"]["name"]);
        $targetFile = $uploadDir . $filename;

        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {

            if (!empty($fotoLama) && file_exists("../" . $fotoLama)) {
                unlink("../" . $fotoLama);
            }

            $fotoBaru = "img/artikel/" . $filename;
        }
    }

    $qUpdate = "
        CALL sp_update_artikel(
            $id,
            $_POST[id_jenisartikel],
            '$_POST[judul]',
            '$_POST[isi]',
            '$fotoBaru',
            '$_POST[tanggal]',
            '$_POST[penulis]'
        );
    ";

    pg_query($conn, $qUpdate);

    echo "
    <script>
        alert('Artikel berhasil diperbarui!');
        window.location.href='kelola_artikel.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>Edit Artikel</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
<?php include 'sidebar.php'; ?>

<div class='content-area container'>
    <h1 class='mb-4 fw-bold text-center'>Edit Artikel</h1>

    <div class='card shadow-sm p-4'>
        <form method='POST' enctype='multipart/form-data'>
            <div class='mb-3'>
                <label class='form-label text-white'>Upload Gambar Baru (Opsional)</label>
                <input type='file' name='foto' class='form-control' accept='image/*'>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Jenis Artikel</label>
                <select name='id_jenisartikel' class='form-control' required>
                    <?php while($j = pg_fetch_assoc($rJenis)) : ?>
                        <option 
                            value="<?= $j['id_jenisartikel']; ?>"
                            <?= ($artikel['id_jenisartikel'] == $j['id_jenisartikel']) ? 'selected' : '' ?>>
                            <?= $j['nama_jenisartikel']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Judul</label>
                <input type='text' name='judul' class='form-control' value='<?= $artikel['judul_artikel']; ?>' required>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Isi Artikel</label>
                <textarea name='isi' class='form-control' rows='5' required><?= $artikel['isi_artikel']; ?></textarea>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Penulis</label>
                <input type='text' name='penulis' class='form-control' value='<?= $artikel['penulis_artikel']; ?>' required>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Tanggal Terbit</label>
                <input type='date' name='tanggal' class='form-control'
                    value='<?= $artikel['tanggal_terbit_artikel']; ?>' required>
            </div>

            <div class='d-flex gap-2 mt-3'>
                <button type='submit' name='update' class='btn btn-primary'>
                    Simpan Perubahan
                </button>
                <a href='kelola_artikel.php' class='btn btn-secondary'> Kembali </a>
            </div>

        </form>
    </div>

</div>
<script src="js/sidebar.js"></script>
</body>
</html>
