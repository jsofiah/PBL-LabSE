<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul  = pg_escape_string($conn, $_POST['judul_proyek']);
    $isi    = pg_escape_string($conn, $_POST['isi_proyek']);
    $tanggal= pg_escape_string($conn, $_POST['tanggal_terbit_proyek']);
    $penulis= pg_escape_string($conn, $_POST['penulis_proyek']);
    $g1     = pg_escape_string($conn, $_POST['url_gambar_proyek1']);
    $g2     = pg_escape_string($conn, $_POST['url_gambar_proyek2']);
    $g3     = pg_escape_string($conn, $_POST['url_gambar_proyek3']);

    $query = "
        CALL sp_create_proyek(
            '$judul',
            '$isi',
            '$tanggal',
            '$penulis',
            '$g1',
            '$g2',
            '$g3'
        );
    ";

    $result = pg_query($conn, $query);

    if ($result) {
        echo "<script>
                alert('Proyek berhasil ditambahkan!');
                window.location = 'kelola_proyek.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('Gagal menambahkan proyek!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Proyek</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container">
    <h3 class="mb-4">Tambah Proyek</h3>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Judul Proyek</label>
            <input type="text" name="judul_proyek" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Isi Proyek</label>
            <textarea name="isi_proyek" class="form-control" rows="6" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Terbit</label>
            <input type="date" name="tanggal_terbit_proyek" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Penulis</label>
            <input type="text" name="penulis_proyek" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">URL Gambar 1</label>
            <input type="text" name="url_gambar_proyek1" class="form-control" placeholder="path/ke/gambar1.png">
        </div>

        <div class="mb-3">
            <label class="form-label">URL Gambar 2</label>
            <input type="text" name="url_gambar_proyek2" class="form-control" placeholder="path/ke/gambar2.png">
        </div>

        <div class="mb-3">
            <label class="form-label">URL Gambar 3</label>
            <input type="text" name="url_gambar_proyek3" class="form-control" placeholder="path/ke/gambar3.png">
        </div>

        <button type="submit" class="btn btn-success">
            <i class="fa fa-save"></i> Simpan
        </button>

        <a href="kelola_proyek.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

</body>
</html>