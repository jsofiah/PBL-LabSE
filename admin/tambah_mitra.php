<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$qJenis = pg_query($conn, "SELECT * FROM jenis_mitra ORDER BY id_jenismitra ASC");

if (isset($_POST['submit'])) {

    $jenis  = $_POST['id_jenismitra'];
    $nama   = pg_escape_string($conn, $_POST['nama_mitra']);
    $isi    = pg_escape_string($conn, $_POST['isi_mitra']);
    $folder = "../img/mitra/";

    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    $namaFile = time() . "_" . basename($_FILES["gambar"]["name"]);
    $targetFile = $folder . $namaFile;

    if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFile)) {

        $url_gambar = "img/mitra/" . $namaFile;

        $query = "CALL sp_create_mitra($1, $2, $3, $4)";
        $params = array($jenis, $nama, $url_gambar, $isi);

        $res = pg_query_params($conn, $query, $params);

        if ($res) {
            echo "<script>
                    alert('Mitra berhasil ditambahkan!');
                    window.location='kelola_mitra.php';
                  </script>";
            exit;
        } else {
            $error = "Gagal menambahkan data!";
        }

    } else {
        $error = "Upload gambar gagal!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tambah Mitra</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="css/styleForm.css" rel="stylesheet">
</head>

<body class="p-4">
<div class="content-area container">
    <h1 class="mb-4 fw-bold text-center">Tambah Mitra</h1>

    <div class="card shadow-sm p-4 form-card">

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label text-white">Jenis Mitra</label>
                <select name="id_jenismitra" class="form-control" required>
                    <option value="">Pilih Jenis Mitra</option>
                    <?php while($j = pg_fetch_assoc($qJenis)): ?>
                        <option value="<?= $j['id_jenismitra'] ?>">
                            <?= htmlspecialchars($j['nama_jenismitra']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Nama Mitra</label>
                <input type="text" name="nama_mitra" placeholder="Masukkan nama mitra" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Upload Gambar Mitra</label>
                <input type="file" name="gambar" class="form-control" accept="image/*" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Deskripsi</label>
                <textarea name="isi_mitra" class="form-control" placeholder="Masukkan deskripsi" rows="4"></textarea>
            </div>

            <div class="d-flex gap-2">
                <button name="submit" class="btn btn-primary">Simpan</button>
                <a href="kelola_mitra.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>
</div>
</body>
</html>