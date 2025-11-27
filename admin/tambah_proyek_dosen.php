<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require "../config.php";

$rProyek = pg_query($conn, "SELECT id_proyek, judul_proyek FROM proyek ORDER BY id_proyek");
$rDosen  = pg_query($conn, "SELECT id_dosen, nama_dosen FROM dosen ORDER BY id_dosen");

if (isset($_POST['simpan'])) {
    $id_proyek = $_POST['id_proyek'];
    $id_dosen  = $_POST['id_dosen'];

    $query = "CALL sp_create_proyek_dosen($id_proyek, $id_dosen)";
    $res = pg_query($conn, $query);

    if ($res) {
        echo "<script>alert('Relasi berhasil ditambahkan!'); window.location='kelola_proyek_dosen.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menambahkan relasi!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Proyek–Dosen</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h2>Tambah Relasi Proyek – Dosen</h2>

<form method="POST">

    <label>Proyek</label>
    <select name="id_proyek" class="form-control" required>
        <option value="">-- Pilih Proyek --</option>
        <?php while ($p = pg_fetch_assoc($rProyek)) : ?>
            <option value="<?= $p['id_proyek']; ?>">
                <?= $p['id_proyek']; ?> - <?= htmlspecialchars($p['judul_proyek']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label class="mt-3">Dosen</label>
    <select name="id_dosen" class="form-control" required>
        <option value="">-- Pilih Dosen --</option>
        <?php while ($d = pg_fetch_assoc($rDosen)) : ?>
            <option value="<?= $d['id_dosen']; ?>">
                <?= $d['id_dosen']; ?> - <?= htmlspecialchars($d['nama_dosen']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br>
    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
    <a href="kelola_proyek_dosen.php" class="btn btn-secondary">Kembali</a>

</form>

</body>
</html>
