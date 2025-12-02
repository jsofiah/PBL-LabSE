<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once '../config.php';

$qSkills = "SELECT * FROM bidang_keahlian ORDER BY nama_keahlian ASC";
$rSkills = pg_query($conn, $qSkills);

if (isset($_POST['submit'])) {
    $nim = htmlspecialchars($_POST['nim']);
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $prodi = htmlspecialchars($_POST['prodi']);
    $angkatan = htmlspecialchars($_POST['angkatan']);
    $status = ($_POST['status'] == 'Aktif') ? 'true' : 'false';
    $keahlian_dipilih = isset($_POST['keahlian']) ? $_POST['keahlian'] : [];

    pg_query($conn, "BEGIN");
    try {
        $queryMhs = "INSERT INTO mhs_segeeks (nim_mhs, nama_mhs, email_mhs, prodi_mhs, angkatan_mhs, status) 
                     VALUES ($1, $2, $3, $4, $5, $6) RETURNING id_mhs";
        $resultMhs = pg_query_params($conn, $queryMhs, array($nim, $nama, $email, $prodi, $angkatan, $status));
        
        if (!$resultMhs) throw new Exception("Gagal simpan data mahasiswa.");
        $row = pg_fetch_assoc($resultMhs);
        $id_mhs_baru = $row['id_mhs'];

        foreach ($keahlian_dipilih as $id_keahlian) {
            $querySkill = "INSERT INTO mhs_menguasai_keahlian (id_mhs, id_keahlian) VALUES ($1, $2)";
            pg_query_params($conn, $querySkill, array($id_mhs_baru, $id_keahlian));
        }

        pg_query($conn, "COMMIT");
        echo "<script>alert('Mahasiswa berhasil ditambahkan!'); window.location='kelola_mhs.php';</script>";

    } catch (Exception $e) {
        pg_query($conn, "ROLLBACK");
        echo "<script>alert('Gagal: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Mahasiswa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleForm.css">
</head>
<body class="p-4">

    <div class="content-area container">
        <h1 class="mb-4 fw-bold text-center">Tambah Mahasiswa</h1>

        <form action="" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label class="form-label text-white">NIM</label>
                <input type="number" name="nim" class="form-control" placeholder="Masukkan NIM" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" placeholder="Masukkan Nama" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Masukkan Email" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Program Studi</label>
                <select name="prodi" class="form-select" required>
                    <option value="">Pilih Program Studi</option>
                    <option value="Sistem Informasi Bisnis">Sistem Informasi Bisnis</option>
                    <option value="Teknik Informatika">Teknik Informatika</option>
                    <option value="Pengembangan Perangkat Lunak Situs">Pengembangan Perangkat Lunak Situs</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Angkatan</label>
                <input type="number" name="angkatan" class="form-control" placeholder="Masukkan Angkatan" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Status</label>
                <select name="status" class="form-select">
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label text-white">Keahlian (Centang yang sesuai):</label>
                <div class="border p-2 rounded" style="max-height: 200px; overflow-y: scroll;">
                    <?php while($skill = pg_fetch_assoc($rSkills)): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="keahlian[]" value="<?= $skill['id_keahlian']; ?>">
                            <label class="form-check-label text-white"> <?= $skill['nama_keahlian']; ?> </label>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                <a href="kelola_mhs.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>