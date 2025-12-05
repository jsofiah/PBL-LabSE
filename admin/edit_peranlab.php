<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$id = $_GET['id'];
$q = "SELECT * FROM peran_lab WHERE id_peran = $id";
$r = pg_query($conn, $q);
$data = pg_fetch_assoc($r);

if (isset($_POST['submit'])) {
    $nama = $_POST['nama_peran'];
    $desk = $_POST['deskripsi_peran'];
    $icon = $_POST['icon'];

    $update = "CALL sp_update_peran_lab(
                $id,
                '$nama',
                '$desk',
                '$icon'
                )";

    pg_query($conn, $update);

    echo "
        <script>
            alert('Peran lab berhasil diperbarui!');
            window.location.href = 'kelola_peranLab.php';
        </script>
        ";
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Peran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleForm.css">
    <link rel="stylesheet" href="css/iconpicker.css">
</head>

<body class="p-4">
    <?php include 'sidebar.php'; ?>
    
    <div class="content-area container">
        <h1 class="mb-4 fw-bold text-center">Edit Peran Lab</h1>

        <form method="POST">
            <div class="card shadow-sm p-4">
                <label class="form-label text-white">Nama Peran</label>
                <input type="text" name="nama_peran" placeholder="Masukkan nama peran"
                    value="<?= $data['nama_peran'] ?>" class="form-control mb-3" required>

                <label class="form-label text-white">Deskripsi Peran</label>
                <textarea name="deskripsi_peran" placeholder="Masukkan deskripsi" class="form-control mb-3"
                    required><?= $data['deskripsi_peran'] ?></textarea>

                <label class="form-label text-white">Pilih Icon</label>
                <div class="icon-picker-wrapper mb-3">
                    <i class="icon-preview" id="iconPreview" style="margin-right:10px; <?= $data['icon'] ? "class='{$data['icon']}'" : '' ?>"></i>

                    <input type="text" name="icon" id="iconInput"
                        class="form-control icon-input"
                        placeholder="Klik tombol untuk memilih icon"
                        value="<?= $data['icon'] ?>"
                        readonly required>
                </div>

                <button type="button" class="btn btn-warning w-100 mb-3" id="openPickerBtn">
                    <i class="fas fa-hand-pointer"></i> Pilih Icon Font Awesome
                </button>

                <div class="selected-display <?= $data['icon'] ? 'show' : '' ?>" id="selectedDisplay">
                    <i id="selectedIcon" class="<?= $data['icon'] ?>"></i>
                    <div class="fw-bold mt-2">Icon Terpilih</div>
                    <code id="selectedCode"><?= $data['icon'] ?></code>
                </div>

                <?php include 'iconpicker_modal.php'; ?>


                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary" name="submit">Simpan perubahan</button>
                    <a href="kelola_peranLab.php" class="btn btn-secondary">Kembali</a>
                </div>
        </form>
    </div>
    <script src="js/iconpicker.js"></script>
    <script src="js/sidebar.js"></script>
</body>
</html>