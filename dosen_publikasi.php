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
        if (isset($navItems[3])) {
            $navItems[3]['subnav'][] = [
                'nama_subnav' => $d['nama_dosen'],
                'url_subnav'  => $url
            ];
        }
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
    
    $qPendidikanTerakhir = "SELECT * FROM vw_riwayat_pendidikan WHERE id_dosen = $id ORDER BY tahun_lulus DESC LIMIT 1";
    $rPendidikanTerakhir = pg_query($conn, $qPendidikanTerakhir);
    $pendidikanTerakhir = pg_fetch_assoc($rPendidikanTerakhir);

    function buildUrl($key, $val) {
        $params = $_GET;
        $params[$key] = $val;
        return '?' . http_build_query($params);
    }

    $qJenis = "SELECT DISTINCT nama_jenispublikasi FROM vw_publikasi_dosen WHERE id_dosen = $id ORDER BY nama_jenispublikasi ASC";
    $rJenis = pg_query($conn, $qJenis);

    $publikasiData = []; 

    if ($rJenis) {
        while ($rowJenis = pg_fetch_assoc($rJenis)) {
            $jenis = $rowJenis['nama_jenispublikasi'];
            if (empty($jenis)) continue;
            
            $paramKey = 'page_' . md5($jenis); 
            
            $limit = 5; 
            $page = isset($_GET[$paramKey]) ? (int)$_GET[$paramKey] : 1;
            $start = ($page > 1) ? ($page * $limit) - $limit : 0;

            $qCount = "SELECT count(*) as total FROM vw_publikasi_dosen WHERE id_dosen = $id AND nama_jenispublikasi = '$jenis'";
            $rCount = pg_query($conn, $qCount);
            $totalData = pg_fetch_assoc($rCount)['total'];
            $totalPage = ceil($totalData / $limit);

            $qData = "SELECT * FROM vw_publikasi_dosen WHERE id_dosen = $id AND nama_jenispublikasi = '$jenis' ORDER BY tahun_publikasi DESC, judul_publikasi ASC LIMIT $limit OFFSET $start";
            $rData = pg_query($conn, $qData);
            
            $items = [];
            while ($p = pg_fetch_assoc($rData)) {
                $items[] = $p;
            }

            $publikasiData[] = [
                'nama_jenis' => $jenis,
                'items' => $items,
                'page' => $page,
                'totalPage' => $totalPage,
                'paramKey' => $paramKey
            ];
        }
    }

    $iconMap = [
        'jurnal'   => 'fa-regular fa-newspaper',
        'buku'     => 'fa-regular fa-bookmark',
        'media'    => 'fa-regular fa-image',
        'lainnya'  => 'fa-regular fa-file-lines'
    ];
    
    function h($s) {
        return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Publikasi Dosen - <?= h($profil['nama_dosen']) ?></title>
    <link rel="icon" href="img/Logo-hitam.png" type="image" sizes="30x30">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleFooter.css">
    <link rel="stylesheet" href="css/styleDosenPublikasi.css">
</head>
<body>
    <div id="scrollIndicator" class="scroll-indicator"></div>
    <div class="header-container">
        <div class="logo">
            <?php if ($rowLogo): ?>
                <img src="<?php echo htmlspecialchars($rowLogo['url_logo']); ?>" alt="LABSE" class="logo-img">
            <?php else: ?>
                <img src="img/logo.png" alt="LABSE" class="logo-img">
            <?php endif; ?>
        </div>
        
        <div class="desktop-nav">
            <nav>
                <ul id="nav-list" class="nav-collapse">
                    <?php foreach ($navItems as $nav): ?>
                        <?php if (count($nav['subnav']) > 0): ?>
                            <li class="dropdown">
                                <a href="<?php echo htmlspecialchars($nav['url_nav']); ?>" class="dropbtn">
                                    <?php echo htmlspecialchars($nav['nama_nav']); ?>
                                    <i class="bi bi-chevron-down"></i>
                                </a>
                                <div class="dropdown-content">
                                    <div class="dropdown-scroll overflow-auto" style="max-height: 250px;">
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

            <?php if($rowLogo): ?>
                <a href="<?php echo htmlspecialchars($rowLogo['link_cta']); ?>" class="cta-button">
                    <span class="cta-text"><?php echo htmlspecialchars($rowLogo['judul_cta']); ?></span>
                </a>
            <?php endif; ?>
        </div>
        
        <div class="mobile-nav-toggle">
            <i class="bi bi-list"></i>
        </div>
    </div>
    
    <div class="mobile-nav">
        <ul class="mobile-nav-list">
            <?php foreach ($navItems as $nav): ?>
                <?php if (count($nav['subnav']) > 0): ?>
                    <li>
                        <button class="mobile-dropdown-btn">
                            <?php echo htmlspecialchars($nav['nama_nav']); ?>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="mobile-dropdown-content">
                            <?php foreach ($nav['subnav'] as $sub): ?>
                                <a href="<?php echo htmlspecialchars($sub['url_subnav']); ?>">
                                    <?php echo htmlspecialchars($sub['nama_subnav']); ?>
                                </a>
                            <?php endforeach; ?>
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
        
        <?php if($rowLogo): ?>
            <div class="mobile-cta">
                <a href="<?php echo htmlspecialchars($rowLogo['link_cta']); ?>" class="cta-button">
                    <span class="cta-text"><?php echo htmlspecialchars($rowLogo['judul_cta']); ?></span>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="hero-wrapper">
        <div class="hero-container">
            <div class="hero-frame">
                <img src="img/bgpublikasi.jpg" alt="Publikasi Background">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <h1 class="hero-title">PUBLIKASI</h1>
                </div>
            </div>
        </div>
        <div class="container my-3" style="margin-left: 0px; padding-left: 0px; margin-right: 0px; padding-right: 0px;">
            <div class="row" style="width: 1350px;">
                <div class="sidebar-wrapper">
                    <a href="dosen_detail.php?id=<?php echo $id; ?>" class="btn-menu">Profil</a>
                    <a href="dosen_penelitian.php?id=<?php echo $id; ?>" class="btn-menu">Penelitian</a>
                    <a href="dosen_publikasi.php?id=<?php echo $id; ?>" class="btn-menu active">Publikasi</a>
                </div>
            
                <div class="col-lg-9">
                    <div class="d-flex align-items-center mb-4">
                        <a href="dosen_detail.php?id=<?php echo $id; ?>" class="btn-back me-3"><i class="bi bi-arrow-left"></i></a>
                        <h2 class="fw-bold mb-0">PUBLIKASI</h2>
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
  
                    <?php if (empty($publikasiData)): ?>
                        <div class="card card-rounded p-3">
                            Tidak ada publikasi untuk dosen ini.
                        </div>
                    <?php else: ?>
                        <?php foreach ($publikasiData as $data): ?>
                            <?php 
                                $jenis = $data['nama_jenis'];
                                $items = $data['items'];
                            ?>

                            <div class="section-card">
                                <div class="publikasi-header my-3">
                                    <?= h(ucwords(strtolower($jenis))) ?>
                                </div>

                                <div class="list-unstyled publikasi-list mb-0">
                                    <?php 
                                        $jenisKey = strtolower($jenis);
                                        $iconClass = 'fa-regular fa-file-lines';
                                        foreach($iconMap as $key => $val) {
                                            if (strpos($jenisKey, $key) !== false) {
                                                $iconClass = $val;
                                                break;
                                            }
                                        }
                                    ?>
                                    
                                    <?php foreach ($items as $it): ?>
                                    <div class="publikasi-card">
                                        <div class="publikasi-icon-box">
                                            <i class="<?= $iconClass ?>"></i>
                                        </div>

                                        <div class="publikasi-content">
                                            <div class="publikasi-title">
                                                <?= h($it['judul_publikasi']) ?>
                                            </div>

                                            <div class="publikasi-meta">
                                                Ketua Publikasi : <?= h($it['nama_dosen']) ?><br>
                                                Tahun : <?= h($it['tahun_publikasi']) ?>
                                            </div>
                                            
                                            <?php if (!empty($it['link_publikasi'])): ?>
                                                <a href="<?= h($it['link_publikasi']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2 rounded-pill" style="font-size: 0.8rem;">
                                                    Lihat Publikasi <i class="bi bi-box-arrow-up-right ms-1"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php if ($data['totalPage'] > 1): ?>
                                    <div class="pagination-wrapper mb-5">
                                        <?php if ($data['page'] > 1): ?>
                                            <a href="<?= buildUrl($data['paramKey'], $data['page'] - 1) ?>" class="btn-pagination">
                                                <i class="bi bi-caret-left-fill me-1"></i> Previous
                                            </a>
                                        <?php else: ?>
                                            <button class="btn-pagination" disabled>Previous</button>
                                        <?php endif; ?>

                                        <span class="pagination-info">
                                            Halaman <?= $data['page'] ?> dari <?= $data['totalPage'] ?>
                                        </span>

                                        <?php if ($data['page'] < $data['totalPage']): ?>
                                            <a href="<?= buildUrl($data['paramKey'], $data['page'] + 1) ?>" class="btn-pagination">
                                                Next <i class="bi bi-caret-right-fill ms-1"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn-pagination" disabled>Next</button>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <button id="toTop" class="to-top-btn">
        <i class="fas fa-arrow-up"></i>
    </button>

    <div id="footer-container"></div>
    <script src="js/footer.js"></script>
    <script src="js/scroll-top.js"></script>
    <script src="js/navigation.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dropdown.js"></script>
</body>
</html>
<?php pg_close($conn); ?>