<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

if (!isset($_GET['id'])) {
    header("Location: kelola_proyek.php");
    exit;
}

$id = $_GET['id'];

// Ambil data proyek
$qSelect = "SELECT * FROM proyek WHERE id_proyek = $1";
$rSelect = pg_query_params($conn, $qSelect, array($id));
$data = pg_fetch_assoc($rSelect);

if (!$data) {
    echo "<script>alert('Data proyek tidak ditemukan!'); window.location='kelola_proyek.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul  = $_POST['judul_proyek'];
    $isi    = $_POST['isi_proyek'];
    $tanggal= $_POST['tanggal_terbit_proyek'];
    $penulis= $_POST['penulis_proyek'];
    $g1     = $_POST['url_gambar_proyek1'];
    $g2     = $_POST['url_gambar_proyek2'];
    $g3     = $_POST['url_gambar_proyek3'];

    $query = "
        CALL sp_update_proyek(
            $id,
            '$judul',
            '$isi',
            '$tanggal',
            '$penulis',
            '$g1',
            '$g2',
            '$g3'
        );
    ";

    $res = pg_query($conn, $query);

    if ($res) {
        echo "<script>
                alert('Proyek berhasil diperbarui!');
                window.location = 'kelola_proyek.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui proyek!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Proyek</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container">
    <h3 class="mb-4">Edit Proyek</h3>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Judul Proyek</label>
            <input type="text" name="judul_proyek" class="form-control" value="<?= htmlspecialchars($data['judul_proyek']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Isi Proyek</label>
            <textarea name="isi_proyek" class="form-control" rows="6" required><?= htmlspecialchars($data['isi_proyek']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Terbit</label>
            <input type="date" name="tanggal_terbit_proyek" class="form-control" value="<?= htmlspecialchars($data['tanggal_terbit_proyek']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Penulis</label>
            <input type="text" name="penulis_proyek" class="form-control" value="<?= htmlspecialchars($data['penulis_proyek']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">URL Gambar 1</label>
            <input type="text" name="url_gambar_proyek1" class="form-control" value="<?= htmlspecialchars($data['url_gambar_proyek1']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">URL Gambar 2</label>
            <input type="text" name="url_gambar_proyek2" class="form-control" value="<?= htmlspecialchars($data['url_gambar_proyek2']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">URL Gambar 3</label>
            <input type="text" name="url_gambar_proyek3" class="form-control" value="<?= htmlspecialchars($data['url_gambar_proyek3']); ?>">
        </div>

        <button type="submit" class="btn btn-warning">
            <i class="fa fa-edit"></i> Update
        </button>

        <a href="kelola_proyek.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

</body>
</html>
