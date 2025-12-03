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

if (isset($_POST['submit'])) {

    $id_proyek = $_POST['id_proyek'];
    $id_mhs    = $_POST['id_mhs'];

    $query = "CALL sp_create_proyek_mhs($1, $2)";
    $params = array($id_proyek, $id_mhs);

    $res = pg_query_params($conn, $query, $params);

    if ($res) {
        echo "<script>
                alert('Relasi proyek–mahasiswa berhasil ditambahkan!');
                window.location='kelola_proyek.php';
              </script>";
        exit;
    } else {
        $error = "Gagal menambahkan relasi!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Tambah Proyek – Mahasiswa</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleSidebar.css">
<link href="css/styleForm.css" rel="stylesheet">

</head>

<body class="p-4">
    <?php include 'sidebar.php'; ?>

<div class="content-area container">

    <h1 class="mb-4 fw-bold text-center">Tambah Relasi Proyek – Mahasiswa</h1>

    <div class="card shadow-sm p-4 form-card">

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label text-white">Proyek</label>
                <select name="id_proyek" class="form-control" required>
                    <option value="">Pilih Proyek</option>
                    <?php while ($p = pg_fetch_assoc($rProyek)) : ?>
                        <option value="<?= $p['id_proyek']; ?>">
                            <?= $p['id_proyek']; ?> - <?= htmlspecialchars($p['judul_proyek']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Mahasiswa</label>
                <select name="id_mhs" class="form-control" required>
                    <option value="">Pilih Mahasiswa</option>
                    <?php while ($m = pg_fetch_assoc($rMhs)) : ?>
                        <option value="<?= $m['id_mhs']; ?>">
                            <?= $m['id_mhs']; ?> - <?= htmlspecialchars($m['nama_mhs']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button name="submit" class="btn btn-primary">Simpan</button>
                <a href="kelola_proyek.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>

</div>
<script src="js/sidebar.js"></script>
</body>
</html>
