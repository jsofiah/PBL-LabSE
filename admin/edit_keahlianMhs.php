<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require '../config.php';

    $id_mhs = $_GET['id_mhs'] ?? 0;
    $id_keahlian_lama = $_GET['id_keahlian'] ?? 0;

    if ($id_mhs == 0 || $id_keahlian_lama == 0) {
        echo "<script>
                alert('Parameter tidak valid!');
                window.location='kelola_mhsKeahlian.php';
            </script>";
        exit;
    }

    $qDetail = "
        SELECT id_mhs, id_keahlian
        FROM mhs_menguasai_keahlian
        WHERE id_mhs = $1 AND id_keahlian = $2
    ";
    $rDetail = pg_query_params($conn, $qDetail, [$id_mhs, $id_keahlian_lama]);
    $detail = pg_fetch_assoc($rDetail);

    if (!$detail) {
        echo "<script>
                alert('Keahlian mahasiswa tidak ditemukan!');
                window.location='kelola_dosenKeahlian.php';
            </script>";
        exit;
    }

    $qKeahlian = "
        SELECT id_keahlian, nama_keahlian 
        FROM vw_bidang_keahlian 
        ORDER BY nama_keahlian ASC
    ";
    $rKeahlian = pg_query($conn, $qKeahlian);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_keahlian_baru = $_POST['id_keahlian'];

        $cekQuery = "
            SELECT 1 FROM mhs_menguasai_keahlian
            WHERE id_mhs = $1 AND id_keahlian = $2
        ";
        $cekRes = pg_query_params($conn, $cekQuery, [$id_mhs, $id_keahlian_baru]);

        if (pg_num_rows($cekRes) > 0) {
            echo "<script>
                    alert('Keahlian tersebut sudah dimiliki mahasiswa ini!');
                    window.location='kelola_dosenKeahlian.php';
                </script>";
            exit;
        }

        $qUpdate = "CALL sp_update_mhs_keahlian($1, $2, $3)";
        pg_query_params($conn, $qUpdate, [$id_mhs, $id_keahlian_lama, $id_keahlian_baru]);

        echo "<script>
                alert('Keahlian mahasiswa berhasil diperbarui!');
                window.location='kelola_mhsKeahlian.php';
            </script>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
<title>Edit Keahlian Dosen</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleSidebar.css">
<link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
    <?php include 'sidebar.php'; ?>
<div class='content-area container'>
    <h2 class='mb-4'>Edit Keahlian Mahasiswa</h2>
    <div class='card shadow-sm p-4'>
        <form method="POST">

            <div class="mb-3">
                <label class="text-white">Pilih Keahlian Baru</label>
                <select name="id_keahlian" class="form-control">
                    <?php while ($k = pg_fetch_assoc($rKeahlian)) : ?>
                        <option 
                            value="<?= $k['id_keahlian'] ?>"
                            <?= ($k['id_keahlian'] == $id_keahlian_lama) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($k['nama_keahlian']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type='submit' class='btn btn-primary'>
                <i class='fa fa-save'></i> Simpan Perubahan
            </button>

            <a href='kelola_mhsKeahlian.php' class='btn btn-secondary'>
                <i class='fa fa-arrow-left'></i> Kembali
            </a>

        </form>
    </div>
</div>
<script src="js/sidebar.js"></script>
</body>
</html>