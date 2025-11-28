<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require '../config.php';

$id = $_GET['id'];

$qData = "SELECT * FROM fasilitas WHERE id_fasilitas = $1";
$rData = pg_query_params($conn, $qData, [$id]);
$data = pg_fetch_assoc($rData);

// Ambil jenis
$qJenis = "SELECT * FROM jenis_fasilitas ORDER BY id_jenisfasilitas";
$rJenis = pg_query($conn, $qJenis);

if (isset($_POST['submit'])) {
    $idJenis = $_POST['id_jenisfasilitas'];
    $nama = $_POST['nama_fasilitas'];
    $isi = $_POST['isi_fasilitas'];
    $url = $_POST['url_gambar_fasilitas'];

    $qUpdate = "UPDATE fasilitas SET
                    id_jenisfasilitas = $1,
                    nama_fasilitas = $2,
                    isi_fasilitas = $3,
                    url_gambar_fasilitas = $4
                WHERE id_fasilitas = $5";

    $result = pg_query_params($conn, $qUpdate, [$idJenis, $nama, $isi, $url, $id]);

    if ($result) {
        header("Location: kelola_fasilitas.php");
        exit;
    } else {
        echo "Gagal memperbarui!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Fasilitas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
</head>
<body class="container mt-4">

<h3>Edit Fasilitas</h3>

<form method="POST">

    <div class="mb-3">
        <label>Jenis Fasilitas</label>
        <select name="id_jenisfasilitas" class="form-control" required>
            <?php while ($j = pg_fetch_assoc($rJenis)) : ?>
                <option value="<?= $j['id_jenisfasilitas']; ?>"
                    <?= $j['id_jenisfasilitas'] == $data['id_jenisfasilitas'] ? 'selected' : '' ?>>
                    <?= $j['nama_jenisfasilitas']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Nama Fasilitas</label>
        <input type="text" name="nama_fasilitas" class="form-control"
               value="<?= htmlspecialchars($data['nama_fasilitas']); ?>" required>
    </div>

    <div class="mb-3">
        <label>Isi Fasilitas</label>
        <textarea name="isi_fasilitas" class="form-control" rows="4" required><?= htmlspecialchars($data['isi_fasilitas']); ?></textarea>
    </div>

    <div class="mb-3">
        <label>URL Gambar</label>
        <input type="text" name="url_gambar_fasilitas" class="form-control"
               value="<?= htmlspecialchars($data['url_gambar_fasilitas']); ?>" required>
    </div>

    <button name="submit" class="btn btn-warning">Update</button>
    <a href="kelola_fasilitas.php" class="btn btn-secondary">Kembali</a>

</form>

</body>
</html>
