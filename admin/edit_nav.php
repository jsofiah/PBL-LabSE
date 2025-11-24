<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id_nav = $_GET['id'] ?? 0;

    $qNav = "SELECT * FROM vw_nav WHERE id_nav = $id_nav";
    $rNav = pg_query($conn, $qNav);
    $nav = pg_fetch_assoc($rNav);

    if (!$nav) {
        echo "Data tidak ditemukan!";
        exit;
    }

    if (isset($_POST['update'])) {

        $nama_nav = $_POST['nama_nav'];
        $url_nav = $_POST['url_nav'];

        $qUpdate = "
            CALL sp_update_nav(
                '$id_nav',
                '$nama_nav',
                '$url_nav'
            );
        ";

        pg_query($conn, $qUpdate);

        echo "
        <script>
            alert('Nav berhasil diperbarui!');
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
<title>Edit Nav</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="p-4">
    <div class="container">
        <h1 class="mb-4 fw-bold text-center">Edit Nav</h1>

        <div class="card shadow-sm p-4">
            <form method="POST">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-white">Nama Nav</label>
                        <input type="text" name="nama_nav" class="form-control" value="<?= htmlspecialchars($nav['nama_nav']); ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label text-white">URL Nav</label>
                        <input type="text" name="url_nav" class="form-control" value="<?= htmlspecialchars($nav['url_nav']); ?>" required>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="fa fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="kelola_nav.php" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
