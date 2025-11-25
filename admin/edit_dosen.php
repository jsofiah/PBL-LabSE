<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id_dosen = $_GET['id'] ?? 0;

    $qDosen = "SELECT * FROM vw_detail_dosen WHERE id_dosen = $id_dosen";
    $rDosen = pg_query($conn, $qDosen);
    $dosen = pg_fetch_assoc($rDosen);

    if (!$dosen) {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href='kelola_profil.php';</script>";
        exit;
    }

    if (isset($_POST['update'])) {

        $fotoLama = $_POST['foto_lama'];
        $fotoBaru = $fotoLama;

        if (!empty($_FILES['foto']['name'])) {
            $targetDir = "../img/dosen/";
            $filename = time() . "_" . basename($_FILES["foto"]["name"]);
            $targetFile = $targetDir . $filename;

            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {

                if (!empty($fotoLama) && file_exists("../" . $fotoLama)) {
                    unlink("../" . $fotoLama);
                }

                $fotoBaru = "img/dosen/" . $filename;
            }
        }

        $qUpdate = "
            CALL sp_update_dosen(
                $id_dosen,
                '$_POST[nama]',
                '$_POST[jabatan]',
                '$_POST[email]',
                '$fotoBaru'
            );
        ";

        pg_query($conn, $qUpdate);

        echo "
        <script>
            alert('Data dosen berhasil diperbarui!');
            window.location.href = 'kelola_profil.php';
        </script>
        ";
        exit;
    }
?>
<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
<title>Edit Dosen</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
    <div class='container'>
        <h1 class='mb-4 fw-bold text-center'>Edit Dosen</h1>

        <div class='card shadow-sm p-4'>
            <form method='POST' enctype='multipart/form-data'>

                <div class='text-center mb-3'>
                    <label class='form-label fw-bold text-white'>Foto Saat Ini:</label><br>
                    <img src='../<?= htmlspecialchars($dosen['url_foto_dosen']); ?>'
                        alt='Foto Dosen'
                        style='max-height:120px; border-radius:10px;'>
                    <input type='hidden' name='foto_lama' value='<?= $dosen['url_foto_dosen']; ?>'>
                </div>

                <div class='mb-3'>
                    <label class='form-label text-white'>Upload Foto Baru</label>
                    <input type='file' name='foto' class='form-control' accept='image/*'>
                </div>

                <div class='mb-3'>
                    <label class='form-label text-white'>Nama Dosen</label>
                    <input type='text' name='nama' class='form-control'
                        value='<?= $dosen['nama_dosen']; ?>' required>
                </div>

                <div class='mb-3'>
                    <label class='form-label text-white'>Jabatan</label>
                    <input type='text' name='jabatan' class='form-control'
                        value='<?= $dosen['jabatan_lab']; ?>' required>
                </div>

                <div class='mb-3'>
                    <label class='form-label text-white'>Email</label>
                    <input type='email' name='email' class='form-control'
                        value='<?= $dosen['email_dosen']; ?>' required>
                </div>

                <div class='d-flex gap-2 mt-3'>
                    <button type='submit' name='update' class='btn btn-primary'>
                        <i class='fa fa-save'></i> Simpan Perubahan
                    </button>
                    <a href='kelola_profil.php' class='btn btn-secondary'>
                        <i class='fa fa-arrow-left'></i> Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>

<script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js'></script>
</body>
</html>
