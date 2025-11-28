<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once '../config.php';

    // AMBIL DATA PENELITIAN
    $qViewPenelitian = "SELECT * FROM vw_penelitian_dosen ORDER BY id_penelitian ASC";
    $rViewPenelitian = pg_query($conn, $qViewPenelitian);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Penelitian - Portal LAB SE</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container-fluid px-3">

    <div class="mb-4">
        <h2 class="mb-2">Kelola Penelitian Dosen</h2>
        <a href="tambah_penelitian.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Penelitian
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-fixed">

            <colgroup>
                <col style="width:40px;">
                <col style="width:300px;">
                <col style="width:100px;">
                <col style="width:230px;">
                <col style="width:150px;">
            </colgroup>

            <thead class="table-primary">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Judul Penelitian</th>
                    <th class="text-center">Tahun</th>
                    <th class="text-center">Nama Dosen</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php while($p = pg_fetch_assoc($rViewPenelitian)) : ?>
                    <tr>
                        <td class="text-center"><?= $p['id_penelitian']; ?></td>

                        <td><?= htmlspecialchars($p['judul_penelitian']); ?></td>

                        <td class="text-center"><?= htmlspecialchars($p['tahun_penelitian']); ?></td>

                        <td class="text-center"><?= htmlspecialchars($p['nama_dosen']); ?></td>

                        <td class="text-center">
                            <a href="edit_penelitian.php?id=<?= $p['id_penelitian']; ?>" 
                               class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            <a href="hapus_penelitian.php?id=<?= $p['id_penelitian']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Yakin ingin menghapus penelitian ini?')">
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
