<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    if (isset($_POST['simpan'])) {

        $nama_nav = $_POST['nama_nav'];
        $url_nav = $_POST['url_nav'];

        $qInsert = "
            CALL sp_create_nav(
                '$nama_nav',
                '$url_nav'
            );
        ";

        pg_query($conn, $qInsert);

        echo "
        <script>
            alert('Nav berhasil ditambahkan!');
            window.location.href = 'kelola_nav.php';
        </script>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Nav</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleSidebar.css">
<link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="p-4">
    <?php include 'sidebar.php'; ?>

    <div class="content-area container">
        <h1 class="mb-4 fw-bold text-center">Tambah Nav</h1>

        <div class="card shadow-sm p-4">
            <form method="POST">

                <div class="mb-3">
                    <label class="form-label text-white">Nama Nav</label>
                    <input type="text" name="nama_nav" class="form-control" placeholder="Masukkan nama nav" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">URL Nav</label>
                    <input type="text" name="url_nav" class="form-control" placeholder="Masukkan url nav" required>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <a href="kelola_nav.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>
