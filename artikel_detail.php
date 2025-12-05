<?php
    require 'config.php';

    $id_artikel = isset($_GET['id']) ? (int)$_GET['id'] : 1; 
    $qArtikel = "SELECT * FROM public.artikel WHERE id_artikel = $1";
    $rArtikel = pg_query_params($conn, $qArtikel, array($id_artikel));
    $artikelUtama = pg_fetch_assoc($rArtikel);

    if (!$artikelUtama) {
        header("Location: artikel.php");
        exit;
    }
    $qArtikelLain = "SELECT * FROM public.artikel WHERE id_artikel != $1 ORDER BY tanggal_terbit_artikel DESC LIMIT 5";
    $rArtikelLain = pg_query_params($conn, $qArtikelLain, array($id_artikel));
    $artikelLain = pg_fetch_all($rArtikelLain);
    $qNav = "SELECT * FROM vw_nav";
    $rNav = pg_query($conn, $qNav);
    $navItems = [];
    while ($rowNav = pg_fetch_assoc($rNav)) {
        $id_nav = $rowNav['id_nav'];
        if (!isset($navItems[$id_nav])) $navItems[$id_nav] = ['nama_nav' => $rowNav['nama_nav'], 'url_nav' => $rowNav['url_nav'], 'subnav' => []];
        if (!empty($rowNav['id_subnav'])) $navItems[$id_nav]['subnav'][] = ['nama_subnav' => $rowNav['nama_subnav'], 'url_subnav' => $rowNav['url_subnav']];
    }

    $qDosen = "SELECT id_dosen, nama_dosen FROM vw_detail_dosen ORDER BY nama_dosen";
    $rDosen = pg_query($conn, $qDosen);
    while ($d = pg_fetch_assoc($rDosen)) {
        $navItems[3]['subnav'][] = ['nama_subnav' => $d['nama_dosen'], 'url_subnav' => "dosen_detail.php?id=" . $d['id_dosen']];
    }

    $rLogo = pg_query($conn, "SELECT * FROM vw_logo_cta");
    $rowLogo = pg_fetch_assoc($rLogo);
    function formatTanggal($tanggal) {
        $bulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
        $tgl = explode('-', $tanggal);
        if (count($tgl) == 3) return $tgl[2] . ' ' . $bulan[$tgl[1]] . ' ' . $tgl[0];
        return $tanggal;
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($artikelUtama['judul_artikel']); ?> | Lab SE</title>
    <link rel="icon" href="img/Logo-hitam.png" type="image">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleArtikelDetail.css">
    <link rel="stylesheet" href="css/styleFooter.css">
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
    
    <div class="content-wrapper">
        <div class="artikel-container">

            <div class="artikel-header-frame">
                <img src="<?php echo htmlspecialchars($artikelUtama['url_gambar_artikel']); ?>" alt="">
                <div class="hero-overlay"></div>
                <div class="artikel-header-content">
                    <h1 class="artikel-judul"><?php echo htmlspecialchars($artikelUtama['judul_artikel']); ?></h1>
                </div>
            </div>

            <div class="artikel-body">
                <div class="isi-artikel">
                    <p><?php echo nl2br(htmlspecialchars($artikelUtama['isi_artikel'])); ?></p>
                </div>

                <div class="meta-footer">
                    <div class="meta-text">
                        <strong><?php echo htmlspecialchars($artikelUtama['penulis_artikel']); ?></strong> — Lab SE<br>
                        <span class="text-muted">
                            <?php echo formatTanggal($artikelUtama['tanggal_terbit_artikel']); ?> WIB
                        </span>
                    </div>
                    <div class="meta-line"></div>
                </div>

            </div>
        </div>
    </div>

    <hr class="section-divider">

    <div class="artikel-lain-section">
        <div class="artikel-lain-wrapper">
            <h2 class="artikel-lain-title">LIHAT ARTIKEL LAIN</h2>
            <button class="slide-btn left" onclick="scrollLeftNew()">&#10094;</button>
            <button class="slide-btn right" onclick="scrollRightNew()">&#10095;</button>

            <div class="artikel-cards-wrapper">
                <?php if (!empty($artikelLain)): ?>
                    <?php foreach ($artikelLain as $artikel): ?>
                        <div class="artikel-card-col">
                            <a href="artikel_detail.php?id=<?php echo $artikel['id_artikel']; ?>" class="text-decoration-none">
                                <div class="artikel-card">
                                    <div class="artikel-card-img-container">
                                        <img src="<?php echo htmlspecialchars($artikel['url_gambar_artikel']); ?>" alt="">
                                    </div>
                                    <div class="artikel-card-body">
                                        <p class="artikel-card-title"><?php echo htmlspecialchars($artikel['judul_artikel']); ?></p>
                                        <p class="artikel-card-meta">
                                            <?php echo formatTanggal($artikel['tanggal_terbit_artikel']); ?>.
                                            <?php echo htmlspecialchars($artikel['penulis_artikel']); ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?><p class="text-center w-100">Tidak ada artikel lain.</p><?php endif; ?>
            </div>
        </div>
    </div>

    <button id="toTop" class="to-top-btn">
        <i class="fas fa-arrow-up"></i>
    </button>

    <div id="footer-container"></div>
    <script src="js/footer.js"></script>
    <script src="js/scroll-top.js"></script>
    <script src="js/dropdown.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function scrollLeftNew() { document.querySelector('.artikel-cards-wrapper').scrollBy({ left: -350, behavior: 'smooth' }); }
    function scrollRightNew() { document.querySelector('.artikel-cards-wrapper').scrollBy({ left: 350, behavior: 'smooth' }); }
    </script>
</body>
</html>