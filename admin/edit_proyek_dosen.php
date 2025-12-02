<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require "../config.php";

if (!isset($_GET['id_proyek']) || !isset($_GET['id_dosen'])) {
    header("Location: kelola_proyek_dosen.php");
    exit;
}

$id_proyek_old = intval($_GET['id_proyek']);
$id_dosen_old  = intval($_GET['id_dosen']);

$qGet = "
    SELECT pd.id_proyek, pd.id_dosen, p.judul_proyek, d.nama_dosen
    FROM proyek_dosen pd
    JOIN proyek p ON pd.id_proyek = p.id_proyek
    JOIN dosen d ON pd.id_dosen = d.id_dosen
    WHERE pd.id_proyek = $1 AND pd.id_dosen = $2
";

$rGet = pg_query_params($conn, $qGet, [$id_proyek_old, $id_dosen_old]);
$data = pg_fetch_assoc($rGet);

if (!$data) {
    echo "<script>alert('Data relasi tidak ditemukan!'); window.location='kelola_proyek_dosen.php';</script>";
    exit;
}

$rDosen = pg_query($conn, "SELECT id_dosen, nama_dosen FROM dosen ORDER BY id_dosen");

if (isset($_POST['update'])) {

    $id_dosen_baru = $_POST['id_dosen'];

    $q = "CALL sp_update_proyek_dosen($1, $2, $3)";
    $params = array($id_proyek_old, $id_dosen_old, $id_dosen_baru);

    $res = pg_query_params($conn, $q, $params);

    if ($res) {
        echo "<script>
                alert('Relasi proyek–dosen berhasil diperbarui!');
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
    <title>Edit Proyek–Dosen</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/styleForm.css" rel="stylesheet">
</head>

<body class="p-4">
<div class="container">

    <h1 class="fw-bold text-center mb-4">Edit Relasi Proyek – Dosen</h1>

    <div class="card p-4 shadow-sm form-card">

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-white">Ganti Dosen</label>
                <select name="id_dosen" class="form-control" required>
                    <?php while ($d = pg_fetch_assoc($rDosen)) : ?>
                        <option value="<?= $d['id_dosen']; ?>"
                            <?= ($d['id_dosen'] == $data['id_dosen']) ? 'selected' : '' ?>>
                            <?= $d['id_dosen']; ?> - <?= htmlspecialchars($d['nama_dosen']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button name="update" class="btn btn-primary">Simpan Perubahan</button>
                <a href="kelola_proyek_dosen.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>
    </div>

</div>
</body>
</html>
