<?php
        session_start();
        if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
        }

        require_once "../config.php";

        $qDosen = "SELECT id_dosen, nama_dosen FROM vw_detail_dosen ORDER BY nama_dosen ASC";
        $rDosen = pg_query($conn, $qDosen);

        if (isset($_POST['simpan'])) {
        $id_dosen      = $_POST['id_dosen'];
        $jenjang       = $_POST['jenjang'];
        $universitas   = $_POST['universitas'];
        $bidang_studi  = $_POST['bidang_studi'];
        $tahun_lulus   = $_POST['tahun_lulus'];
        $gelar         = $_POST['gelar'];

        $qInsert = "
                CALL sp_create_riwayat_pendidikan(
                $id_dosen,
                '$jenjang',
                '$universitas',
                '$bidang_studi',
                '$tahun_lulus',
                '$gelar'
                );
        ";

        pg_query($conn, $qInsert);

        echo "
        <script>
                alert('Riwayat pendidikan berhasil ditambahkan!');
                window.location.href = 'kelola_pendidikan.php';
        </script>
        ";
        exit;
        }
?>

<!DOCTYPE html>
<html lang='id'>
<head>
<meta charset='UTF-8'>
<title>Tambah Riwayat Pendidikan</title>
<link href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">s
<link rel='stylesheet' href='css/styleForm.css'>
</head>

<body class='p-4'>
        <div class='container'>
                <h1 class='mb-4 fw-bold text-center'>Tambah Riwayat Pendidikan</h1>

                <div class='card shadow-sm p-4'>
                <form method='POST'>

                        <div class='mb-3'>
                        <label class='form-label text-white'>Nama Dosen</label>
                        <select name='id_dosen' class='form-control' required>
                                <option value='' disabled selected>Pilih Dosen</option>
                                <?php while ($d = pg_fetch_assoc($rDosen)) : ?>
                                <option value="<?= $d['id_dosen'] ?>">
                                        <?= htmlspecialchars($d['nama_dosen']) ?>
                                </option>
                                <?php endwhile; ?>
                        </select>
                        </div>

                        <div class='mb-3'>
                        <label class='form-label text-white'>Jenjang</label>
                        <input type='text' name='jenjang' class='form-control' placeholder="S1 / S2 / S3" required>
                        </div>

                        <div class='mb-3'>
                        <label class='form-label text-white'>Universitas</label>
                        <input type='text' name='universitas' class='form-control' placeholder="Masukkan universitas" required>
                        </div>

                        <div class='mb-3'>
                        <label class='form-label text-white'>Bidang Studi</label>
                        <input type='text' name='bidang_studi' class='form-control' placeholder="Masukkan bidang studi" required>
                        </div>

                        <div class='mb-3'>
                        <label class='form-label text-white'>Tahun Lulus</label>
                        <input type="number" name="tahun_lulus" class="form-control" min="1900" max="2100" step="1" placeholder="Masukkan tahun lulus" required>
                        </div>

                        <div class='mb-3'>
                        <label class='form-label text-white'>Gelar</label>
                        <input type='text' name='gelar' class='form-control' placeholder="S.T. / M.T. / Ph.D" required>
                        </div>

                        <div class='d-flex gap-2 mt-3'>
                        <button type='submit' name='simpan' class='btn btn-primary'>
                                <i class='fa fa-plus'></i> Tambah
                        </button>
                        <a href='kelola_pendidikan.php' class='btn btn-secondary'>
                                <i class='fa fa-arrow-left'></i> Kembali
                        </a>
                        </div>
                </form>
                </div>
        </div>
</body>
</html>
