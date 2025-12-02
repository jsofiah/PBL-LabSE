<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    $qViewNav = "SELECT * FROM vw_nav ORDER BY id_nav, id_subnav";
    $rViewNav = pg_query($conn, $qViewNav);

    $navs = [];
    $subnavs = [];

    while ($row = pg_fetch_assoc($rViewNav)) {
        $idNav = $row['id_nav'];

        if (!isset($navs[$idNav])) {
            $navs[$idNav] = [
                'id_nav' => $row['id_nav'],
                'nama_nav' => $row['nama_nav'],
                'url_nav' => $row['url_nav']
            ];
        }

        if (!empty($row['id_subnav'])) {
            $subnavs[] = [
                'id_subnav' => $row['id_subnav'],
                'nama_subnav' => $row['nama_subnav'],
                'url_subnav' => $row['url_subnav'],
                'parent_nav' => $row['nama_nav']
            ];
        }
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
            <h2 class="mb-2">Kelola Nav</h2>
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
                        <th class="text-center">ID</th>
                        <th class="text-center">Nama Nav</th>
                        <th class="text-center">URL Nav</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($navs as $nav) : ?>
                        <tr>
                            <td class="text-center"><?= $nav['id_nav']; ?></td>
                            <td class="text-center"><?= htmlspecialchars($nav['nama_nav']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($nav['url_nav']); ?></td>
                            <td class="text-center">
                                <a href="edit_nav.php?id=<?= $nav['id_nav']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                <a href="hapus_nav.php?id=<?= $nav['id_nav']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus Nav ini?')"><i class="fa fa-trash"></i> Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-5 mb-4">
            <h2 class="mb-2">Kelola Subnav</h2>
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
                        <th class="text-center">ID</th>
                        <th class="text-center">Nama Subnav</th>
                        <th class="text-center">URL Subnav</th>
                        <th class="text-center">Parent Nav</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($subnavs as $sub) : ?>
                        <tr>
                            <td class="text-center"><?= $sub['id_subnav']; ?></td>
                            <td class="text-center"><?= htmlspecialchars($sub['nama_subnav']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($sub['url_subnav']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($sub['parent_nav']); ?></td>
                            <td class="text-center">
                                <a href="edit_subnav.php?id=<?= $sub['id_subnav']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                <a href="hapus_subnav.php?id=<?= $sub['id_subnav']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus Subnav ini?')"><i class="fa fa-trash"></i> Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="js/sidebar.js"></script>
</body>