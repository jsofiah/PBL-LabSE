<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: kelola_roadmap.php");
    exit;
}

$id = $_GET['id'];

// Ambil data roadmap berdasarkan ID
$qSelect = "SELECT * FROM roadmap WHERE id_roadmap = $id";
$rSelect = pg_query($conn, $qSelect);

$data = pg_fetch_assoc($rSelect);

if (!$data) {
    echo "<script>alert('Data roadmap tidak ditemukan!'); window.location='kelola_roadmap.php';</script>";
    exit;
}

// Jika form disubmit → proses UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul_roadmap'];
    $deskripsi = $_POST['deskripsi_roadmap'];
    $tanggal = $_POST['tanggal_roadmap'];

    $qUpdate = "
        CALL sp_update_roadmap(
            $id,
            '$judul',
            '$deskripsi',
            '$tanggal'
        );
    ";
    $result = pg_query($conn, $qUpdate);

    if ($result) {
        echo "<script>
                alert('Roadmap berhasil diperbarui!');
                window.location = 'kelola_roadmap.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui roadmap!');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Roadmap</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container">
    <h3 class="mb-4">Edit Roadmap</h3>

    <form action="" method="POST">

        <div class="mb-3">
            <label class="form-label">Judul Roadmap</label>
            <input type="text" name="judul_roadmap" class="form-control" 
                   value="<?= htmlspecialchars($data['judul_roadmap']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi Roadmap</label>
            <textarea name="deskripsi_roadmap" class="form-control" rows="4" required><?= htmlspecialchars($data['deskripsi_roadmap']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Roadmap</label>
            <input type="date" name="tanggal_roadmap" class="form-control"
                   value="<?= htmlspecialchars($data['tanggal_roadmap']); ?>" required>
        </div>

        <button type="submit" class="btn btn-warning">
            <i class="fa fa-edit"></i> Update
        </button>

        <a href="kelola_roadmap.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

</body>
</html>
