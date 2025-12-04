<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $qMhs = "SELECT id_mhs, nama_mhs FROM vw_mhs_full ORDER BY nama_mhs ASC";
    $rMhs = pg_query($conn, $qMhs);

    $qKeahlian = "SELECT id_keahlian, nama_keahlian FROM vw_bidang_keahlian ORDER BY nama_keahlian ASC";
    $rKeahlian = pg_query($conn, $qKeahlian);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $id_mhs = $_POST['id_mhs'];
        $id_keahlian = $_POST['id_keahlian'];

        $qCek = "
            SELECT 1 
            FROM mhs_menguasai_keahlian 
            WHERE id_mhs = $1 AND id_keahlian = $2
        ";

        $rCek = pg_query_params($conn, $qCek, [$id_mhs, $id_keahlian]);

        if (pg_num_rows($rCek) > 0) {
            echo "<script>
                alert('Data keahlian untuk mahasiswa ini sudah ada!');
                window.location='kelola_mhsKeahlian.php';
            </script>";
            exit;
        }

        $q = "CALL sp_create_mhs_keahlian($1, $2)";
        pg_query_params($conn, $q, [$id_mhs, $id_keahlian]);

        echo "<script>
            alert('Keahlian Mahasiswa Berhasil Ditambahkan');
            window.location='kelola_mhsKeahlian.php';
        </script>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
<title>Tambah Keahlian Mahasiswa</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleSidebar.css">
<link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
    <?php include 'sidebar.php'; ?>

<div class='content-area container'>
    <h2 class='mb-4'>Tambah Keahlian Mahasiswa</h2>
    <div class='card shadow-sm p-4'>
        <form method='POST'>
            <div class="mb-3">
                <label class="text-white">Nama Mahasiswa</label>
                <select name="id_mhs" class="form-control" required>
                    <option value="">Pilih Mahasiswa</option>
                    <?php while ($d = pg_fetch_assoc($rMhs)) : ?>
                        <option value="<?= $d['id_mhs'] ?>"><?= htmlspecialchars($d['nama_mhs']) ?></option>
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

            <button type='submit' name='simpan' class='btn btn-primary'>Tambah
            </button>
            <a href='kelola_mhsKeahlian.php' class='btn btn-secondary'>Kembali
            </a>

        </form>
    </div>
</div>
<script src="js/sidebar.js"></script>
</body>
</html>