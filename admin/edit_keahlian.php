<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id_keahlian = $_GET['id'] ?? 0;

    $qKeahlian = "SELECT * FROM vw_bidang_keahlian WHERE id_keahlian = $id_keahlian";
    $rKeahlian = pg_query($conn, $qKeahlian);
    $keahlian = pg_fetch_assoc($rKeahlian);

    if (!$keahlian) {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href='kelola_profil.php';</script>";
        exit;
    }

    if (isset($_POST['update'])) {
        $qUpdate = "
            CALL sp_update_keahlian(
                $id_keahlian,
                '$_POST[nama_keahlian]'
            );
        ";

        pg_query($conn, $qUpdate);

        echo "
        <script>
            alert('Data keahlian berhasil diperbarui!');
            window.location.href = 'kelola_keahlian.php';
        </script>
        ";
        exit;
    }
?>
<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
<title>Edit Keahlian</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
    <div class='container'>
        <h1 class='mb-4 fw-bold text-center'>Edit Keahlian</h1>

        <div class='card shadow-sm p-4'>
            <form method='POST' enctype='multipart/form-data'>
                <div class='mb-3'>
                    <label class='form-label text-white'>Nama Keahlian</label>
                    <input type='text' name='nama_keahlian' class='form-control' placeholder="Masukkan keahlian"
                        value='<?= $keahlian['nama_keahlian']; ?>' required>
                </div>
                <div class='d-flex gap-2 mt-3'>
                    <button type='submit' name='update' class='btn btn-primary'>
                        <i class='fa fa-save'></i> Simpan Perubahan
                    </button>
                    <a href='kelola_keahlian.php' class='btn btn-secondary'>
                        <i class='fa fa-arrow-left'></i> Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>

<script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js'></script>
</body>
</html>
