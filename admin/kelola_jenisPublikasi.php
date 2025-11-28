<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once '../config.php';

    $qJenis = "SELECT * FROM jenis_publikasi ORDER BY id_jenispublikasi ASC";
    $rJenis = pg_query($conn, $qJenis);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jenis Publikasi</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container-fluid px-3">

    <div class="mb-4">
        <h2 class="mb-2">Kelola Jenis Publikasi</h2>
        <a href="tambah_jenispublikasi.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Jenis Publikasi
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-fixed">

            <colgroup>
                <col style="width:80px;">
                <col style="width:300px;">
                <col style="width:150px;">
            </colgroup>

            <thead class="table-primary">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Nama Jenis Publikasi</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($j = pg_fetch_assoc($rJenis)) : ?>
                    <tr>
                        <td class="text-center"><?= $j['id_jenispublikasi']; ?></td>

                        <td><?= htmlspecialchars($j['nama_jenispublikasi']); ?></td>

                        <td class="text-center">
                            <a href="edit_jenispublikasi.php?id=<?= $j['id_jenispublikasi']; ?>"
                               class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            <a href="hapus_jenispublikasi.php?id=<?= $j['id_jenispublikasi']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Yakin ingin menghapus jenis publikasi ini?')">
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
