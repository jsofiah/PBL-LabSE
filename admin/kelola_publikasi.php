<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once '../config.php';

    // Ambil data publikasi dari view
    $qView = "SELECT * FROM vw_publikasi_dosen ORDER BY id_publikasi ASC";
    $rView = pg_query($conn, $qView);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Publikasi</title>
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
        <h2 class="mb-2">Kelola Publikasi</h2>
        <a href="tambah_publikasi.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Publikasi
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-fixed">
            <colgroup>
                <col style="width:40px;">
                <col style="width:300px;">
                <col style="width:120px;">
                <col style="width:180px;">
                <col style="width:170px;">
            </colgroup>

            <thead class="table-primary">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Judul</th>
                    <th class="text-center">Tahun</th>
                    <th class="text-center">Jenis</th>
                    <th class="text-center">Dosen</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php while($p = pg_fetch_assoc($rView)) : ?>
                <tr>
                    <td class="text-center"><?= $p['id_publikasi']; ?></td>
                    <td><?= htmlspecialchars($p['judul_publikasi']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($p['tahun_publikasi']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($p['nama_jenispublikasi']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($p['nama_dosen']); ?></td>

                    <td class="text-center">
                        <a href="edit_publikasi.php?id=<?= $p['id_publikasi']; ?>" class="btn btn-warning btn-sm">
                            <i class="fa fa-edit"></i> Edit
                        </a>

                        <a href="hapus_publikasi.php?id=<?= $p['id_publikasi']; ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin ingin menghapus publikasi ini?')">
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
