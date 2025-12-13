<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $qNav = "SELECT DISTINCT id_nav, nama_nav FROM vw_nav ORDER BY nama_nav ASC";
    $rNav = pg_query($conn, $qNav);

    if (isset($_POST['simpan'])) {

        $nama_subnav = $_POST['nama_subnav'];
        $url_subnav  = $_POST['url_subnav'];
        $parent_nav  = $_POST['parent_nav'];

        $qInsert = "
            CALL sp_create_subnav(
                $parent_nav,
                '$nama_subnav',
                '$url_subnav'
            );
        ";

        pg_query($conn, $qInsert);

        echo "
        <script>
            alert('Subnav berhasil ditambahkan!');
            window.location.href = 'kelola_nav.php';
        </script>
        ";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Subnav</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleForm.css">
<link rel="stylesheet" href="css/styleSidebar.css">
</head>

<body class="p-4">
    <?php include 'sidebar.php'; ?>

    <div class="content-area container">
        <h1 class="mb-4 fw-bold text-center">Tambah Subnav</h1>

        <div class="card shadow-sm p-4">
            <form method="POST">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-white">Nama Subnav</label>
                        <input type="text" name="nama_subnav" class="form-control" placeholder="Masukkan nama subnav" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label text-white">URL Subnav</label>
                        <input type="text" name="url_subnav" class="form-control" placeholder="Masukkan url subnav" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label text-white">Parent Nav</label>
                        <select class="form-select" name="parent_nav" required>
                            <option disabled selected>Pilih Nav</option>
                            <?php while ($rowNav = pg_fetch_assoc($rNav)) : ?>
                                <option value="<?= $rowNav['id_nav']; ?>">
                                    <?= htmlspecialchars($rowNav['nama_nav']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <a href="kelola_nav.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
<script src="js/sidebar.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>