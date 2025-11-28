<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (file_exists('../config.php')) {
    require_once '../config.php';
} elseif (file_exists('config.php')) {
    require_once 'config.php';
} else {
    die("Error: File config.php tidak ditemukan.");
}

if (isset($_POST['simpan'])) {
    $username = pg_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); 
    
    $fotoPath = "";
    
    if (!empty($_FILES['foto']['name'])) {
        $targetDir = "img/foto_admin/"; 
        
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['foto']['name']);
        $targetFilePath = $targetDir . $fileName;
        
        $dbFilePath = "img/foto_admin/" . $fileName; 
        
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        
        if (in_array(strtolower($fileType), $allowTypes)) {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFilePath)) {
                $fotoPath = $dbFilePath;
            } else {
                $error = "Gagal upload. Pastikan folder 'admin/img/foto_admin' bisa ditulis.";
            }
        } else {
            $error = "Format file harus JPG, PNG, atau GIF.";
        }
    }

    if (!isset($error)) {
        $query = "CALL sp_create_admin('$username', '$password', '$fotoPath')";
        $result = pg_query($conn, $query);

        if ($result) {
            $_SESSION['pesan'] = "Data Admin berhasil ditambahkan!";
            header("Location: kelola_admin.php");
            exit;
        } else {
            $error = "Database Error: " . pg_last_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="content-area container">
        <h3 class="mb-4">Tambah Admin Baru</h3>
        
        <?php if(isset($error)): ?>
            <div class='alert alert-danger'><?= $error ?></div>
        <?php endif; ?>
        
        <form action="" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Foto Profil</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                <a href="kelola_admin.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>