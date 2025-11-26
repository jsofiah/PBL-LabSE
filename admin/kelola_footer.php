<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    $qViewFooter = "SELECT * FROM vw_footer ORDER BY id_footer";
    $rViewFooter = pg_query($conn, $qViewFooter);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal LAB SE</title>
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
            <h2 class="mb-2">Kelola Footer</h2>
            <a href="tambah_footer.php" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Footer</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
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
                    <col style="width:180px;">
                    <col style="width:180px;">
                    <col style="width:180px;">
                    <col style="width:180px;">
                    <col style="width:200px;">
                </colgroup>
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Logo</th>
                        <th class="text-center">Judul</th>
                        <th class="text-center">Hari Kerja</th>
                        <th class="text-center">Jam Kerja</th>
                        <th class="text-center">Telepon 1</th>
                        <th class="text-center">Telepon 2</th>
                        <th class="text-center">Alamat</th>
                        <th class="text-center">Email</th>
                        <th class="text-center">Instagram</th>
                        <th class="text-center">YouTube</th>
                        <th class="text-center">LinkedIn</th>
                        <th class="text-center">Maps</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($footer = pg_fetch_assoc($rViewFooter)) : ?>
                        <tr>
                            <td class="text-center"><?= $footer['id_footer']; ?></td>
                            <td class="text-center"><?= htmlspecialchars($footer['url_logo_footer']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($footer['judul_footer']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($footer['hari_kerja']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($footer['jam_kerja']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($footer['no_telepon1']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($footer['no_telepon2']); ?></td>

                            <td class="text-truncate text-center" style="max-width:180px;" title="<?= htmlspecialchars($footer['alamat']); ?>">
                                <?= htmlspecialchars($footer['alamat']); ?>
                            </td>

                            <td class="text-center"><?= htmlspecialchars($footer['email']); ?></td>

                            <td class="text-center">
                                <a href="<?= htmlspecialchars($footer['link_instagram']); ?>" 
                                target="_blank" 
                                class="link-truncate"
                                title="<?= htmlspecialchars($footer['link_instagram']); ?>">
                                <?= htmlspecialchars($footer['link_instagram']); ?>
                                </a>
                            </td>

                            <td class="text-center">
                                <a href="<?= htmlspecialchars($footer['link_youtube']); ?>" 
                                target="_blank" 
                                class="link-truncate"
                                title="<?= htmlspecialchars($footer['link_youtube']); ?>">
                                <?= htmlspecialchars($footer['link_youtube']); ?>
                                </a>
                            </td>

                            <td class="text-center">
                                <a href="<?= htmlspecialchars($footer['link_linkedin']); ?>" 
                                target="_blank" 
                                class="link-truncate"
                                title="<?= htmlspecialchars($footer['link_linkedin']); ?>">
                                <?= htmlspecialchars($footer['link_linkedin']); ?>
                                </a>
                            </td>

                            <td class="text-center">
                                <a href="<?= htmlspecialchars($footer['link_maps']); ?>" target="_blank" 
                                target="_blank"
                                class="link-truncate"
                                title="<?= htmlspecialchars($footer['link_maps']); ?>">
                                    <?= htmlspecialchars($footer['link_maps']); ?>
                                </a>
                            </td>

                            <td class="text-center">
                                <a href="edit_footer.php?id=<?= $footer['id_footer']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="hapus_footer.php?id=<?= $footer['id_footer']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin ingin menghapus Footer ini?')">
                                    <i class="fa fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>