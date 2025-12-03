<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

$id = intval($_GET['id']);

$q = "SELECT * FROM mhs_segeeks WHERE id_mhs = $1";
$r = pg_query_params($conn, $q, [$id]);
$mhs = pg_fetch_assoc($r);

if (!$mhs) {
    echo "<script>alert('Data tidak ditemukan'); window.location='kelola_mhs.php';</script>";
    exit;
}

$qMy = "SELECT id_keahlian FROM mhs_menguasai_keahlian WHERE id_mhs = $1";
$rMy = pg_query_params($conn, $qMy, [$id]);
$mySkills = [];
while ($row = pg_fetch_assoc($rMy)) {
    $mySkills[] = $row['id_keahlian'];
}

$qAllSkills = "SELECT * FROM bidang_keahlian ORDER BY nama_keahlian ASC";
$rAllSkills = pg_query($conn, $qAllSkills);

if (isset($_POST['update'])) {

    $nim      = htmlspecialchars($_POST['nim']);
    $nama     = htmlspecialchars($_POST['nama']);
    $email    = htmlspecialchars($_POST['email']);
    $prodi    = htmlspecialchars($_POST['prodi']);
    $angkatan = htmlspecialchars($_POST['angkatan']);
    $status   = ($_POST['status'] == 'Aktif') ? 'true' : 'false';

    $skillsDipilih = isset($_POST['keahlian']) ? $_POST['keahlian'] : [];

    pg_query($conn, "BEGIN");

    try {
        $qUpdate = "
            UPDATE mhs_segeeks 
            SET nim_mhs=$1, nama_mhs=$2, email_mhs=$3, prodi_mhs=$4, angkatan_mhs=$5, status=$6 
            WHERE id_mhs=$7
        ";
        $params = [$nim, $nama, $email, $prodi, $angkatan, $status, $id];
        $rUp = pg_query_params($conn, $qUpdate, $params);
        if (!$rUp) throw new Exception("Gagal update data mahasiswa");

        pg_query_params($conn, "DELETE FROM mhs_menguasai_keahlian WHERE id_mhs=$1", [$id]);

        foreach ($skillsDipilih as $sk) {
            pg_query_params(
                $conn,
                "INSERT INTO mhs_menguasai_keahlian (id_mhs, id_keahlian) VALUES($1, $2)",
                [$id, $sk]
            );
        }

        pg_query($conn, "COMMIT");

        echo "<script>alert('Data mahasiswa berhasil diperbarui!'); window.location='kelola_mhs.php';</script>";
        exit;

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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleForm.css">
</head>
<body class="p-4">
    <?php include 'sidebar.php'; ?>

<div class="content-area container">
    <h1 class="mb-4 fw-bold text-center">Edit Mahasiswa</h1>

    <div class="card shadow-sm p-4 form-card">

        <form method="POST">

            <div class="mb-3">
                <label class="form-label text-white">NIM</label>
                <input type="number" name="nim" class="form-control"
                    value="<?= htmlspecialchars($mhs['nim_mhs']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control"
                    value="<?= htmlspecialchars($mhs['nama_mhs']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Email</label>
                <input type="email" name="email" class="form-control"
                    value="<?= htmlspecialchars($mhs['email_mhs']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Prodi</label>
                <select name="prodi" class="form-select" required>
                    <option value="Sistem Informasi Bisnis"
                        <?= ($mhs['prodi_mhs']=="Sistem Informasi Bisnis")?"selected":""; ?>>
                        Sistem Informasi Bisnis
                    </option>
                    <option value="Teknik Informatika"
                        <?= ($mhs['prodi_mhs']=="Teknik Informatika")?"selected":""; ?>>
                        Teknik Informatika
                    </option>
                    <option value="PPLS"
                        <?= ($mhs['prodi_mhs']=="PPLS")?"selected":""; ?>>
                        PPLS
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Angkatan</label>
                <input type="number" name="angkatan" class="form-control"
                    value="<?= htmlspecialchars($mhs['angkatan_mhs']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Status</label>
                <select name="status" class="form-select">
                    <option value="Aktif" <?= ($mhs['status']=='t') ? 'selected':''; ?>>Aktif</option>
                    <option value="Tidak Aktif" <?= ($mhs['status']=='f') ? 'selected':''; ?>>Tidak Aktif</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label text-white">Keahlian</label>
                <div class="border p-2 rounded" style="max-height:220px; overflow-y: scroll;">
                    <?php while($sk = pg_fetch_assoc($rAllSkills)): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                name="keahlian[]" value="<?= $sk['id_keahlian']; ?>"
                                <?= in_array($sk['id_keahlian'], $mySkills) ? 'checked' : ''; ?>>
                            
                            <label class="form-check-label text-white">
                                <?= $sk['nama_keahlian']; ?>
                            </label>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                <a href="kelola_mhs.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>
    </div>
</div>
<script src="js/sidebar.js"></script>
</body>
</html>