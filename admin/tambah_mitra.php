<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

// Ambil data jenis mitra
$qJenis = pg_query($conn, "SELECT * FROM jenis_mitra ORDER BY id_jenismitra ASC");

if (isset($_POST['submit'])) {

    $jenis  = $_POST['id_jenismitra'];
    $nama   = pg_escape_string($conn, $_POST['nama_mitra']);
    $gambar = pg_escape_string($conn, $_POST['url_gambar_mitra']);
    $isi    = pg_escape_string($conn, $_POST['isi_mitra']);

    $query = "CALL sp_create_mitra($1, $2, $3, $4)";
    $params = array($jenis, $nama, $gambar, $isi);

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
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Tambah Mitra</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="css/styleForm.css" rel="stylesheet">
<link href="css/styleSidebar.css" rel="stylesheet">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container">
    <h1 class="mb-4 fw-bold text-center">Tambah Mitra</h1>

    <div class="card shadow-sm p-4 form-card">

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label text-white">Jenis Mitra</label>
                <select name="id_jenismitra" class="form-control" required>
                    <option value="">-- Pilih Jenis Mitra --</option>

                    <?php while($j = pg_fetch_assoc($qJenis)): ?>
                        <option value="<?= $j['id_jenismitra'] ?>">
                            <?= htmlspecialchars($j['nama_jenismitra']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Nama Mitra</label>
                <input type="text" name="nama_mitra" class="form-control" 
                placeholder="Masukkan nama mitra" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">URL Gambar</label>
                <input type="text" name="url_gambar_mitra" class="form-control"
                placeholder="Masukkan url gambar" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Deskripsi</label>
                <textarea name="isi_mitra" class="form-control" rows="4" 
                placeholder="Masukkan deskripsi"></textarea>
            </div>

            <div class="d-flex gap-2">
                <button name="submit" class="btn btn-primary">Simpan</button>
                <a href="kelola_mitra.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>
</div>

<script src="js/sidebar.js"></script>
</body>
</html>
