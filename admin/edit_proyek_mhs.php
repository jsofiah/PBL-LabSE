<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

if (!isset($_GET['id_proyek']) || !isset($_GET['id_mhs'])) {
    header("Location: kelola_proyek.php");
    exit;
}

$id_proyek = intval($_GET['id_proyek']);
$id_mhs_lama = intval($_GET['id_mhs']);

$qRelasi = "
    SELECT pm.id_proyek, pm.id_mhs, 
           p.judul_proyek, 
           m.nama_mhs
    FROM proyek_mhs pm
    JOIN proyek p ON pm.id_proyek = p.id_proyek
    JOIN mhs_segeeks m ON pm.id_mhs = m.id_mhs
    WHERE pm.id_proyek = $1 AND pm.id_mhs = $2
";

$rRelasi = pg_query_params($conn, $qRelasi, [$id_proyek, $id_mhs_lama]);
$data = pg_fetch_assoc($rRelasi);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='kelola_proyek.php';</script>";
    exit;
}

$rMhs = pg_query($conn, "SELECT id_mhs, nama_mhs FROM mhs_segeeks ORDER BY id_mhs");

if (isset($_POST['update'])) {
    $id_mhs_baru = intval($_POST['id_mhs']);

    $qUpdate = "CALL sp_update_proyek_mhs($1, $2, $3)";
    $params = array($id_proyek, $id_mhs_lama, $id_mhs_baru);

    $res = pg_query_params($conn, $qUpdate, $params);

    if ($res) {
        echo "<script>
                alert('Mahasiswa pada proyek berhasil diperbarui!');
                window.location='kelola_proyek.php';
              </script>";
        exit;
    } else {
        $error = "Gagal update relasi!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Proyek–Mahasiswa</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleSidebar.css">
<link href="css/styleForm.css" rel="stylesheet">

</head>

<body class="p-4">
    <?php include 'sidebar.php'; ?>

<div class="content-area container">

    <h1 class="fw-bold text-center mb-4">Edit Relasi Proyek - Mahasiswa</h1>

    <div class="card shadow-sm p-4 form-card">

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-white">Mahasiswa</label>
                <select name="id_mhs" class="form-control" required>
                    <?php while ($m = pg_fetch_assoc($rMhs)): ?>
                        <option value="<?= $m['id_mhs']; ?>"
                            <?= $m['id_mhs'] == $data['id_mhs'] ? 'selected' : '' ?>>
                            <?= $m['id_mhs']; ?> - <?= htmlspecialchars($m['nama_mhs']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                <a href="kelola_proyek.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>
</div>
<script src="js/sidebar.js"></script>
</body>
</html>