<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id_pendidikan = $_GET['id'] ?? 0;

    $qDosen = "SELECT id_dosen, nama_dosen FROM vw_detail_dosen ORDER BY nama_dosen ASC";
    $rDosen = pg_query($conn, $qDosen);

    $qDetail = "
        SELECT 
            rp.*, 
            d.nama_dosen
        FROM riwayat_pendidikan rp
        JOIN dosen d ON d.id_dosen = rp.id_dosen
        WHERE rp.id_pendidikan = $id_pendidikan
    ";
    $rDetail = pg_query($conn, $qDetail);
    $data = pg_fetch_assoc($rDetail);

    if (!$data) {
        die("Data tidak ditemukan.");
    }

    if (isset($_POST['simpan'])) {
        $id_dosen      = $_POST['id_dosen'];
        $jenjang       = $_POST['jenjang'];
        $universitas   = $_POST['universitas'];
        $bidang_studi  = $_POST['bidang_studi'];
        $tahun_lulus   = $_POST['tahun_lulus'];
        $gelar         = $_POST['gelar'];

        $qUpdate = "
            CALL sp_update_riwayat_pendidikan(
                $id_pendidikan,
                '$jenjang',
                '$universitas',
                '$bidang_studi',
                '$tahun_lulus',
                '$gelar'
            );
        ";

        pg_query($conn, $qUpdate);

        echo "
        <script>
            alert('Riwayat pendidikan berhasil diperbarui!');
            window.location.href = 'kelola_pendidikan.php';
        </script>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
<title>Edit Riwayat Pendidikan</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleSidebar.css">
<link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
    <?php include 'sidebar.php'; ?>

    <div class='content-area container'>
        <h1 class='mb-4 fw-bold text-center'>Edit Riwayat Pendidikan</h1>

        <div class='card shadow-sm p-4'>
            <form method='POST'>
                <div class='mb-3'>
                    <label class='form-label text-white'>Nama Dosen</label>
                    <select name='id_dosen' class='form-control' required>
                        <?php while ($d = pg_fetch_assoc($rDosen)) : ?>
                            <option value="<?= $d['id_dosen'] ?>"
                                <?= ($d['id_dosen'] == $data['id_dosen']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['nama_dosen']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Jenjang</label>
                    <select name="jenjang" class="form-control" required>
                        <option value="" disabled>Pilih Jenjang</option>
                        <option value="D3" <?= ($data['jenjang'] == 'D3') ? 'selected' : '' ?>>D3</option>
                        <option value="D4" <?= ($data['jenjang'] == 'D4') ? 'selected' : '' ?>>D4</option>
                        <option value="S1" <?= ($data['jenjang'] == 'S1') ? 'selected' : '' ?>>S1</option>
                        <option value="S2" <?= ($data['jenjang'] == 'S2') ? 'selected' : '' ?>>S2</option>
                        <option value="S3" <?= ($data['jenjang'] == 'S3') ? 'selected' : '' ?>>S3</option>
                    </select>
                </div>

                <div class='mb-3'>
                    <label class='form-label text-white'>Universitas</label>
                    <input type='text' name='universitas' placeholder="Masukkan universitas" value="<?= htmlspecialchars($data['universitas']) ?>"
                        class='form-control' required>
                </div>

                <div class='mb-3'>
                    <label class='form-label text-white'>Bidang Studi</label>
                    <input type='text' name='bidang_studi' placeholder="Masukkan bidang studi" value="<?= htmlspecialchars($data['bidang_studi']) ?>"
                        class='form-control' required>
                </div>

                <div class='mb-3'>
                    <label class='form-label text-white'>Tahun Lulus</label>
                    <input type="number" name="tahun_lulus"
                        class="form-control"
                        min="1900" max="2100" step="1" placeholder="Masukkan tahun lulus"
                        value="<?= htmlspecialchars($data['tahun_lulus']) ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Gelar</label>
                    <select name="gelar" class="form-control" required>
                        <option value="" disabled <?= empty($data['gelar']) ? 'selected' : '' ?>>Pilih Gelar</option>
                        <option value="Diploma" <?= ($data['gelar'] == 'Diploma') ? 'selected' : '' ?>>Diploma</option>
                        <option value="Sarjana" <?= ($data['gelar'] == 'Sarjana') ? 'selected' : '' ?>>Sarjana</option>
                        <option value="Magister" <?= ($data['gelar'] == 'Magister') ? 'selected' : '' ?>>Magister</option>
                        <option value="Doktor" <?= ($data['gelar'] == 'Doktor') ? 'selected' : '' ?>>Doktor</option>
                    </select>
                </div>


                <div class='d-flex gap-2 mt-3'>
                    <button type='submit' name='simpan' class='btn btn-primary'>
                        <i></i> Simpan Perubahan
                    </button>
                    <a href='kelola_pendidikan.php' class='btn btn-secondary'>
                        <i></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>