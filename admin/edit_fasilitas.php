<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require '../config.php';
require_once "upload_validator.php";

$id = $_GET['id'];

$qData = "SELECT * FROM fasilitas WHERE id_fasilitas = $1";
$rData = pg_query_params($conn, $qData, [$id]);
$data = pg_fetch_assoc($rData);

$qJenis = "SELECT * FROM jenis_fasilitas ORDER BY id_jenisfasilitas";
$rJenis = pg_query($conn, $qJenis);

if (isset($_POST['submit'])) {

    $idJenis = $_POST['id_jenisfasilitas'];
    $nama = $_POST['nama_fasilitas'];
    $isi = $_POST['isi_fasilitas'];

    $gambarBaru = $data['url_gambar_fasilitas'];

    if (!empty($_FILES['gambar']['name'])) {
        $valid = validateUpload($_FILES['gambar'], 2);

        if ($valid !== true) {
            echo "<script>alert('$valid'); history.back();</script>";
            exit;
        }

        $folder = '../img/fasilitas/';
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $namaFile = uniqid() . "_" . basename($_FILES['gambar']['name']);
        $pathSimpan = $folder . $namaFile;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $pathSimpan)) {

            $oldFile = "../" . $data['url_gambar_fasilitas'];
            if ($data['url_gambar_fasilitas'] && file_exists($oldFile)) {
                unlink($oldFile);
            }

            $gambarBaru = "img/fasilitas/" . $namaFile;
        }
    }

    $qUpdate = "CALL sp_update_fasilitas(
                    $1, -- _id_fasilitas
                    $2, -- _id_jenisfasilitas
                    $3, -- _nama_fasilitas
                    $4, -- _isi_fasilitas
                    $5  -- _url_gambar_fasilitas
                );";

    $result = pg_query_params($conn, $qUpdate, [
        $id,        
        $idJenis,     
        $nama,      
        $isi,       
        $gambarBaru  
    ]);


    if ($result) {
        echo "
        <script>
            alert('Data fasilitas berhasil diperbarui!');
            window.location.href = 'kelola_fasilitas.php';
        </script>
        ";
        exit;
    } else {
        echo "Gagal memperbarui!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Fasilitas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleForm.css">
    <link rel="stylesheet" href="css/styleSidebar.css">
</head>

<body class="p-4">
    <?php include 'sidebar.php'; ?>

    <div class="content-area container">
        <h1 class="mb-4 fw-bold text-center">Edit Fasilitas</h1>

        <form method="POST" enctype="multipart/form-data">
            <div class="card shadow-sm p-4">
                <div class="mb-3">
                    <label class="text-white">Jenis Fasilitas</label>
                    <select name="id_jenisfasilitas" class="form-control" required>
                        <?php while ($j = pg_fetch_assoc($rJenis)) : ?>
                        <option value="<?= $j['id_jenisfasilitas']; ?>"
                            <?= $j['id_jenisfasilitas'] == $data['id_jenisfasilitas'] ? 'selected' : '' ?>>
                            <?= $j['nama_jenisfasilitas']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Nama Fasilitas</label>
                    <input type="text" name="nama_fasilitas" placeholder="Masukkan nama fasilitas" class="form-control"
                        value="<?= htmlspecialchars($data['nama_fasilitas']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Isi Fasilitas</label>
                    <textarea name="isi_fasilitas" placeholder="Masukkan isi fasilitas" class="form-control" rows="4"
                        required><?= htmlspecialchars($data['isi_fasilitas']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Upload Gambar Baru</label>
                    <input type="file" name="gambar" class="form-control">
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button name="submit" class="btn btn-primary">Simpan perubahan</button>
                    <a href="kelola_fasilitas.php" class="btn btn-secondary">Kembali</a>
                </div>
            </div>

        </form>
    </div>
    <script src="js/sidebar.js"></script>
</body>

</html>