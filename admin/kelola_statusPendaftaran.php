<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once '../config.php';

$qStatus = "SELECT * FROM status_pendaftaran ORDER BY id_statusPendaftaran ASC";
$rStatus = pg_query($conn, $qStatus);

$statusList = [];
while ($row = pg_fetch_assoc($rStatus)) {
    $statusList[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Status Pendaftaran</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
</head>

<body>
<?php include 'sidebar.php'; ?>

<div class="content-area container-fluid px-4">

    <div class="mb-4">
        <h2 class="mb-3">Kelola Status Pendaftaran</h2>
        <a href="tambah_status.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus me-1"></i> Tambah Status
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center" style="width: 80px;">No</th>
                            <th>Nama Status</th>
                            <th class="text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php $no = 1; ?>
                    <?php if (count($statusList) > 0) : ?>
                        <?php foreach ($statusList as $st) : ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><?= htmlspecialchars($st['nama_statuspendaftaran']); ?></td>

                                <td class="text-center">
                                    <a href="edit_status.php?id=<?= $st['id_statuspendaftaran']; ?>"
                                    class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit me-1"></i>Edit
                                    </a>

                                    <a href="hapus_status.php?id=<?= $st['id_statuspendaftaran']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus status ini?')">
                                        <i class="fa fa-trash me-1"></i>Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data status.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>


                </table>
            </div>
        </div>
    </div>

</div>

<script src="js/sidebar.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

</body>
</html>
