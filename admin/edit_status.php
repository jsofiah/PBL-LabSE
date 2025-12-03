<?php
session_start();
require_once '../config.php';

$id = $_GET['id'];

$data = pg_fetch_assoc(
    pg_query_params($conn, "SELECT * FROM status_pendaftaran WHERE id_statusPendaftaran=$1", [$id])
);

if (isset($_POST['submit'])) {
    $nama = $_POST['nama_status'];

    pg_query_params($conn, "CALL sp_update_status($1,$2)", [$id, $nama]);

    header("Location: kelola_statusPendaftaran.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Status</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleForm.css"> 
</head>

<body class="p-4">

    <?php include 'sidebar.php'; ?>

    <div class="content-area container">

        <h2 class="mb-4 fw-bold text-center">Edit Status Pendaftaran</h2>

        <form method="POST">
            <div class="card shadow-sm p-4">
                
                <div class="mb-3">
                    <label class="form-label text-white">Nama Status</label>
                    <input type="text" name="nama_status" class="form-control"
                           value="<?= htmlspecialchars($data['nama_statuspendaftaran']); ?>" required>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary" name="submit">
                        <i class="fa fa-save"></i> Update
                    </button>
                    <a href="kelola_statusPendaftaran.php" class="btn btn-secondary">Kembali</a>
                </div>

            </div>
        </form>

    </div> <script src="js/sidebar.js"></script>
</body>
</html>