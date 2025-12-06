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
    <title>Kelola Logo CTA</title>

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
            <h2 class="mb-2">Kelola Logo CTA</h2>
        </div>

        <div class="table-container">

            <div class="table-responsive">
                <table class="table modern-table">
                    <colgroup>
                        <col style="width:40px;">
                        <col style="width:200px;">
                        <col style="width:200px;">
                        <col style="width:200px;">
                        <col style="width:120px;">
                    </colgroup>
    
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Logo</th>
                            <th class="text-center">Judul</th>
                            <th class="text-center">Link</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
    
                    <tbody>
                    <?php $no = 1; ?>
                    <?php while ($logo = pg_fetch_assoc($rViewLogo)) : ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
    
                            <td class="text-center">
                                <img src="../<?= htmlspecialchars($logo['url_logo']); ?>"
                                    alt="Logo CTA"
                                    style="width:80px; height:auto; border-radius:5px;">
                            </td>
    
                            <td class="text-center"><?= htmlspecialchars($logo['judul_cta']); ?></td>
    
                            <td class="text-center">
                                <a href="<?= htmlspecialchars($logo['link_cta']); ?>" target="_blank" class="text-primary">
                                    <?= htmlspecialchars($logo['link_cta']); ?>
                                </a>
                            </td>
    
                            <td class="text-center">
                                <a href="edit_logoCTA.php?id=<?= $logo['id_logo_cta']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
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