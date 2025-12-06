<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    $qViewFooter = "SELECT * FROM vw_footer ORDER BY id_footer";
    $rViewFooter = pg_query($conn, $qViewFooter);

    $qViewSosmed = "SELECT * FROM vw_footer_social ORDER BY id_social";
    $rViewSosmed = pg_query($conn, $qViewSosmed);

    $limit = 20;
    $page_sosmed = isset($_GET['page_sosmed']) && is_numeric($_GET['page_sosmed']) 
                    ? (int)$_GET['page_sosmed'] 
                    : 1;

    $page_sosmed = max(1, $page_sosmed);

    $qTotalSosmed = "SELECT COUNT(*) FROM vw_footer_social";
    $rTotalSosmed = pg_query($conn, $qTotalSosmed);
    $total_sosmed_records = pg_fetch_result($rTotalSosmed, 0, 0);

    $total_sosmed_pages = ceil($total_sosmed_records / $limit);

    if ($total_sosmed_records == 0) {
        $page_sosmed = 0;
        $offset_sosmed = 0;
    } else {
        $page_sosmed = min($page_sosmed, $total_sosmed_pages);
        $offset_sosmed = ($page_sosmed - 1) * $limit;
    }

    $qViewSosmed = "
        SELECT * FROM vw_footer_social
        ORDER BY id_social ASC
        LIMIT $limit OFFSET $offset_sosmed
    ";
    $rViewSosmed = pg_query($conn, $qViewSosmed);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal LAB SE</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content-area container-fluid px-3">

        <div class="mb-4">
            <h2 class="mb-2">Kelola Footer</h2>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table modern-table">
                    <colgroup>
                        <col style="width:40px;">
                        <col style="width:190px;">
                        <col style="width:130px;">
                        <col style="width:130px;">
                        <col style="width:130px;">
                        <col style="width:170px;">
                        <col style="width:170px;">
                        <col style="width:200px;">
                        <col style="width:200px;">
                        <col style="width:200px;">
                        <col style="width:100px;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Logo</th>
                            <th class="text-center">Judul</th>
                            <th class="text-center">Hari Kerja</th>
                            <th class="text-center">Jam Kerja</th>
                            <th class="text-center">Telepon 1</th>
                            <th class="text-center">Telepon 2</th>
                            <th class="text-center">Alamat</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Maps</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php if(pg_num_rows($rViewFooter) > 0): ?>
                        <?php while($footer = pg_fetch_assoc($rViewFooter)) : ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td class="text-center"><?= htmlspecialchars($footer['url_logo_footer']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($footer['judul_footer']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($footer['hari_kerja']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($footer['jam_kerja']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($footer['no_telepon1']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($footer['no_telepon2']); ?></td>
                                <td class="text-center text-truncate" style="max-width:180px;" title="<?= htmlspecialchars($footer['alamat']); ?>">
                                    <?= htmlspecialchars($footer['alamat']); ?>
                                </td>
                                <td class="text-center"><?= htmlspecialchars($footer['email']); ?></td>
                                <td class="text-center text-truncate">
                                    <a href="<?= htmlspecialchars($footer['link_maps']); ?>" target="_blank">
                                        <?= htmlspecialchars($footer['link_maps']); ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="edit_footer.php?id=<?= $footer['id_footer']; ?>" class="btn btn-action btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11">
                                <div class="empty-state text-center">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <h5>Tidak Ada Data Footer</h5>
                                    <p class="text-muted">Data footer belum tersedia.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-4 mt-4">
            <h2 class="mb-2">Kelola Sosial Media</h2>
            <a href="tambah_sosmed.php" class="btn btn-success"><i class="fa fa-plus"></i> Tambah Sosial Media</a>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table modern-table">
                    <colgroup>
                        <col style="width:40px;">
                        <col style="width:150px;">
                        <col style="width:130px;">
                        <col style="width:170px;">
                        <col style="width:100px;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama Platform</th>
                            <th class="text-center">Icon</th>
                            <th class="text-center">Link</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no = $offset_sosmed + 1; ?>
                    <?php if(pg_num_rows($rViewSosmed) > 0): ?>
                        <?php while($sosmed = pg_fetch_assoc($rViewSosmed)) : ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td class="text-center"><?= htmlspecialchars($sosmed['nama_platform']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($sosmed['icon_class']); ?></td>
                                <td class="text-center text-truncate" style="max-width:200px;" title="<?= htmlspecialchars($sosmed['url']); ?>">
                                    <a href="<?= htmlspecialchars($sosmed['url']); ?>" target="_blank">
                                        <?= htmlspecialchars($sosmed['url']); ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="edit_sosmed.php?id=<?= $sosmed['id_social']; ?>" class="btn btn-action btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a href="hapus_sosmed.php?id=<?= $sosmed['id_social']; ?>" class="btn btn-action btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus Sosial Media ini?')">
                                        <i class="fa fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state text-center">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <h5>Tidak Ada Data Sosial Media</h5>
                                    <p class="text-muted">Data sosial media tidak ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php
                $page = $page_sosmed;
                $total_pages = $total_sosmed_pages;
                $offset = $offset_sosmed;
                $total_records = $total_sosmed_records;
                $param_name = 'page_sosmed';
                $label = 'Sosial Media';
                include 'paging.php';
            ?>
        </div>
    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>