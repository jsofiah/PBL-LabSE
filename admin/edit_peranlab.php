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
</head>

<body class="p-4">
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

                <label class="form-label text-white">Icon (text)</label>
                <input type="text" name="icon" placeholder="Masukkan icon" value="<?= $data['icon'] ?>"
                    class="form-control mb-3">
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary" name="submit">Simpan perubahan</button>
                    <a href="kelola_peranLab.php" class="btn btn-secondary">Kembali</a>
                </div>

        </form>

    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>