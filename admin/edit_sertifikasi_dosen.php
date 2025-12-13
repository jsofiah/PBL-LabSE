<?php
session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require '../config.php';
    $id_sertifikasi_lama = $_GET['id'] ?? 0;

    $qDetail = "
        SELECT id_dosen, id_sertifikasi
        FROM mempunyai_sertifikasi
        WHERE id_sertifikasi = $1
    ";
    $rDetail = pg_query_params($conn, $qDetail, [$id_sertifikasi_lama]);
    $detail = pg_fetch_assoc($rDetail);

    if (!$detail) {
        echo "<script>alert('Relasi sertifikasi dosen tidak ditemukan'); 
            window.location='kelola_sertifikasi.php';</script>";
        exit;
    }

    $id_dosen = $detail['id_dosen'];

    $qSert = "SELECT id_sertifikasi, nama_sertifikasi FROM sertifikasi ORDER BY nama_sertifikasi";
    $rSert = pg_query($conn, $qSert);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $id_sertifikasi_baru = $_POST['id_sertifikasi'];

        $cekQuery = "
            SELECT 1 FROM mempunyai_sertifikasi 
            WHERE id_dosen = $1 
            AND id_sertifikasi = $2
        ";
        $cekResult = pg_query_params($conn, $cekQuery, [$id_dosen, $id_sertifikasi_baru]);

        if (pg_num_rows($cekResult) > 0) {
            echo "<script>
                    alert('Sertifikasi tersebut sudah terdaftar untuk dosen ini!');
                    window.location.href = 'kelola_sertifikasi.php';
                </script>";
            exit;
        }

        $q = "CALL sp_update_dosen_sertifikasi($1, $2, $3)";
        pg_query_params($conn, $q, [$id_dosen, $id_sertifikasi_lama, $id_sertifikasi_baru]);

        echo "<script>
                alert('Sertifikasi berhasil diperbarui!');
                window.location.href='kelola_sertifikasi.php';
            </script>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
<title>Edit Sertifikasi Dosen</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel='stylesheet' href='css/styleForm.css'>
<link rel="stylesheet" href="css/styleSidebar.css">
</head>

<body class='p-4'>
    <?php include 'sidebar.php'; ?>

<div class='content-area container'>
    <h1 class='mb-4 fw-bold text-center'>Edit Sertifikasi Dosen</h1>
    <div class='card shadow-sm p-4'>
        <form method="POST">
        <div class="mb-3">
            <label class="text-white">Sertifikasi</label>
            <select name="id_sertifikasi" class="form-control">
                <?php while ($s = pg_fetch_assoc($rSert)) : ?>
                    <option 
                        value="<?= $s['id_sertifikasi'] ?>"
                        <?= $s['id_sertifikasi'] == $id_sertifikasi_lama ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($s['nama_sertifikasi']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type='submit' name='simpan' class='btn btn-primary'>Simpan Perubahan</button>
        <a href='kelola_sertifikasi.php' class='btn btn-secondary'>Kembali</a>
    </form>
    </div>
</div>
<script src="js/sidebar.js"></script>
</body>
</html>