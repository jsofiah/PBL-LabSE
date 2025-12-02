<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $qDosen = "SELECT id_dosen, nama_dosen FROM vw_detail_dosen ORDER BY nama_dosen ASC";
    $rDosen = pg_query($conn, $qDosen);

    $qSert = "SELECT id_sertifikasi, nama_sertifikasi FROM vw_sertifikasi ORDER BY nama_sertifikasi ASC";
    $rSert = pg_query($conn, $qSert);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $id_dosen = $_POST['id_dosen'];
        $id_sertifikasi = $_POST['id_sertifikasi'];

        $qCek = "
            SELECT 1 
            FROM mempunyai_sertifikasi 
            WHERE id_dosen = $1 AND id_sertifikasi = $2
        ";

        $rCek = pg_query_params($conn, $qCek, [$id_dosen, $id_sertifikasi]);

        if (pg_num_rows($rCek) > 0) {
            echo "<script>
                alert('Data sertifikasi untuk dosen ini sudah ada!');
                window.location='kelola_sertifikasi.php';
            </script>";
            exit;
        }

        $q = "CALL sp_create_dosen_sertifikasi($1, $2)";
        pg_query_params($conn, $q, [$id_dosen, $id_sertifikasi]);

        echo "<script>
            alert('Sertifikasi Dosen Berhasil Ditambahkan');
            window.location='kelola_sertifikasi.php';
        </script>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
<title>Tambah Sertifikasi Dosen</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
<div class='container'>
    <h2 class='mb-4 fw-bold text-center'>Tambah Sertifikasi Dosen</h2>
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
                <label class="text-white">Nama Sertifikasi</label>
                <select name="id_sertifikasi" class="form-control" required>
                    <option value="">Pilih Sertifikasi</option>
                    <?php while ($s = pg_fetch_assoc($rSert)) : ?>
                        <option value="<?= $s['id_sertifikasi'] ?>"><?= htmlspecialchars($s['nama_sertifikasi']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type='submit' name='simpan' class='btn btn-primary'>Simpan
            </button>
            <a href='kelola_sertifikasi.php' class='btn btn-secondary'>Kembali</a>
        </form>
    </div>
</div>
</body>
</html>