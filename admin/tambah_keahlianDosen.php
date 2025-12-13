<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $qDosen = "SELECT id_dosen, nama_dosen FROM vw_detail_dosen ORDER BY nama_dosen ASC";
    $rDosen = pg_query($conn, $qDosen);

    $qKeahlian = "SELECT id_keahlian, nama_keahlian FROM vw_bidang_keahlian ORDER BY nama_keahlian ASC";
    $rKeahlian = pg_query($conn, $qKeahlian);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $id_dosen = $_POST['id_dosen'];
        $id_keahlian = $_POST['id_keahlian'];

        $qCek = "
            SELECT 1 
            FROM dosen_menguasai_keahlian 
            WHERE id_dosen = $1 AND id_keahlian = $2
        ";

        $rCek = pg_query_params($conn, $qCek, [$id_dosen, $id_keahlian]);

        if (pg_num_rows($rCek) > 0) {
            echo "<script>
                alert('Data keahlian untuk dosen ini sudah ada!');
                window.location='kelola_dosenKeahlian.php';
            </script>";
            exit;
        }

        $q = "CALL sp_create_dosen_keahlian($1, $2)";
        pg_query_params($conn, $q, [$id_dosen, $id_keahlian]);

        echo "<script>
            alert('Keahlian Dosen Berhasil Ditambahkan');
            window.location='kelola_dosenKeahlian.php';
        </script>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
<title>Tambah Keahlian Dosen</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel='stylesheet' href='css/styleForm.css'>
<link rel="stylesheet" href="css/styleSidebar.css">
</head>

<body class='p-4'>
    <?php include 'sidebar.php'; ?>

<div class='content-area container'>
    <h1 class='mb-4 fw-bold text-center'>Tambah Keahlian Dosen</h1>
    <div class='card shadow-sm p-4'>
        <form method='POST'>
            <div class="mb-3">
                <label class="text-white">Nama Dosen</label>
                <select name="id_dosen" class="form-control" required>
                    <option value="">Pilih Dosen</option>
                    <?php while ($d = pg_fetch_assoc($rDosen)) : ?>
                        <option value="<?= $d['id_dosen'] ?>"><?= htmlspecialchars($d['nama_dosen']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="text-white">Nama Keahlian</label>
                <select name="id_keahlian" class="form-control" required>
                    <option value="">Pilih Keahlian</option>
                    <?php while ($s = pg_fetch_assoc($rKeahlian)) : ?>
                        <option value="<?= $s['id_keahlian'] ?>"><?= htmlspecialchars($s['nama_keahlian']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type='submit' name='simpan' class='btn btn-primary'>Simpan
            </button>
            <a href='kelola_dosenKeahlian.php' class='btn btn-secondary'>Kembali
            </a>

        </form>
    </div>
</div>
<script src="js/sidebar.js"></script>
</body>
</html>