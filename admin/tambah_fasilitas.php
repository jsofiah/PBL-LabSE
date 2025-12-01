<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$qJenis = "SELECT * FROM jenis_fasilitas ORDER BY id_jenisfasilitas";
$rJenis = pg_query($conn, $qJenis);

if (isset($_POST['submit'])) {
    $idJenis = $_POST['id_jenisfasilitas'];
    $nama = $_POST['nama_fasilitas'];
    $isi = $_POST['isi_fasilitas'];

    $folder = "../img/fasilitas/";

    $namaFile = time() . "_" . basename($_FILES["gambar"]["name"]);
    $targetFile = $folder . $namaFile;

    if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFile)) {

        $url_gambar = "img/fasilitas/" . $namaFile;

        $qInsert = "INSERT INTO fasilitas (id_jenisfasilitas, nama_fasilitas, isi_fasilitas, url_gambar_fasilitas)
                    VALUES ($1, $2, $3, $4)";

        $result = pg_query_params($conn, $qInsert, [$idJenis, $nama, $isi, $url_gambar]);

        if ($result) {
            header("Location: kelola_fasilitas.php");
            exit;
        } else {
            echo "Gagal menyimpan data!";
        }

    } else {
        echo "Upload gambar gagal!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Fasilitas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleForm.css">
</head>
<body class="container mt-4">

<h1 class="mb-4 fw-bold text-center">Tambah Fasilitas</h1>

<form method="POST" enctype="multipart/form-data">
    

    
    <div class="card shadow-sm p-4">
        <div class="mb-3">
        <label class="form-label text-white">Jenis Fasilitas</label>
        <select name="id_jenisfasilitas" class="form-control" required>
            <option value="">Pilih Jenis</option>
            <?php while ($row = pg_fetch_assoc($rJenis)) : ?>
                <option value="<?= $row['id_jenisfasilitas']; ?>">
                    <?= $row['nama_jenisfasilitas']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label text-white">Nama Fasilitas</label>
        <input type="text" name="nama_fasilitas" placeholder="Masukkan nama fasilitas" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label text-white">Isi Fasilitas</label>
        <textarea name="isi_fasilitas" placeholder="Masukkan isi fasilitas" class="form-control" rows="4" required></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label text-white">Upload Gambar Fasilitas</label>
        <input type="file" name="gambar" class="form-control" accept="image/*" required>
    </div>
    <div class="d-flex gap-2 mt-3">
    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
    <a href="kelola_fasilitas.php" class="btn btn-secondary">Kembali</a>
            </div>
    
    </div>
    

</form>

</body>
</html>
