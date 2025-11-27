<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    if (isset($_POST['simpan'])) {

        $fotoBaru = "";

        if (!empty($_FILES['foto']['name'])) {
            $targetDir = "../img/dosen/";
            $filename = time() . "_" . basename($_FILES["foto"]["name"]);
            $targetFile = $targetDir . $filename;

            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
                $fotoBaru = "img/dosen/" . $filename;
            }
        }

        $qInsert = "
            CALL sp_create_dosen(
                '$_POST[nama]',
                '$_POST[jabatan]',
                '$_POST[email]',
                '$fotoBaru'
            );
        ";

        pg_query($conn, $qInsert);

        echo "
        <script>
            alert('Dosen baru berhasil ditambahkan!');
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
<title>Tambah Dosen</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
    <div class='container'>
        <h1 class='mb-4 fw-bold text-center'>Tambah Dosen</h1>

        <div class='card shadow-sm p-4'>
            <form method='POST' enctype='multipart/form-data'>

                <div class='mb-3'>
                    <label class='form-label text-white'>Nama Dosen</label>
                    <input type='text' name='nama' class='form-control' placeholder="Masukkan nama" required>
                </div>

                <div class='mb-3'>
                    <label class='form-label text-white'>Jabatan</label>
                    <select name='jabatan' class='form-control' required>
                        <option value='' disabled selected>Pilih Jabatan</option>
                        <option value='Ketua Laboratorium'>Ketua Laboratorium</option>
                        <option value='Peneliti'>Peneliti</option>
                    </select>
                </div>

                <div class='mb-3'>
                    <label class='form-label text-white'>Email</label>
                    <input type='email' name='email' class='form-control' placeholder="Masukkan email" required>
                </div>

                <div class='mb-3'>
                    <label class='form-label text-white'>Upload Foto</label>
                    <input type='file' name='foto' class='form-control' accept='image/*' required>
                </div>

                <div class='d-flex gap-2 mt-3'>
                    <button type='submit' name='simpan' class='btn btn-primary'>
                        <i class='fa fa-plus'></i> Tambah Dosen
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