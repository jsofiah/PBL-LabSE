<?php
session_start();
require '../config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$qAdmin = "SELECT * FROM admin_user WHERE username = $1";
$rAdmin = pg_query_params($conn, $qAdmin, [$_SESSION['username']]);
$admin = pg_fetch_assoc($rAdmin);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $foto = $admin['foto_admin'];

    if (!empty($_FILES['foto']['name'])) {

        $targetDir = "../uploads/admin/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["foto"]["name"]);
        $targetFile = $targetDir . $fileName;

        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {

            if (!empty($admin['foto_admin']) && file_exists($targetDir . $admin['foto_admin'])) {
                unlink($targetDir . $admin['foto_admin']);
            }

            move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile);
            $foto = $fileName;
        }
    }

    if (!empty($password)) {
        $passwordHash = md5($password);
    } else {
        $passwordHash = $admin['password'];
    }

    $qUpdate = "CALL sp_update_admin($1, $2, $3, $4)";
    pg_query_params($conn, $qUpdate, [
        $admin['id'],
        $username,
        $passwordHash,
        $foto
    ]);

    $_SESSION['username'] = $username;

    header("Location: profil.php?success=1");
    exit;
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleForm.css">
</head>

<body class="p-4">

<div class="container">

    <h2 class='mb-4 fw-bold text-center'>Profil Admin</h2>

    <?php if (isset($_GET['success'])) : ?>
        <div class="alert alert-success">Profil berhasil diperbarui.</div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">

            <div class="text-center mb-3">
                <img src="<?= $admin['foto_admin'] ?: 'default.png' ?>" alt="Foto Admin" class="rounded-circle" width="120" height="120" style="object-fit: cover;">
            </div>

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label text-white">Username</label>
                    <input type="text" name="username" placeholder="Masukkan username" class="form-control" value="<?= htmlspecialchars($admin['username']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Password Baru</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diganti">
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Ganti Foto</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                    <small class="text-white">Format: jpg, jpeg, png</small>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href='dashboard.php' class='btn btn-secondary'>Kembali</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>