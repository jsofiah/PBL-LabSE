<?php
    require 'config.php';

    $qNav = "SELECT * FROM vw_nav";
    $rNav = pg_query($conn, $qNav);

    $navItems = [];
    if ($rNav) {
        while ($rowNav = pg_fetch_assoc($rNav)) {
            $id_nav = $rowNav['id_nav'];
            if (!isset($navItems[$id_nav])) {
                $navItems[$id_nav] = [
                    'nama_nav' => $rowNav['nama_nav'],
                    'url_nav'  => $rowNav['url_nav'],
                    'subnav'   => []
                ];
            }
            if (!empty($rowNav['id_subnav'])) {
                $navItems[$id_nav]['subnav'][] = [
                    'nama_subnav' => $rowNav['nama_subnav'],
                    'url_subnav'  => $rowNav['url_subnav']
                ];
            }
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
    $profil = pg_fetch_assoc($rDosenDetail);

    if (!$profil) {
        header('Location: daftar_dosen.php');
        exit;
    }

    $emailDosen = $profil['email_dosen'] ?? '';
    $default_avatar = 'img/default_dosen.png';

    $qPendidikanTerakhir = "SELECT * FROM vw_riwayat_pendidikan WHERE id_dosen = $id ORDER BY tahun_lulus DESC LIMIT 1";
    $rPendidikanTerakhir = pg_query($conn, $qPendidikanTerakhir);
    $pendidikanTerakhir = pg_fetch_assoc($rPendidikanTerakhir);

    $qPenelitian = "SELECT * FROM vw_penelitian_dosen WHERE id_dosen = $id ORDER BY tahun_penelitian DESC";
    $rPenelitian = pg_query($conn, $qPenelitian);

    function h($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penelitian Dosen - <?= h($profil['nama_dosen']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleFooter.css">
    <link rel="stylesheet" href="css/styleDosenPenelitian.css">
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
                <?php if (!empty($nav['subnav'])): ?>
                    <li class="dropdown">
                        <a href="<?= h($nav['url_nav']) ?>" class="dropbtn"><?= h($nav['nama_nav']) ?> <i class="bi bi-chevron-down"></i></a>
                        <div class="dropdown-content">
                            <div class="dropdown-scroll overflow-auto" style="max-height: 250px;">
                                <?php foreach ($nav['subnav'] as $sub): ?>
                                    <a href="<?= h($sub['url_subnav']) ?>"><?= h($sub['nama_subnav']) ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="<?= h($nav['url_nav']) ?>"><?= h($nav['nama_nav']) ?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </nav>

    <?php if($rowLogo): ?>
        <a href="<?php echo htmlspecialchars($rowLogo['link_cta']); ?>" class="cta-button">
            <span class="cta-text"><?php echo htmlspecialchars($rowLogo['judul_cta']); ?></span>
        </a>
    <?php endif; ?>

    <div class="hero-wrapper">
        <div class="hero-container">
            <div class="hero-frame">
                <img src="img/bgpenelitian.jpg" alt="Penelitian Background">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <h1 class="hero-title">PENELITIAN</h1>
                </div>
            </div>
        </div>
        <div class="container my-3" style="margin-left: 0px; padding-left: 0px; margin-right: 0px; padding-right: 0px;">
            <div class="row" style="width: 1350px;">
                <div class="sidebar-wrapper">
                    <a href="dosen_detail.php?id=<?php echo $id; ?>" class="btn-menu">Profil</a>
                    <a href="dosen_penelitian.php?id=<?php echo $id; ?>" class="btn-menu active">Penelitian</a>
                    <a href="dosen_publikasi.php?id=<?php echo $id; ?>" class="btn-menu">Publikasi</a>
                </div>
            
                <div class="col-lg-9">
                    <div class="d-flex align-items-center mb-4">
                        <a href="dosen_detail.php?id=<?php echo $id; ?>" class="btn-back me-3"><i class="bi bi-arrow-left"></i></a>
                        <h2 class="fw-bold mb-0">PENELITIAN</h2>
                    </div>
        
                    <div class="profil-card mb-4">
                        <div class="d-flex">
                            <div class="profil-img-wrapper me-4">
                                <img class="profil-img"
                                    src="<?= h($profil['url_foto_dosen'] ?: 'img/avatar-placeholder.png') ?>"
                                    alt="<?= h($profil['nama_dosen']) ?>">
                            </div>
    
                            <div class="profil-text">
                                <?php if (!empty($pendidikanTerakhir)): ?>
                                    <div class="text-primary fw-semibold">
                                        <?= h($pendidikanTerakhir['jenjang']) ?> – <?= h($pendidikanTerakhir['universitas']) ?>
                                    </div>
                                <?php endif; ?>
    
                                <h4 class="mt-1"><?= h($profil['nama_dosen']) ?></h4>
    
                                <?php if (!empty($profil['email_dosen'])): ?>
                                    <p><?= h($profil['email_dosen']) ?></p>
                                <?php endif; ?>
    
                                <?php if (!empty($profil['jabatan_lab'])): ?>
                                    <p><strong>Jabatan Lab:</strong> <?= h($profil['jabatan_lab']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
        
                    <div class="section-card">
                        <?php if($rPenelitian && pg_num_rows($rPenelitian) > 0): ?>
                            <?php while($pen = pg_fetch_assoc($rPenelitian)): ?>
                                <div class="research-card">
                                    <div class="research-icon-wrapper">
                                        <div class="icon-box"><i class="fa-regular fa-file-lines"></i></div>
                                    </div>
                                    <div class="research-info">
                                        <h5 class="research-title"><?php echo htmlspecialchars($pen['judul_penelitian']); ?></h5>
                                        <div class="research-details">
                                            Ketua Peneliti : <?= htmlspecialchars($profil['nama_dosen']); ?><br>
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
            </div>
        </div>
    </div>

    <div id="footer-container"></div>
    <script src="js/footer.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dropdown.js"></script>
</body>
</html>