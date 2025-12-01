<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$id = intval($_GET['id']);

// Data lama
$qData = "SELECT * FROM mitra WHERE id_mitra = $1";
$rData = pg_query_params($conn, $qData, [$id]);
$data = pg_fetch_assoc($rData);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='kelola_mitra.php';</script>";
    exit;
}

// Dropdown jenis mitra
$qJenis = pg_query($conn, "SELECT * FROM jenis_mitra ORDER BY id_jenismitra ASC");

if (isset($_POST['update'])) {

    $jenis  = $_POST['id_jenismitra'];
    $nama   = pg_escape_string($conn, $_POST['nama_mitra']);
    $gambar = pg_escape_string($conn, $_POST['url_gambar_mitra']);
    $isi    = pg_escape_string($conn, $_POST['isi_mitra']);

    $qUpdate = "CALL sp_update_mitra($1, $2, $3, $4, $5)";
    $params = array($id, $jenis, $nama, $gambar, $isi);

    $res = pg_query_params($conn, $qUpdate, $params);

    if ($res) {
        echo "<script>
                alert('Data mitra berhasil diperbarui!');
                window.location='kelola_mitra.php';
              </script>";
        exit;
    } else {
        $error = "Gagal update data!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Mitra</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="css/styleForm.css" rel="stylesheet">
<link href="css/styleSidebar.css" rel="stylesheet">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container">
    <h1 class="mb-4 fw-bold text-center">Edit Mitra</h1>

    <div class="card shadow-sm p-4 form-card">

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label text-white">Jenis Mitra</label>
                <select name="id_jenismitra" class="form-control" required>
                    <?php while($j = pg_fetch_assoc($qJenis)): ?>
                        <option value="<?= $j['id_jenismitra'] ?>"
                            <?= $j['id_jenismitra'] == $data['id_jenismitra'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($j['nama_jenismitra']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Nama Mitra</label>
                <input type="text" name="nama_mitra" class="form-control"
                       value="<?= htmlspecialchars($data['nama_mitra']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">URL Gambar</label>
                <input type="text" name="url_gambar_mitra" class="form-control"
                       value="<?= htmlspecialchars($data['url_gambar_mitra']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Deskripsi</label>
                <textarea name="isi_mitra" class="form-control" rows="4"><?= 
                    htmlspecialchars($data['isi_mitra']); ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                <a href="kelola_mitra.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>
</div>

<script src="js/sidebar.js"></script>
</body>
</html>
