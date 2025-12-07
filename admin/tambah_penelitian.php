<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "../config.php";

if (isset($_POST['simpan'])) {

    $judul  = $_POST['judul'];
    $tahun  = $_POST['tahun'];
    $idDosen = $_POST['id_dosen'];

    $qInsert = "
        CALL sp_create_penelitian(
            '$judul',
            '$tahun',
            $idDosen
        );
    ";

    pg_query($conn, $qInsert);

    echo "
    <script>
        alert('Penelitian berhasil ditambahkan!');
        window.location.href = 'kelola_penelitian.php';
    </script>";
    exit;
}

$qDosen = "SELECT id_dosen, nama_dosen FROM vw_detail_dosen ORDER BY nama_dosen";
$rDosen = pg_query($conn, $qDosen);
?>

<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
<title>Tambah Penelitian</title>

<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleSidebar.css">
<link rel='stylesheet' href='css/styleForm.css'>

</head>

<body class='p-4'>
    <?php include 'sidebar.php'; ?>

    <div class='content-area container'>
        <h1 class='mb-4 fw-bold text-center'>Tambah Penelitian</h1>

        <div class='card shadow-sm p-4'>

            <form method='POST'>

                <div class='mb-3'>
                    <label class='form-label text-white'>Judul Penelitian</label>
                    <input type='text' name='judul' class='form-control' placeholder="Masukkan judul penelitian" required>
                </div>

                <div class='mb-3'>
                    <label class='form-label text-white'>Tahun Penelitian</label>
                    <input type='number' name='tahun' class='form-control' min="1990" max="2100" placeholder="masukkan tahun penelitian" required>
                </div>

                <div class='mb-3'>
                    <label class='form-label text-white'>Dosen</label>
                    <select name='id_dosen' class='form-control' required>
                        <option value='' disabled selected>Pilih Dosen</option>
                        <?php while ($d = pg_fetch_assoc($rDosen)) : ?>
                            <option value="<?= $d['id_dosen']; ?>">
                                <?= htmlspecialchars($d['nama_dosen']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class='d-flex gap-2 mt-3'>
                    <button type='submit' name='simpan' class='btn btn-primary'> Tambah Penelitian</button>
                    <a href='kelola_penelitian.php' class='btn btn-secondary'> Kembali </a>
                </div>

            </form>

        </div>
    </div>

<script src="js/sidebar.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js'></script>
</body>
</html>
