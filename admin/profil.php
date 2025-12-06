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

$foto = $admin['foto_admin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($_FILES['foto']['name'])) {

        $targetDir = "img/foto_admin/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["foto"]["name"]);
        $targetFile = $targetDir . $fileName;

        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {

            if (!empty($admin['foto_admin']) && file_exists($admin['foto_admin'])) {
                unlink($admin['foto_admin']);
            }

            move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile);

            $foto = $targetDir . $fileName;
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

    header("Location: profil.php");
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleProfil.css">
    <link rel="stylesheet" href="css/styleSidebar.css">
</head>

<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="content-area">
        <div class="profile-container">
            
            <?php if (isset($_GET['success'])) : ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Profil berhasil diperbarui!
                </div>
            <?php endif; ?>

            <div class="profile-header">
                <h2>
                    <i class="fas fa-user-circle me-2"></i>
                    Profil Admin
                </h2>
            </div>

            <div class="profile-card">
                <div class="avatar-section">
                    <div class="avatar-wrapper">
                        <img src="<?= !empty($admin['foto_admin']) ? $admin['foto_admin'] : 'https://ui-avatars.com/api/?name='.urlencode($admin['username']).'&size=150&background=667eea&color=fff' ?>" 
                             alt="Foto Admin" 
                             class="avatar-image"
                             id="preview-avatar">
                        <label for="foto" class="avatar-badge">
                            <i class="fas fa-camera"></i>
                        </label>
                    </div>
                    <h4 class="mb-2 fw-bold"><?= htmlspecialchars($admin['username']) ?></h4>
                    <span class="admin-role">
                        <i class="fas fa-shield-alt me-1"></i>
                        Administrator
                    </span>
                </div>

                <div class="form-section">
                    <form method="POST" enctype="multipart/form-data" id="profileForm">
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-user me-2"></i>
                                Username
                            </label>
                            <div class="input-group-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" 
                                       name="username" 
                                       placeholder="Masukkan username" 
                                       class="form-control" 
                                       value="<?= htmlspecialchars($admin['username']) ?>" 
                                       required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-lock me-2"></i>
                                Password Baru
                            </label>
                            <div class="input-group-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       class="form-control" 
                                       placeholder="Kosongkan jika tidak ingin mengganti">
                                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Kosongkan jika tidak ingin mengubah password
                            </small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-image me-2"></i>
                                Foto Profil
                            </label>
                            <label for="foto" class="custom-file-upload">
                                <i class="fas fa-cloud-upload-alt d-block"></i>
                                <strong>Klik untuk upload foto</strong>
                                <p class="text-muted small mb-0 mt-1">Format: JPG, JPEG, PNG (Max 2MB)</p>
                                <p class="text-muted small mb-0" id="file-name">Belum ada file dipilih</p>
                            </label>
                            <input type="file" 
                                   name="foto" 
                                   id="foto"
                                   class="file-input-hidden" 
                                   accept="image/*">
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href='dashboard.php' class='btn btn-secondary'>
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="js/sidebar.js"></script>
    
    <script>
        document.getElementById('foto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-avatar').src = e.target.result;
                }
                reader.readAsDataURL(file);
                document.getElementById('file-name').textContent = file.name;
            }
        });

        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        setTimeout(function() {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.style.animation = 'slideUp 0.5s ease';
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
</body>
</html>