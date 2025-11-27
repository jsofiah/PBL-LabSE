<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require "../config.php";

// Load proyek
$rProyek = pg_query($conn, "SELECT id_proyek, judul_proyek FROM proyek ORDER BY id_proyek");

// Load mahasiswa
$rMhs = pg_query($conn, "SELECT id_mhs, nama_mhs FROM mhs_segeeks ORDER BY id_mhs");

if (isset($_POST['simpan'])) {
    $p_id_proyek = $_POST['id_proyek'];
    $p_id_mhs    = $_POST['id_mhs'];

    $q = "CALL sp_create_proyek_mhs($p_id_proyek, $p_id_mhs)";
    $res = pg_query($conn, $q);

    if ($res) {
        echo "<script>alert('Relasi berhasil ditambahkan!'); window.location='kelola_proyek.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menambahkan relasi!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Proyek–Mahasiswa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h2>Tambah Relasi Proyek – Mahasiswa</h2>

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

    <label class="mt-3">Mahasiswa</label>
    <select name="id_mhs" class="form-control" required>
        <option value="">-- Pilih Mahasiswa --</option>
        <?php while ($m = pg_fetch_assoc($rMhs)) : ?>
            <option value="<?= $m['id_mhs']; ?>">
                <?= $m['id_mhs']; ?> - <?= htmlspecialchars($m['nama_mhs']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br>
    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
    <a href="kelola_proyek.php" class="btn btn-secondary">Kembali</a>

</form>

</body>
</html>
