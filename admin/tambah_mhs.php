<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once '../config.php';

// Ambil data keahlian untuk checkbox
$qSkills = "SELECT * FROM bidang_keahlian ORDER BY nama_keahlian ASC";
$rSkills = pg_query($conn, $qSkills);

// Proses Simpan Data
if (isset($_POST['submit'])) {
    $nim = htmlspecialchars($_POST['nim']);
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $prodi = htmlspecialchars($_POST['prodi']);
    $angkatan = htmlspecialchars($_POST['angkatan']);
    $status = ($_POST['status'] == 'Aktif') ? 'true' : 'false';
    $keahlian_dipilih = isset($_POST['keahlian']) ? $_POST['keahlian'] : [];

    // Mulai simpan
    pg_query($conn, "BEGIN");
    try {
        // 1. Simpan Data Diri
        $queryMhs = "INSERT INTO mhs_segeeks (nim_mhs, nama_mhs, email_mhs, prodi_mhs, angkatan_mhs, status) 
                     VALUES ($1, $2, $3, $4, $5, $6) RETURNING id_mhs";
        $resultMhs = pg_query_params($conn, $queryMhs, array($nim, $nama, $email, $prodi, $angkatan, $status));
        
        if (!$resultMhs) throw new Exception("Gagal simpan data mahasiswa.");
        $row = pg_fetch_assoc($resultMhs);
        $id_mhs_baru = $row['id_mhs'];

        // 2. Simpan Keahlian (Looping)
        foreach ($keahlian_dipilih as $id_keahlian) {
            $querySkill = "INSERT INTO mhs_menguasai_keahlian (id_mhs, id_keahlian) VALUES ($1, $2)";
            pg_query_params($conn, $querySkill, array($id_mhs_baru, $id_keahlian));
        }

        pg_query($conn, "COMMIT");
        echo "<script>alert('Berhasil disimpan!'); window.location='kelola_mhs.php';</script>";

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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="content-area container p-4">
        <h3>Tambah Mahasiswa</h3>
        <br>

        <form action="" method="POST" class="bg-white p-4 border rounded">
            
            <div class="mb-3">
                <label class="form-label">NIM</label>
                <input type="number" name="nim" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Prodi</label>
                <select name="prodi" class="form-select" required>
                    <option value="">- Pilih -</option>
                    <option value="Sistem Informasi Bisnis">Sistem Informasi Bisnis</option>
                    <option value="Teknik Informatika">Teknik Informatika</option>
                    <option value="PPLS">PPLS</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Angkatan</label>
                <input type="number" name="angkatan" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label d-block">Keahlian (Centang yang sesuai):</label>
                <div class="border p-2 rounded" style="max-height: 200px; overflow-y: scroll;">
                    <?php while($skill = pg_fetch_assoc($rSkills)): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="keahlian[]" value="<?= $skill['id_keahlian']; ?>">
                            <label class="form-check-label"> <?= $skill['nama_keahlian']; ?> </label>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            <a href="kelola_mhs.php" class="btn btn-secondary">Batal</a>

        </form>
    </div>

    <script src="js/sidebar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>