<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once '../config.php';

    $qJenis = "SELECT * FROM jenis_artikel ORDER BY id_jenisartikel ASC";
    $rJenis = pg_query($conn, $qJenis);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jenis Artikel - Portal LAB SE</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container-fluid px-3">

    <div class="mb-4">
        <h2 class="mb-2">Kelola Jenis Artikel</h2>
        <a href="tambah_jenisartikel.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Jenis Artikel
        </a>
    </div>

    <div class="table-responsive">

        <table class="table table-bordered table-striped table-fixed">

            <colgroup>
                <col style="width:30px;">
                <col style="width:150px;">
                <col style="width:100px;">
            </colgroup>

            <thead class="table-primary">
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nama Jenis Artikel</th>
                <th class="text-center">Aksi</th>
            </tr>
            </thead>

            <tbody>
                <?php $no = 1; ?>
                <?php while($j = pg_fetch_assoc($rJenis)) : ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><?= htmlspecialchars($j['nama_jenisartikel']); ?></td>
                        <td class="text-center">
                            <a href="edit_jenisartikel.php?id=<?= $j['id_jenisartikel']; ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="hapus_jenisartikel.php?id=<?= $j['id_jenisartikel']; ?>" 
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin ingin menghapus jenis artikel ini?')">
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
