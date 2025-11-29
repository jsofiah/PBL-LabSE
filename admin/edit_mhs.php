<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once '../config.php';

$id_mhs = intval($_GET['id']);

// 1. Ambil Data Mahasiswa
$qMhs = "SELECT * FROM mhs_segeeks WHERE id_mhs = $1";
$rMhs = pg_query_params($conn, $qMhs, array($id_mhs));
$mhs = pg_fetch_assoc($rMhs);

if (!$mhs) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='kelola_mhs.php';</script>";
    exit;
}

// 2. Ambil Keahlian yang SUDAH dimiliki (untuk mencentang checkbox nanti)
$qMySkills = "SELECT id_keahlian FROM mhs_menguasai_keahlian WHERE id_mhs = $1";
$rMySkills = pg_query_params($conn, $qMySkills, array($id_mhs));
$mySkills = [];
while ($row = pg_fetch_assoc($rMySkills)) {
    $mySkills[] = $row['id_keahlian'];
}

// 3. Ambil Semua Daftar Keahlian (Master Data)
$qAllSkills = "SELECT * FROM bidang_keahlian ORDER BY nama_keahlian ASC";
$rAllSkills = pg_query($conn, $qAllSkills);

// --- PROSES UPDATE ---
if (isset($_POST['update'])) {
    $nim = htmlspecialchars($_POST['nim']);
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $prodi = htmlspecialchars($_POST['prodi']);
    $angkatan = htmlspecialchars($_POST['angkatan']);
    $status = ($_POST['status'] == 'Aktif') ? 'true' : 'false';
    $keahlian_dipilih = isset($_POST['keahlian']) ? $_POST['keahlian'] : [];

    pg_query($conn, "BEGIN"); // Mulai Transaksi
    try {
        // A. Update Biodata
        $qUpdate = "UPDATE mhs_segeeks SET nim_mhs=$1, nama_mhs=$2, email_mhs=$3, prodi_mhs=$4, angkatan_mhs=$5, status=$6 WHERE id_mhs=$7";
        $resUpdate = pg_query_params($conn, $qUpdate, array($nim, $nama, $email, $prodi, $angkatan, $status, $id_mhs));
        if (!$resUpdate) throw new Exception("Gagal update biodata.");

        // B. Reset Keahlian (Hapus semua yg lama, insert yg baru)
        $qDelSkill = "DELETE FROM mhs_menguasai_keahlian WHERE id_mhs = $1";
        pg_query_params($conn, $qDelSkill, array($id_mhs));

        foreach ($keahlian_dipilih as $id_keahlian) {
            $qInsSkill = "INSERT INTO mhs_menguasai_keahlian (id_mhs, id_keahlian) VALUES ($1, $2)";
            pg_query_params($conn, $qInsSkill, array($id_mhs, $id_keahlian));
        }

        pg_query($conn, "COMMIT");
        echo "<script>alert('Data berhasil diperbarui!'); window.location='kelola_mhs.php';</script>";

    } catch (Exception $e) {
        pg_query($conn, "ROLLBACK");
        echo "<script>alert('Gagal Update: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Mahasiswa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="content-area container p-4">
        <h3>Edit Mahasiswa</h3>
        <br>

        <form action="" method="POST" class="bg-white p-4 border rounded">
            
            <div class="mb-3">
                <label class="form-label">NIM</label>
                <input type="number" name="nim" class="form-control" value="<?= htmlspecialchars($mhs['nim_mhs']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($mhs['nama_mhs']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($mhs['email_mhs']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Prodi</label>
                <select name="prodi" class="form-select" required>
                    <option value="Sistem Informasi Bisnis" <?= ($mhs['prodi_mhs'] == 'Sistem Informasi Bisnis') ? 'selected' : ''; ?>>Sistem Informasi Bisnis</option>
                    <option value="Teknik Informatika" <?= ($mhs['prodi_mhs'] == 'Teknik Informatika') ? 'selected' : ''; ?>>Teknik Informatika</option>
                    <option value="PPLS" <?= ($mhs['prodi_mhs'] == 'PPLS') ? 'selected' : ''; ?>>PPLS</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Angkatan</label>
                <input type="number" name="angkatan" class="form-control" value="<?= htmlspecialchars($mhs['angkatan_mhs']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="Aktif" <?= ($mhs['status'] == 't') ? 'selected' : ''; ?>>Aktif</option>
                    <option value="Tidak Aktif" <?= ($mhs['status'] == 'f') ? 'selected' : ''; ?>>Tidak Aktif</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label d-block">Keahlian:</label>
                <div class="border p-2 rounded" style="max-height: 200px; overflow-y: scroll;">
                    <?php while($skill = pg_fetch_assoc($rAllSkills)): ?>
                        <?php 
                            // Cek apakah keahlian ini sudah dimiliki mahasiswa
                            $checked = in_array($skill['id_keahlian'], $mySkills) ? 'checked' : ''; 
                        ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="keahlian[]" 
                                   value="<?= $skill['id_keahlian']; ?>" <?= $checked; ?>>
                            <label class="form-check-label"> <?= $skill['nama_keahlian']; ?> </label>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
            <a href="kelola_mhs.php" class="btn btn-secondary">Batal</a>

        </form>
    </div>

    <script src="js/sidebar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>