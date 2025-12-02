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
    <link rel="stylesheet" href="css/styleSidebar.css">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container-fluid px-4">

    <h2 class="mb-3">Edit Status Pendaftaran</h2>

    <div class="card shadow-sm border-0" style="max-width: 500px;">
        <div class="card-body">

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Status</label>
                    <input type="text" name="nama_status" class="form-control"
                           value="<?= htmlspecialchars($data['nama_statuspendaftaran']); ?>" required>
                </div>

                <button class="btn btn-warning" name="submit">
                    <i class="fa fa-edit me-1"></i> Update
                </button>

                <a href="kelola_statusPendaftaran.php" class="btn btn-secondary">Kembali</a>
            </form>

        </div>
    </div>

</div>

</body>
</html>
