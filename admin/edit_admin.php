<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once '../config.php';
require_once "upload_validator.php";

if (!isset($_GET['id'])) {
    header("Location: kelola_admin.php");
    exit;
}

$id = intval($_GET['id']);
$qGet = "SELECT * FROM admin_user WHERE id = $id";
$rGet = pg_query($conn, $qGet);
$data = pg_fetch_assoc($rGet);

if (!$data) {
    echo "Data tidak ditemukan!";
    exit;
}

if (isset($_POST['update'])) {
    $username = pg_escape_string($conn, $_POST['username']);
    
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
    } else {
        $password = $data['password'];
    }

    $fotoPath = $data['foto_admin'];
    
    if (!empty($_FILES['foto']['name'])) {
        $valid = validateUpload($_FILES['foto'], 2);

        if ($valid !== true) {
            echo "<script>alert('$valid'); history.back();</script>";
            exit;
        }
        $targetDir = "img/foto_admin/";
        $fileName = time() . "_" . basename($_FILES['foto']['name']);
        $targetFilePath = $targetDir . $fileName;
        $dbFilePath = "img/foto_admin/" . $fileName;
        
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        
        if (in_array(strtolower($fileType), $allowTypes)) {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFilePath)) {
                if (!empty($data['foto_admin']) && file_exists("../" . $data['foto_admin'])) {
                    unlink("../" . $data['foto_admin']);
                }
                $fotoPath = $dbFilePath;
            }
        }
    }

    $query = "CALL sp_update_admin($id, '$username', '$password', '$fotoPath')";
    $result = pg_query($conn, $query);

    if ($result) {
         echo "
        <script>
            alert('Data Admin berhasil diperbarui!');
            window.location.href = 'kelola_admin.php';
        </script>";
        exit;
    } else {
        $error = "Gagal update data: " . pg_last_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Admin - Portal LAB SE</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleForm.css">
</head>
<body class="p-4">
    <?php include 'sidebar.php'; ?>

    <div class="content-area container">
        <h1 class="mb-4 fw-bold text-center">Edit Data Admin</h1>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        
        <form action="" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label class="form-label text-white">Username</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-white">Password (Baru)</label>
                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
            </div>
            <div class="mb-3">
                <label class="form-label text-white">Foto Profil Saat Ini</label><br>
                <?php if (!empty($data['foto_admin'])): ?>
                    <img src="<?= htmlspecialchars($data['foto_admin']); ?>" width="100" class="img-thumbnail mb-2">
                <?php else: ?>
                    <p class="text-white">Belum ada foto.</p>
                <?php endif; ?>
                <input type="file" name="foto" class="form-control" accept="image/*">
                <small class="text-white">Upload foto baru untuk mengganti.</small>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" name="update" class="btn btn-primary">Simpan perubahan</button>
                <a href="kelola_admin.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>