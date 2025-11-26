<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once '../config.php';

    $qViewLogo = "SELECT * FROM vw_logo_cta ORDER BY id_logo_cta";
    $rViewLogo = pg_query($conn, $qViewLogo);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal LAB SE</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>
    <div class="content-area container">
        <div class="mb-4">
            <h2 class="mb-2">Kelola Logo CTA</h2>
            <a href="tambah_logoCTa.php" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Logo</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <colgroup>
                    <col style="width:40px;">
                    <col style="width:200px;">
                    <col style="width:200px;">
                    <col style="width:200px;">
                    <col style="width:120px;">
                </colgroup>

                <thead class="table-primary">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Logo</th>
                        <th class="text-center">Judul</th>
                        <th class="text-center">Link</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($logo = pg_fetch_assoc($rViewLogo)) : ?>
                        <tr>
                            <td class="text-center"><?= $logo['id_logo_cta']; ?></td>

                            <td class="text-center">
                                <img src="../<?= htmlspecialchars($logo['url_logo']); ?>"
                                    alt="Logo CTA"
                                    style="width:80px; height:auto; border-radius:5px;">
                            </td>

                            <td class="text-center"><?= htmlspecialchars($logo['judul_cta']); ?></td>

                            <td class="text-center">
                                <a href="<?= htmlspecialchars($logo['link_cta']); ?>"
                                target="_blank"
                                class="text-primary">
                                    <?= htmlspecialchars($logo['link_cta']); ?>
                                </a>
                            </td>

                            <td class="text-center">
                                <a href="edit_logoCTA.php?id=<?= $logo['id_logo_cta']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>

                                <a href="hapus_logoCTA.php?id=<?= $logo['id_logo_cta']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus Logo CTA ini?')">
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