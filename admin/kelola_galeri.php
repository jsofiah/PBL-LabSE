<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    // NAV
    $qViewNav = "SELECT * FROM vw_nav ORDER BY id_nav, id_subnav";
    $rViewNav = pg_query($conn, $qViewNav);

    $navs = [];
    while ($row = pg_fetch_assoc($rViewNav)) {
        $idNav = $row['id_nav'];
        if (!isset($navs[$idNav])) {
            $navs[$idNav] = [
                'id_nav' => $row['id_nav'],
                'nama_nav' => $row['nama_nav'],
                'url_nav' => $row['url_nav']
            ];
        }
    }

    // GALERI
    $qGaleri = pg_query($conn, "SELECT * FROM galeri ORDER BY id_galeri ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
</head>

<body>
<?php include 'sidebar.php'; ?>

<div class="content-area container">

    <div class="mb-4">
        <h2 class="mb-2">Kelola Galeri</h2>
        <a href="tambah_galeri.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Gambar
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-fixed">
            <colgroup>
                <col style="width:50px">
                <col style="width:200px">
                <col style="width:200px">
                <col style="width:100px">
            </colgroup>

            <thead class="table-primary">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Deskripsi</th>
                    <th class="text-center">Gambar</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($g = pg_fetch_assoc($qGaleri)) : ?>
                <tr>
                    <td class="text-center"><?= $g['id_galeri']; ?></td>

                    <td class="text-center"><?= htmlspecialchars($g['deskripsi_galeri']); ?></td>

                    <td class="text-center">
                        <img src="../<?= htmlspecialchars($g['url_gambar_galeri']); ?>" 
                             style="width:120px; border-radius:6px;">
                    </td>

                    <td class="text-center">
                        <a href="edit_galeri.php?id=<?= $g['id_galeri']; ?>" class="btn btn-warning btn-sm">
                            <i class="fa fa-edit"></i> Edit
                        </a>

                        <a href="hapus_galeri.php?id=<?= $g['id_galeri']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin ingin menghapus gambar ini?')">
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
