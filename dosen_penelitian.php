<?php
    require 'config.php';

    $qNav = "SELECT * FROM vw_nav";
    $rNav = pg_query($conn, $qNav);

    $navItems = [];
    while ($rowNav = pg_fetch_assoc($rNav)) {
        $id_nav = $rowNav['id_nav'];

        if (!isset($navItems[$id_nav])) {
            $navItems[$id_nav] = [
                'nama_nav' => $rowNav['nama_nav'],
                'url_nav'  => $rowNav['url_nav'],
                'subnav'   => []
            ];
        }

        if ($rowNav['id_subnav']) {
            $navItems[$id_nav]['subnav'][] = [
                'nama_subnav' => $rowNav['nama_subnav'],
                'url_subnav'  => $rowNav['url_subnav']
            ];
        }
    }

    $qDosen = "SELECT id_dosen, nama_dosen FROM vw_detail_dosen ORDER BY nama_dosen";
    $rDosen = pg_query($conn, $qDosen);

    while ($d = pg_fetch_assoc($rDosen)) {
        $url = "dosen_detail.php?id=" . $d['id_dosen'];
        $navItems[3]['subnav'][] = [
            'nama_subnav' => $d['nama_dosen'],
            'url_subnav'  => $url
        ];
    }

    $qLogo = "SELECT * FROM vw_logo_cta";
    $rLogo = pg_query($conn, $qLogo);
    $rowLogo = pg_fetch_assoc($rLogo);

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        header('Location: daftar_dosen.php');
        exit;
    }

    $qDosenDetail = "SELECT * FROM vw_detail_dosen WHERE id_dosen = $id";
    $rDosenDetail = pg_query($conn, $qDosenDetail);
    $rowDosen = pg_fetch_assoc($rDosenDetail);

    $emailDosen = $rowDosen['email_dosen'] ?? '';

    $qPendidikan = "SELECT * FROM vw_riwayat_pendidikan WHERE id_dosen = $id ORDER BY tahun_lulus DESC LIMIT 1";
    $rPendidikan = pg_query($conn, $qPendidikan);
    $pendidikanTerakhir = pg_fetch_assoc($rPendidikan);

    $qPenelitian = "SELECT * FROM vw_penelitian_dosen WHERE id_dosen = $id ORDER BY tahun_penelitian DESC";
    $rPenelitian = pg_query($conn, $qPenelitian);
    $default_avatar = 'img/default_dosen.png';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penelitian Dosen - Laboratorium SE</title>

    <link rel="stylesheet" href="css/styleIndex.css">
    <link rel="stylesheet" href="css/styleFooter.css">
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleDosenPenelitian.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="logo">
        <?php if ($rowLogo): ?>
            <img src="<?php echo htmlspecialchars($rowLogo['url_logo']); ?>" alt="LABSE" class="logo-img">
        <?php else: ?>
            <img src="img/logo.png" alt="LABSE" class="logo-img">
        <?php endif; ?>
    </div>

    <nav>
        <ul id="nav-list" class="nav-collapse">
            <?php foreach ($navItems as $nav): ?>
                <?php if (count($nav['subnav']) > 0): ?>
                    <li class="dropdown">
                        <a href="#" class="dropbtn" onclick="toggleDropdown(event)">
                            <?php echo htmlspecialchars($nav['nama_nav']); ?> 
                            <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="dropdown-content">
                            <div class="dropdown-scroll overflow-auto" style="max-height:250px;">
                                <?php foreach ($nav['subnav'] as $sub): ?>
                                    <a href="<?php echo htmlspecialchars($sub['url_subnav']); ?>">
                                        <?php echo htmlspecialchars($sub['nama_subnav']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($nav['url_nav']); ?>">
                            <?php echo htmlspecialchars($nav['nama_nav']); ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </nav>

    <?php if ($rowLogo): ?>
        <a href="<?php echo htmlspecialchars($rowLogo['link_cta']); ?>" class="cta-button">
            <span class="cta-text"><?php echo htmlspecialchars($rowLogo['judul_cta']); ?></span>
        </a>
    <?php endif; ?>

    <div class="hero-wrapper">
        <div class="hero-container">
            <div class="hero-frame">
                <img src="img/background_index.jpg" alt="Lab Background">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <h1 class="hero-title">PENELITIAN</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="page-container">
        <div class="sidebar-wrapper">
            <a href="dosen_detail.php?id=<?php echo $id; ?>" class="btn-menu">Profil</a>
            <a href="dosen_penelitian.php?id=<?php echo $id; ?>" class="btn-menu active">Penelitian</a>
            <a href="publikasi.php?id=<?php echo $id; ?>" class="btn-menu">Publikasi</a>
        </div>

        <div class="content-wrapper">
            <div class="d-flex align-items-center mb-4">
                <a href="dosen_detail.php" class="btn-back me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="page-title">PENELITIAN</h2>
            </div>
            <div class="dosen-profile-card">
                <div class="dosen-profile-inner">
                    <div class="dosen-photo-wrapper">
                        <img src="<?php echo htmlspecialchars(($rowDosen && $rowDosen['url_foto_dosen']) ? $rowDosen['url_foto_dosen'] : $default_avatar); ?>" class="dosen-photo">
                    </div>
                    <div class="dosen-info">
                        <p class="dosen-prodi">
                            Program Studi 
                            <?php echo htmlspecialchars($pendidikanTerakhir['jenjang'] . ' - ' . $pendidikanTerakhir['bidang_studi'] . ' ' . $pendidikanTerakhir['universitas']); ?>
                        </p>
                        <h2 class="dosen-name">
                            <?php echo htmlspecialchars($rowDosen['nama_dosen'] ?? 'Dosen Tidak Ditemukan'); ?>
                        </h2>
                        <p class="dosen-email">
                            <?php echo htmlspecialchars($emailDosen); ?>
                        </p>
                    </div>
                </div>
            </div>

            <?php if ($rPenelitian && pg_num_rows($rPenelitian) > 0): ?>
                <?php while ($pen = pg_fetch_assoc($rPenelitian)): ?>
                    <div class="research-card">
                        <div class="research-icon-wrapper">
                            <div class="icon-box">
                                <i class="bi bi-journal-text"></i>
                            </div>
                        </div>
                        <div class="research-info">
                            <h7 class="research-title">
                                <?php echo htmlspecialchars($pen['judul_penelitian']); ?>
                            </h7>
                            <div class="research-details">
                                Ketua Peneliti : <?php echo htmlspecialchars($rowDosen['nama_dosen']); ?><br>
                                Tahun : <?php echo htmlspecialchars($pen['tahun_penelitian']); ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="card card-rounded p-3">
                    Tidak ada penelitian untuk dosen ini.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="footer-container"></div>
    <script src="js/footer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
