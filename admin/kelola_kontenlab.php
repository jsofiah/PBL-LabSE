<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once '../config.php';

    $qViewKonten = "SELECT * FROM vw_konten_lab ORDER BY id_konten ASC";
    $rViewKonten = pg_query($conn, $qViewKonten);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Konten Lab - Portal LAB SE</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container-fluid px-3">

    <div class="mb-4">
        <h2 class="mb-2">Kelola Konten Laboratorium</h2>
        <a href="tambah_kontenLab.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Konten
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-fixed">

            <colgroup>
                <col style="width:50px;">
                <col style="width:150px;">
                <col style="width:400px;">
                <col style="width:150px;">
            </colgroup>

            <thead class="table-primary">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Judul Konten</th>
                    <th class="text-center">Isi Konten</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php while($k = pg_fetch_assoc($rViewKonten)) : ?>
                    <tr>
                        <td class="text-center"><?= $k['id_konten']; ?></td>

                        <td><?= htmlspecialchars($k['judul_konten']); ?></td>

                        <td><?= nl2br(htmlspecialchars(substr($k['isi_konten'], 0, 80))); ?>...</td>

                        <td class="text-center">
                            <a href="edit_kontenLab.php?id=<?= $k['id_konten']; ?>" 
                               class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            <a href="hapus_kontenLab.php?id=<?= $k['id_konten']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Yakin ingin menghapus konten ini?')">
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
