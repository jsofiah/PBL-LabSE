<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id_subnav = $_GET['id'] ?? 0;

    $qSubnav = "SELECT * FROM vw_nav WHERE id_subnav = $id_subnav";
    $rSubnav = pg_query($conn, $qSubnav);
    $subnav = pg_fetch_assoc($rSubnav);

    if (!$subnav) {
        echo "Data tidak ditemukan!";
        exit;
    }

    $qNav = "SELECT DISTINCT id_nav, nama_nav FROM vw_nav ORDER BY nama_nav ASC";
    $rNav = pg_query($conn, $qNav);

    if (isset($_POST['update'])) {

        $nama_subnav = $_POST['nama_subnav'];
        $url_subnav  = $_POST['url_subnav'];
        $parent_nav  = $_POST['parent_nav'];

        $qUpdate = "
            CALL sp_update_subnav(
                $id_subnav,
                '$nama_subnav',
                '$url_subnav',
                $parent_nav
            );
        ";

        pg_query($conn, $qUpdate);

        echo "
        <script>
            alert('Subnav berhasil diperbarui!');
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
<title>Edit Subnav</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleForm.css">
<link rel="stylesheet" href="css/styleSidebar.css">
</head>

<body class="p-4">
    <?php include 'sidebar.php'; ?>

    <div class="content-area container">
        <h1 class="mb-4 fw-bold text-center">Edit Subnav</h1>

        <div class="card shadow-sm p-4">
            <form method="POST">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-white">Nama Subnav</label>
                        <input type="text" name="nama_subnav" placeholder="Masukkan nama subnav" class="form-control"
                            value="<?= htmlspecialchars($subnav['nama_subnav']); ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label text-white">URL Subnav</label>
                        <input type="text" name="url_subnav" placeholder="Masukkan url subnav" class="form-control"
                            value="<?= htmlspecialchars($subnav['url_subnav']); ?>" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label text-white">Parent Nav</label>
                        <select class="form-select" name="parent_nav" required>
                            <option disabled>Pilih Nav</option>
                            <?php while ($rowNav = pg_fetch_assoc($rNav)) : ?>
                                <option value="<?= $rowNav['id_nav']; ?>"
                                    <?= ($rowNav['id_nav'] == $subnav['id_nav']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($rowNav['nama_nav']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>


                <div class="d-flex gap-2 mt-3">
                    <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="kelola_nav.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
<script src="js/sidebar.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
