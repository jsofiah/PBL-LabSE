<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "../config.php";

$id_penelitian = $_GET['id'] ?? 0;

$qData = "SELECT * FROM vw_penelitian_dosen WHERE id_penelitian = $id_penelitian";
$rData = pg_query($conn, $qData);
$data = pg_fetch_assoc($rData);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='kelola_penelitian.php';</script>";
    exit;
}

$qDosen = "SELECT id_dosen, nama_dosen FROM vw_detail_dosen ORDER BY nama_dosen";
$rDosen = pg_query($conn, $qDosen);

if (isset($_POST['update'])) {

    $judul = $_POST['judul'];
    $tahun = $_POST['tahun'];
    $idDosen = $_POST['id_dosen'];

    $qUpdate = "
        CALL sp_update_penelitian(
            $id_penelitian,
            '$judul',
            '$tahun',
            $idDosen
        );
    ";

    pg_query($conn, $qUpdate);

    echo "
    <script>
        alert('Data penelitian berhasil diperbarui!');
        window.location.href = 'kelola_penelitian.php';
    </script>
    ";
    exit;
}
?>

<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
<title>Edit Penelitian</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleSidebar.css">
<link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
    <?php include 'sidebar.php'; ?>

    <div class='content-area container'>
        <h1 class='mb-4 fw-bold text-center'>Edit Penelitian</h1>

        <div class='card shadow-sm p-4'>
            <form method='POST'>

                <div class='mb-3'>
                    <label class='form-label text-white'>Judul Penelitian</label>
                    <input type='text' name='judul' class='form-control'
                        value='<?= htmlspecialchars($data['judul_penelitian']); ?>' required>
                </div>

                <div class='mb-3'>
                    <label class='form-label text-white'>Tahun Penelitian</label>
                    <input type='number' name='tahun' class='form-control'
                        value='<?= htmlspecialchars($data['tahun_penelitian']); ?>'
                        min="1990" max="2100" required>
                </div>

                <div class='mb-3'>
                    <label class='form-label text-white'>Pilih Dosen</label>
                    <select name='id_dosen' class='form-control' required>
                        <option value='' disabled>Pilih Dosen</option>

                        <?php while ($d = pg_fetch_assoc($rDosen)) : ?>
                            <option value="<?= $d['id_dosen']; ?>"
                                <?= ($d['id_dosen'] == $data['id_dosen']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['nama_dosen']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class='d-flex gap-2 mt-3'>
                    <button type='submit' name='update' class='btn btn-primary'>
                        <i class='fa fa-save'></i> Simpan Perubahan
                    </button>

                    <a href='kelola_penelitian.php' class='btn btn-secondary'>
                        <i class='fa fa-arrow-left'></i> Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>
<script src="js/sidebar.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js'></script>
</body>
</html>
