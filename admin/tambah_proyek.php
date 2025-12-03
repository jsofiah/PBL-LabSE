<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

if (isset($_POST['submit'])) {

    $judul   = pg_escape_string($conn, $_POST['judul_proyek']);
    $isi     = pg_escape_string($conn, $_POST['isi_proyek']);
    $tanggal = $_POST['tanggal_terbit_proyek'];
    $penulis = pg_escape_string($conn, $_POST['penulis_proyek']);

    $folder = "../img/proyek/";
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    function uploadFile($inputName, $folder) {
        if (!empty($_FILES[$inputName]['name'])) {
            $namaFile = time() . "_" . basename($_FILES[$inputName]["name"]);
            $path = $folder . $namaFile;
            if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $path)) {
                return "img/proyek/" . $namaFile;
            }
        }
        return "";
    }

    $g1 = uploadFile("gambar1", $folder);
    $g2 = uploadFile("gambar2", $folder);
    $g3 = uploadFile("gambar3", $folder);

    $query  = "CALL sp_create_proyek($1,$2,$3,$4,$5,$6,$7)";
    $params = array($judul, $isi, $tanggal, $penulis, $g1, $g2, $g3);

    $res = pg_query_params($conn, $query, $params);

    if ($res) {
        echo "<script>alert('Proyek berhasil ditambahkan!'); window.location='kelola_proyek.php';</script>";
        exit;
    } else {
        $error = "Gagal menambahkan proyek!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tambah Proyek</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/styleForm.css" rel="stylesheet">
</head>

<body class="p-4">

<div class="content-area container">
    <h1 class="mb-4 fw-bold text-center">Tambah Proyek</h1>

    <div class="card shadow-sm p-4 form-card">

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label text-white">Judul Proyek</label>
                <input type="text" name="judul_proyek" class="form-control" 
                placeholder="Masukkan judul proyek" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Isi Proyek</label>
                <textarea name="isi_proyek" class="form-control" rows="5" 
                placeholder="Masukkan isi proyek" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Tanggal Terbit</label>
                <input type="date" name="tanggal_terbit_proyek" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Penulis</label>
                <input type="text" name="penulis_proyek" class="form-control" 
                placeholder="Masukkan penulis" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Upload Gambar 1</label>
                <input type="file" name="gambar1" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Upload Gambar 2</label>
                <input type="file" name="gambar2" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Upload Gambar 3</label>
                <input type="file" name="gambar3" class="form-control" accept="image/*">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                <a href="kelola_proyek.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>
</div>

</body>
</html>
