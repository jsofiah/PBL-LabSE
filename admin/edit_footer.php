<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id_footer = $_GET['id'] ?? 0;

    $qFooter = "SELECT * FROM vw_footer WHERE id_footer = $id_footer";
    $rFooter = pg_query($conn, $qFooter);
    $footer = pg_fetch_assoc($rFooter);

    if (!$footer) {
        echo "Data tidak ditemukan!";
        exit;
    }

    if (isset($_POST['update'])) {
        $logoLama = $_POST['logo_lama'];
        $logoBaru = $logoLama;

        if (!empty($_FILES['logo']['name'])) {

            $targetDir = "../img/";
            $filename = time() . "_" . basename($_FILES["logo"]["name"]);
            $targetFile = $targetDir . $filename;

            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFile)) {

                $oldFile = "../" . $logoLama;

                if (!empty($logoLama) && file_exists($oldFile) && is_file($oldFile)) {
                    unlink($oldFile);
                }

                $logoBaru = "img/" . $filename;
            }
        }

        $qUpdate = "
            CALL sp_update_footer(
                $id_footer,
                '$logoBaru',
                '$_POST[judul]',
                '$_POST[hari_kerja]',
                '$_POST[jam_kerja]',
                '$_POST[telepon1]',
                '$_POST[telepon2]',
                '$_POST[alamat]',
                '$_POST[email]',
                '$_POST[maps]',
                '$_POST[instagram]',
                '$_POST[youtube]',
                '$_POST[linkedin]'
            );
        ";

        pg_query($conn, $qUpdate);

        echo "
        <script>
            alert('Footer berhasil diperbarui!');
            window.location.href = 'kelola_footer.php';
        </script>
        ";
        exit;
    }

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Footer</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="p-4">
    <div class="container">
        <h1 class="mb-4 fw-bold text-center">Edit Footer</h1>

        <div class="card shadow-sm p-4">
            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3 text-center">
                    <label class="form-label fw-bold text-white">Logo saat ini:</label><br>
                    <img src="../<?= htmlspecialchars($footer['url_logo_footer']); ?>" 
                        alt="Logo" style="max-height:100px;">
                    <input type="hidden" name="logo_lama" value="<?= $footer['url_logo_footer']; ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Upload Logo Baru</label>
                    <input type="file" name="logo" class="form-control">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-white">Judul</label>
                        <input type="text" name="judul" class="form-control" placeholder="Masukkan judul footer" value="<?= $footer['judul_footer']; ?>" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label text-white">Hari Kerja</label>
                        <input type="text" name="hari_kerja" class="form-control" placeholder="Masukkan rentang hari kerja" value="<?= $footer['hari_kerja']; ?>" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label text-white">Jam Kerja</label>
                        <input type="text" name="jam_kerja" class="form-control" placeholder="Masukkan jam kerja" value="<?= $footer['jam_kerja']; ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label text-white">Telepon 1</label>
                        <input type="text" name="telepon1" class="form-control" placeholder="Masukkan nomor telepon" value="<?= $footer['no_telepon1']; ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label text-white">Telepon 2</label>
                        <input type="text" name="telepon2" class="form-control" placeholder="Masukkan nomor telepon" value="<?= $footer['no_telepon2']; ?>">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label text-white">Alamat</label>
                        <textarea name="alamat" class="form-control" placeholder="Masukkan alamat" rows="2"><?= $footer['alamat']; ?></textarea required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label text-white">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan email" value="<?= $footer['email']; ?>" required>
                    </div>

                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-white">Instagram</label>
                        <input type="text" name="instagram" class="form-control" placeholder="Masukkan link instagram" value="<?= $footer['link_instagram']; ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-white">YouTube</label>
                        <input type="text" name="youtube" class="form-control" placeholder="Masukkan link youtube" value="<?= $footer['link_youtube']; ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-white">LinkedIn</label>
                        <input type="text" name="linkedin" class="form-control" placeholder="Masukkan link linkedin" value="<?= $footer['link_linkedin']; ?>">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label text-white">Link Maps</label>
                        <textarea name="maps" class="form-control" rows="2" placeholder="Masukkan link maps" required><?= $footer['link_maps']; ?></textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="fa fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="kelola_footer.php" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

</body>
</html>
