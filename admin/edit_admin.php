<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once '../config.php';

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
        $_SESSION['pesan'] = "Data Admin berhasil diperbarui!";
        header("Location: kelola_admin.php");
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
    <link rel="stylesheet" href="css/styleSidebar.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content-area container">
        <h3 class="mb-4">Edit Data Admin</h3>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        
        <form action="" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password (Baru)</label>
                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
            </div>
            <div class="mb-3">
                <label class="form-label">Foto Profil Saat Ini</label><br>
                <?php if (!empty($data['foto_admin'])): ?>
                    <img src="../<?= htmlspecialchars($data['foto_admin']); ?>" width="100" class="img-thumbnail mb-2">
                <?php else: ?>
                    <p class="text-muted">Belum ada foto.</p>
                <?php endif; ?>
                <input type="file" name="foto" class="form-control" accept="image/*">
                <small class="text-muted">Upload foto baru untuk mengganti.</small>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" name="update" class="btn btn-primary">Update</button>
                <a href="kelola_admin.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>