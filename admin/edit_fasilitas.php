<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require '../config.php';

// Cek apakah ID fasilitas tersedia
if (!isset($_GET['id'])) {
    header("Location: kelola_fasilitas.php");
    exit;
}

$id = $_GET['id'];

// Ambil data fasilitas saat ini
$qData = "SELECT * FROM fasilitas WHERE id_fasilitas = $1";
$rData = pg_query_params($conn, $qData, [$id]);
$data = pg_fetch_assoc($rData);

// Cek jika data tidak ditemukan
if (!$data) {
    echo "Fasilitas tidak ditemukan.";
    exit;
}

// Ambil jenis fasilitas
$qJenis = "SELECT * FROM jenis_fasilitas ORDER BY id_jenisfasilitas";
$rJenis = pg_query($conn, $qJenis);

if (isset($_POST['submit'])) {
    $idJenis = $_POST['id_jenisfasilitas'];
    $nama = $_POST['nama_fasilitas'];
    $isi = $_POST['isi_fasilitas'];

    // 1. Definisikan gambar lama
    $gambarLama = $data['url_gambar_fasilitas']; 
    $gambarBaru = $gambarLama; // Default: tetap gambar lama

    // === HANDLE UPLOAD GAMBAR BARU ===
    if (!empty($_FILES['gambar']['name'])) {

        $folder = '../img/fasilitas/';  // folder penyimpanan
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true); // Pastikan folder ada
        }

        $fileInfo = pathinfo($_FILES['gambar']['name']);
        $tipeFile = strtolower($fileInfo['extension']);
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        // Validasi Tipe File (Sisi Server)
        if (!in_array($tipeFile, $allowedTypes)) {
            echo "<script>alert('Gagal: Hanya file JPG, JPEG, PNG & GIF yang diizinkan!');</script>";
        } else {
            // Generate nama unik untuk menghindari konflik
            $namaFile = uniqid() . "." . $tipeFile; 
            $pathSimpan = $folder . $namaFile;

            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $pathSimpan)) {
                
                // Simpan path relative baru ke database
                $gambarBaru = "img/fasilitas/" . $namaFile;
                
                // --- LOGIKA PENGHAPUSAN GAMBAR LAMA DI FOLDER ---
                
                // Pastikan gambar lama tidak kosong, bukan gambar default, dan benar-benar file
                if (!empty($gambarLama) && $gambarLama != 'img/default.jpg') {
                    $pathLama = '../' . $gambarLama; 
                    
                    if (file_exists($pathLama)) {
                        // Hapus file fisik gambar lama
                        unlink($pathLama); 
                    }
                }
                // ----------------------------------------------------

            } else {
                 echo "<script>alert('Gagal mengunggah file. Cek izin folder server!');</script>";
            }
        }
    }

    // Query UPDATE database
    $qUpdate = "UPDATE fasilitas SET
                    id_jenisfasilitas = $1,
                    nama_fasilitas = $2,
                    isi_fasilitas = $3,
                    url_gambar_fasilitas = $4
                WHERE id_fasilitas = $5";

    $result = pg_query_params($conn, $qUpdate, [
        $idJenis, $nama, $isi, $gambarBaru, $id
    ]);

    if ($result) {
        // Redireksi jika update berhasil
        header("Location: kelola_fasilitas.php");
        exit;
    } else {
        echo "Gagal memperbarui data fasilitas di database!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Fasilitas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styleForm.css">
</head>
<body class="container mt-4">

<h1 class="mb-4 fw-bold text-center">Edit Fasilitas</h1>

<form method="POST" enctype="multipart/form-data"> 
<div class="card shadow-sm p-4">
    <div class="mb-3">
        <label>Jenis Fasilitas</label>
        <select name="id_jenisfasilitas" class="form-control" required>
            <?php pg_result_seek($rJenis, 0); while ($j = pg_fetch_assoc($rJenis)) : ?>
                <option value="<?= $j['id_jenisfasilitas']; ?>"
                    <?= $j['id_jenisfasilitas'] == $data['id_jenisfasilitas'] ? 'selected' : '' ?>>
                    <?= $j['nama_jenisfasilitas']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label text-white">Nama Fasilitas</label>
        <input type="text" name="nama_fasilitas" class="form-control"
                value="<?= htmlspecialchars($data['nama_fasilitas']); ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label text-white">Isi Fasilitas</label>
        <textarea name="isi_fasilitas" class="form-control" rows="4" required><?= htmlspecialchars($data['isi_fasilitas']); ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label text-white">Upload Gambar Baru</label>
        <input type="file" name="gambar" class="form-control" accept="image/*"> 
    </div>
    
    <div class="d-flex gap-2 mt-3">
        <button name="submit" type="submit" class="btn btn-primary">Update</button>
        <a href="kelola_fasilitas.php" class="btn btn-secondary">Kembali</a>
    </div>
</div>

</form>

</body>
</html>