<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

if (isset($_POST['submit'])) {
    $jenis  = $_POST['id_jenismitra'];
    $nama   = pg_escape_string($conn, $_POST['nama_mitra']);
    $gambar = pg_escape_string($conn, $_POST['url_gambar_mitra']);
    $isi    = pg_escape_string($conn, $_POST['isi_mitra']);

    $qInsert = "CALL sp_create_mitra($1, $2, $3, $4)";
    $params  = array($jenis, $nama, $gambar, $isi);

    $result = pg_query_params($conn, $qInsert, $params);

    if ($result) {
        header("Location: kelola_mitra.php?msg=success");
        exit;
    } else {
        $error = "Gagal menambah data mitra!";
    }
}

// Ambil data jenis mitra untuk dropdown
$qJenis = pg_query($conn, "SELECT * FROM jenis_mitra ORDER BY id_jenismitra ASC");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Mitra</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h3>Tambah Mitra</h3>

<?php if(isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">

    <label>Jenis Mitra</label>
    <select name="id_jenismitra" class="form-control" required>
        <option value="">-- Pilih Jenis Mitra --</option>
        <?php while($j = pg_fetch_assoc($qJenis)): ?>
            <option value="<?= $j['id_jenismitra'] ?>">
                <?= htmlspecialchars($j['nama_jenismitra']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label class="mt-2">Nama Mitra</label>
    <input type="text" name="nama_mitra" class="form-control" required>

    <label class="mt-2">URL Gambar</label>
    <input type="text" name="url_gambar_mitra" class="form-control">

    <label class="mt-2">Deskripsi</label>
    <textarea name="isi_mitra" class="form-control" rows="4"></textarea>

    <br>
    <button name="submit" class="btn btn-success">Simpan</button>
    <a href="kelola_mitra.php" class="btn btn-secondary">Kembali</a>

</form>

</body>
</html>
