<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

if (isset($_POST['submit'])) {
    $nama = $_POST['nama_platform'];
    $icon = $_POST['icon_class'];
    $url  = $_POST['url'];

    $q = "INSERT INTO footer_social (nama_platform, icon_class, url) 
          VALUES ('$nama', '$icon', '$url')";
    pg_query($conn, $q);

    header("Location: kelola_footer.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Social Media</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleForm.css">
    <link rel="stylesheet" href="css/iconpicker.css">
</head>

<body class="content-area container">
    <?php include 'sidebar.php'; ?>

    <h1 class="mb-4 fw-bold text-center">Tambah Social Media Footer</h1>

    <form method="POST">
        <div class="card shadow-sm p-4">

        <label class="form-label text-white">Nama Platform</label>
        <input type="text" name="nama_platform" 
               class="form-control mb-3" 
               placeholder="Contoh: Instagram" required>

        <label class="form-label text-white">URL</label>
        <input type="text" name="url" 
               class="form-control mb-3" 
               placeholder="Masukkan URL lengkap https:// ..." required>

        <label class="form-label text-white">Pilih Icon</label>
        <div class="icon-picker-wrapper mb-3">
            <i class="icon-preview" id="iconPreview"></i>
            <input type="text" name="icon_class" id="iconInput" 
                   class="form-control icon-input"
                   placeholder="Klik tombol untuk memilih icon" 
                   readonly required>
        </div>

        <button type="button" class="btn btn-warning w-100 mb-3" id="openPickerBtn">
            <i class="fas fa-hand-pointer"></i> Pilih Icon Font Awesome
        </button>

        <div class="selected-display" id="selectedDisplay">
            <i id="selectedIcon"></i>
            <div class="fw-bold mt-2">Icon Terpilih</div>
            <code id="selectedCode"></code>
        </div>

        <?php include 'iconpicker_modal.php'; ?>

        <div class="d-flex gap-2 mt-3">
            <button class="btn btn-primary" name="submit">Simpan</button>
            <a href="kelola_footer.php" class="btn btn-secondary">Kembali</a>
        </div>
    </form>

    <script src="js/iconpicker.js"></script>
    <script src="js/sidebar.js"></script>
</body>
</html>
