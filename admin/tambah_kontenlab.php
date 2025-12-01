<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    if (isset($_POST['simpan'])) {

        $judul = $_POST['judul'];
        $isi = $_POST['isi'];

        $qInsert = "
            CALL sp_create_konten_lab(
                '$judul',
                '$isi'
            );
        ";

        pg_query($conn, $qInsert);

        echo "
        <script>
            alert('Konten baru berhasil ditambahkan!');
            window.location.href = 'kelola_kontenLab.php';
        </script>
        ";
        exit;
    }
?>

<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>Tambah Konten</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
<div class='container'>
    <h1 class='mb-4 fw-bold text-center'>Tambah Konten Lab</h1>

    <div class='card shadow-sm p-4'>
        <form method='POST'>

            <div class='mb-3'>
                <label class='form-label text-white'>Judul Konten</label>
                <input type='text' name='judul' class='form-control' placeholder="tambah judul Konten" required>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Isi Konten</label>
                <textarea name='isi' rows='5' class='form-control' placeholder="tambah Isi Konten" required></textarea>
            </div>

            <div class='d-flex gap-2 mt-3'>
                <button type='submit' name='simpan' class='btn btn-primary'>
                    <i class='fa fa-plus'></i> Tambah Konten
                </button>
                <a href='kelola_kontenLab.php' class='btn btn-secondary'>
                    <i class='fa fa-arrow-left'></i> Kembali
                </a>
            </div>

        </form>
    </div>
</div>
</body>
</html>
