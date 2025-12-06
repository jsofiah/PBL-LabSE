<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$id = $_GET['id'] ?? 0;
$id = intval($id);

$q = "SELECT * FROM footer_social WHERE id_social = $id";
$r = pg_query($conn, $q);
$data = pg_fetch_assoc($r);

if (!$data) {
    echo "<script>
            alert('Social media tidak ditemukan!');
            window.location.href = 'kelola_footer.php';
          </script>";
    exit;
}

if (isset($_POST['submit'])) {
    $nama = $_POST['nama_platform'];
    $url  = $_POST['url'];
    $icon = $_POST['icon_class'];

    $update = "
        UPDATE footer_social
        SET nama_platform = '$nama',
            url = '$url',
            icon_class = '$icon'
        WHERE id_social = $id
    ";

    pg_query($conn, $update);

    echo "<script>
            alert('Social media berhasil diperbarui!');
            window.location.href = 'kelola_footer.php';
          </script>";
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Social Media</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleForm.css">
    <link rel="stylesheet" href="css/iconpicker.css">
</head>

<body class="p-4">
    <?php include 'sidebar.php'; ?>

    <div class="content-area container">
        <h1 class="mb-4 fw-bold text-center">Edit Social Media Footer</h1>

        <form method="POST">
            <div class="card shadow-sm p-4">

                <label class="form-label text-white">Nama Platform</label>
                <input type="text" 
                    name="nama_platform" 
                    class="form-control mb-3"
                    placeholder="Masukkan nama platform"
                    value="<?= $data['nama_platform'] ?>" required>

                <label class="form-label text-white">URL</label>
                <input type="text" 
                    name="url" 
                    class="form-control mb-3"
                    placeholder="Masukkan URL lengkap https:// ..."
                    value="<?= $data['url'] ?>" required>

                <label class="form-label text-white">Pilih Icon</label>
                <div class="icon-picker-wrapper mb-3">
                    <i class="icon-preview <?= $data['icon_class'] ?>" id="iconPreview"></i>

                    <input type="text" 
                        name="icon_class" 
                        id="iconInput"
                        class="form-control icon-input"
                        placeholder="Klik tombol untuk memilih icon"
                        value="<?= $data['icon_class'] ?>"
                        readonly required>
                </div>

                <button type="button" class="btn btn-warning w-100 mb-3" id="openPickerBtn">
                    <i class="fas fa-hand-pointer"></i> Pilih Icon Font Awesome
                </button>

                <div class="selected-display <?= $data['icon_class'] ? 'show' : '' ?>" id="selectedDisplay">
                    <i id="selectedIcon" class="<?= $data['icon_class'] ?>"></i>
                    <div class="fw-bold mt-2">Icon Terpilih</div>
                    <code id="selectedCode"><?= $data['icon_class'] ?></code>
                </div>

                <?php include 'iconpicker_modal.php'; ?>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary" name="submit">Simpan perubahan</button>
                    <a href="kelola_footer.php" class="btn btn-secondary">Kembali</a>
                </div>

            </div>
        </form>

    </div>

    <script src="js/iconpicker.js"></script>
    <script src="js/sidebar.js"></script>

</body>
</html>
