<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$qView = "SELECT * FROM roadmap ORDER BY id_roadmap ASC";
$rView = pg_query($conn, $qView);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Roadmap</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/styleSidebar.css">
<link rel="stylesheet" href="css/styleTabel.css">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container">
    <div class="mb-4">
        <h2 class="fw-bold">Kelola Roadmap</h2>
        <a href="tambah_roadmap.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Roadmap
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-fixed">

            <colgroup>
                <col style="width:60px;">
                <col style="width:200px;">
                <col style="width:450px;">
                <col style="width:160px;">
                <col style="width:200px;">
            </colgroup>

            <thead class="table-primary">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Judul</th>
                    <th class="text-center">Deskripsi</th>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = pg_fetch_assoc($rView)) : ?>
                    <tr>
                        <td class="text-center"><?= $row['id_roadmap']; ?></td>
                        <td><?= htmlspecialchars($row['judul_roadmap']); ?></td>
                        <td><?= nl2br(htmlspecialchars($row['deskripsi_roadmap'])); ?></td>
                        <td class="text-center"><?= $row['tanggal_roadmap']; ?></td>

                        <td class="text-center">
                            <a href="edit_roadmap.php?id=<?= $row['id_roadmap']; ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            <a href="hapus_roadmap.php?id=<?= $row['id_roadmap']; ?>"
                               onclick="return confirm('Yakin ingin menghapus roadmap ini?')"
                               class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>

        </table>
    </div>

</div>

<script src="js/sidebar.js"></script>

</body>
</html>
