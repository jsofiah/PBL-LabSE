<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$id = $_GET['id'];

$qData = "SELECT * FROM mitra WHERE id_mitra = $1";
$rData = pg_query_params($conn, $qData, [$id]);
$data = pg_fetch_assoc($rData);

if (!$data) {
    die("Data tidak ditemukan");
}

if (isset($_POST['submit'])) {
    $jenis = $_POST['id_jenismitra'];
    $nama  = $_POST['nama_mitra'];
    $gambar = $_POST['url_gambar_mitra'];
    $isi = $_POST['isi_mitra'];

    $qUpdate = "
        CALL sp_update_mitra(
            $id,
            $jenis,
            '".pg_escape_string($nama)."',
            '".pg_escape_string($gambar)."',
            '".pg_escape_string($isi)."'
        );
    ";

    $res = pg_query($conn, $qUpdate);

    if ($res) {
        header("Location: kelola_mitra.php?msg=updated");
        exit;
    } else {
        $error = "Gagal mengupdate data!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Mitra</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h3>Edit Mitra</h3>

<form method="POST">

    <label>Jenis Mitra</label>
    <input type="number" name="id_jenismitra" class="form-control"
           value="<?= $data['id_jenismitra']; ?>" required>

    <label>Nama Mitra</label>
    <input type="text" name="nama_mitra" class="form-control"
           value="<?= htmlspecialchars($data['nama_mitra']); ?>" required>

    <label>URL Gambar</label>
    <input type="text" name="url_gambar_mitra" class="form-control"
           value="<?= htmlspecialchars($data['url_gambar_mitra']); ?>">

    <label>Deskripsi</label>
    <textarea name="isi_mitra" class="form-control" rows="4"><?= htmlspecialchars($data['isi_mitra']); ?></textarea>

    <br>
    <button name="submit" class="btn btn-warning">Update</button>
    <a href="kelola_mitra.php" class="btn btn-secondary">Kembali</a>

</form>

</body>
</html>