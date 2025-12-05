<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    $limit = 20;

    $page_nav = isset($_GET['page_nav']) && is_numeric($_GET['page_nav']) ? (int)$_GET['page_nav'] : 1;
    $page_nav = max(1, $page_nav);

    $qTotalNav = "SELECT COUNT(*) FROM nav";
    $rTotalNav = pg_query($conn, $qTotalNav);
    $total_nav_records = pg_fetch_result($rTotalNav, 0, 0);
    $total_nav_pages = ceil($total_nav_records / $limit);

    if ($total_nav_records === 0) {
        $page_nav = 0;
        $offset_nav = 0;
    } else {
        $page_nav = min($page_nav, $total_nav_pages);
        $offset_nav = ($page_nav - 1) * $limit;
    }

    $qNav = "
        SELECT id_nav, nama_nav, url_nav 
        FROM nav 
        ORDER BY id_nav ASC 
        LIMIT $limit OFFSET $offset_nav
    ";
    $rNav = pg_query($conn, $qNav);
    if (!$rNav) {
        die("Query Navigasi error: " . pg_last_error($conn));
    }

    $page_subnav = isset($_GET['page_subnav']) && is_numeric($_GET['page_subnav']) ? (int)$_GET['page_subnav'] : 1;
    $page_subnav = max(1, $page_subnav);

    $qTotalSubnav = "SELECT COUNT(*) FROM subnav";
    $rTotalSubnav = pg_query($conn, $qTotalSubnav);
    $total_subnav_records = pg_fetch_result($rTotalSubnav, 0, 0);
    $total_subnav_pages = ceil($total_subnav_records / $limit);

    if ($total_subnav_records === 0) {
        $page_subnav = 0;
        $offset_subnav = 0;
    } else {
        $page_subnav = min($page_subnav, $total_subnav_pages);
        $offset_subnav = ($page_subnav - 1) * $limit;
    }

    $qSubnav = "
        SELECT 
            s.id_subnav, s.nama_subnav, s.url_subnav, n.nama_nav as parent_nav 
        FROM subnav s
        JOIN nav n ON s.id_nav = n.id_nav
        ORDER BY s.id_subnav ASC 
        LIMIT $limit OFFSET $offset_subnav
    ";
    $rSubnav = pg_query($conn, $qSubnav);
    if (!$rSubnav) {
        die("Query Subnavigasi error: " . pg_last_error($conn));
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Nav</title>
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
            <h2 class="mb-2">Kelola Navigasi Utama</h2>
            <a href="tambah_nav.php" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Nav</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <colgroup>
                    <col style="width:40px">
                    <col style="width:100px">
                    <col style="width:200px">
                    <col style="width:90px">
                </colgroup>
                <thead class="table-primary">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Nama Nav</th>
                    <th class="text-center">URL Nav</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (pg_num_rows($rNav) > 0) :
                    $no = $offset_nav + 1;
                    while ($nav = pg_fetch_assoc($rNav)) : 
                ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= htmlspecialchars($nav['nama_nav']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($nav['url_nav']); ?></td>
                        <td class="text-center text-nowrap">
                            <a href="edit_nav.php?id=<?= $nav['id_nav']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                            <a href="hapus_nav.php?id=<?= $nav['id_nav']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus Nav ini?')"><i class="fa fa-trash"></i> Hapus</a>
                        </td>
                    </tr>
                <?php 
                    endwhile; 
                else:
                ?>
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data Navigasi Utama yang ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            </table>
        </div>

        <?php 
            $page = $page_nav;
            $total_pages = $total_nav_pages;
            $offset = $offset_nav;
            $total_records = $total_nav_records;
            $param_name = 'page_nav';  
            $other_param = 'page_subnav=' . $page_subnav; 
            $label = 'Navigasi';

            include 'paging.php'; 
        ?>

        <div class="mt-5 mb-4">
            <h2 class="mb-2">Kelola Subnavigasi</h2>
            <a href="tambah_subnav.php" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Subnav</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <colgroup>
                    <col style="width:40px">
                    <col style="width:100px">
                    <col style="width:200px">
                    <col style="width:80px">
                    <col style="width:100px">
                </colgroup>
                <thead class="table-primary">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Nama Subnav</th>
                    <th class="text-center">URL Subnav</th>
                    <th class="text-center">Parent Nav</th>
                    <th class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                    <?php 
                    if (pg_num_rows($rSubnav) > 0) :
                        $noSub = $offset_subnav + 1; 
                        while ($sub = pg_fetch_assoc($rSubnav)) : 
                    ?>
                        <tr>
                        <td class="text-center"><?= $noSub++; ?></td>
                        <td class="text-center"><?= htmlspecialchars($sub['nama_subnav']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($sub['url_subnav']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($sub['parent_nav']); ?></td>
                        <td class="text-center text-nowrap">
                            <a href="edit_subnav.php?id=<?= $sub['id_subnav']; ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="hapus_subnav.php?id=<?= $sub['id_subnav']; ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin ingin menghapus Subnav ini?')">
                            <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                        </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data Subnavigasi yang ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php 
            $page = $page_subnav;
            $total_pages = $total_subnav_pages;
            $offset = $offset_subnav;
            $total_records = $total_subnav_records;
            $param_name = 'page_subnav';  
            $other_param = 'page_nav=' . $page_nav;  
            $label = 'Subnavigasi';

            include 'paging.php'; 
        ?>
    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>