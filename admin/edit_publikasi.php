<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id = $_GET['id'] ?? 0;
    $id = intval($id);

    // Ambil data publikasi
    $qPub = "SELECT * FROM publikasi WHERE id_publikasi = $id";
    $rPub = pg_query($conn, $qPub);
    $pub = pg_fetch_assoc($rPub);

    if (!$pub) {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href='kelola_publikasi.php';</script>";
        exit;
    }

    // Ambil opsi
    $qJenis = "SELECT id_jenispublikasi, nama_jenispublikasi FROM jenis_publikasi ORDER BY id_jenispublikasi ASC";
    $rJenis = pg_query($conn, $qJenis);

    $qDosen = "SELECT id_dosen, nama_dosen FROM vw_detail_dosen ORDER BY nama_dosen ASC";
    $rDosen = pg_query($conn, $qDosen);

    if (isset($_POST['update'])) {

        $idJenis = intval($_POST['id_jenispublikasi']);
        $idDosen = intval($_POST['id_dosen']);
        $judul = pg_escape_string($conn, $_POST['judul']);
        $tahun = pg_escape_string($conn, $_POST['tahun']);

        $qUpdate = "
            CALL sp_update_publikasi(
                $id,
                $idJenis,
                $idDosen,
                '$judul',
                '$tahun'
            );
        ";

        pg_query($conn, $qUpdate);

        echo "<script>
                alert('Publikasi berhasil diperbarui!');
                window.location.href='kelola_publikasi.php';
            </script>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
    <title>Edit Publikasi</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
<div class='container'>
    <h1 class='mb-4 fw-bold text-center'>Edit Publikasi</h1>

    <div class='card shadow-sm p-4'>
        <form method='POST'>

            <div class='mb-3'>
                <label class='form-label text-white'>Judul Publikasi</label>
                <input type='text' name='judul' class='form-control' value='<?= htmlspecialchars($pub['judul_publikasi']); ?>' required>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Tahun</label>
                <input type='text' name='tahun' class='form-control' value='<?= htmlspecialchars($pub['tahun_publikasi']); ?>' required>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Jenis Publikasi</label>
                <select name='id_jenispublikasi' class='form-control' required>
                    <?php while($j = pg_fetch_assoc($rJenis)) : ?>
                        <option value="<?= $j['id_jenispublikasi']; ?>" <?= ($pub['id_jenispublikasi'] == $j['id_jenispublikasi']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($j['nama_jenispublikasi']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class='mb-3'>
                <label class='form-label text-white'>Dosen</label>
                <select name='id_dosen' class='form-control' required>
                    <?php while($d = pg_fetch_assoc($rDosen)) : ?>
                        <option value="<?= $d['id_dosen']; ?>" <?= ($pub['id_dosen'] == $d['id_dosen']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($d['nama_dosen']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class='d-flex gap-2 mt-3'>
                <button type='submit' name='update' class='btn btn-primary'>
                    <i class='fa fa-save'></i> Simpan Perubahan
                </button>
                <a href='kelola_publikasi.php' class='btn btn-secondary'>
                    <i class='fa fa-arrow-left'></i> Kembali
                </a>
            </div>

        </form>
    </div>
</div>
</body>
</html>
