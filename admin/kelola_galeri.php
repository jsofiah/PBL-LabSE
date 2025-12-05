<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    $limit = 20;

    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $offset = ($page - 1) * $limit;

    $qTotal = "SELECT COUNT(*) FROM vw_galeri";
    $rTotal = pg_query($conn, $qTotal);
    $total_records = pg_fetch_result($rTotal, 0, 0);

    $total_pages = ceil($total_records / $limit);

    if ($page > $total_pages && $total_pages > 0) {
        $page = $total_pages;
        $offset = ($page - 1) * $limit;
    } elseif ($total_pages === 0) {
        $page = 0;
        $offset = 0;
    }

    $qGaleri = "SELECT * FROM vw_galeri 
                ORDER BY id_galeri ASC 
                LIMIT $limit OFFSET $offset";
    $rGaleri = pg_query($conn, $qGaleri);
    
    if (!$rGaleri) {
        die("Query error: " . pg_last_error($conn));
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
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
                <th class="text-center">No</th>
                <th class="text-center">Deskripsi</th>
                <th class="text-center">Gambar</th>
                <th class="text-center">Aksi</th>
            </tr>
            </thead>

            <tbody>
                <?php if (pg_num_rows($rGaleri) > 0): ?>
                    <?php $no = $offset + 1; ?>
                    <?php while ($g = pg_fetch_assoc($rGaleri)) : ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>

                        <td class="text-center"><?= htmlspecialchars($g['deskripsi_galeri']); ?></td>

                        <td class="text-center">
                            <img src="../<?= htmlspecialchars($g['url_gambar_galeri']); ?>"
                                style="width:120px; height:80px; object-fit:cover; border-radius:6px;">
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
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data galeri.</td>
                    </tr>
                <?php endif; ?>
            </tbody>


        </table>
    </div>
    <?php include 'paging.php'; ?>
</div>

<script src="js/sidebar.js"></script>
</body>
</html>