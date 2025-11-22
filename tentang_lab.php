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
                'url_nav' => $rowNav['url_nav'],
                'subnav' => []
            ];
        }
        
        if ($rowNav['id_subnav']) {
            $navItems[$id_nav]['subnav'][] = [
                'nama_subnav' => $rowNav['nama_subnav'],
                'url_subnav' => $rowNav['url_subnav']
            ];
        }
    }
    $qDosen = "SELECT id_dosen, nama_dosen FROM vw_detail_dosen ORDER BY nama_dosen";
    $rDosen = pg_query($conn, $qDosen);

    while ($d = pg_fetch_assoc($rDosen)) {
        $url = "dosen_detail.php?id=" . $d['id_dosen'];
        $navItems[3]['subnav'][] = [
            'nama_subnav' => $d['nama_dosen'],
            'url_subnav' => $url
        ];
    }

    $qLogo = "SELECT * FROM vw_logo_cta";
    $rLogo = pg_query($conn, $qLogo);
    $rowLogo = pg_fetch_assoc($rLogo);

    $qDeskripsi = "SELECT * FROM vw_deskripsi_seGeeks";
    $rDeskripsi = pg_query($conn, $qDeskripsi);
    $rowDeskripsi = pg_fetch_assoc($rDeskripsi);

    $qRiset = "SELECT * FROM vw_fokus_riset";
    $rRiset = pg_query($conn, $qRiset);

    $qPeran = "SELECT * FROM vw_peran_lab";
    $rPeran = pg_query($conn, $qPeran);

    if (!$rPeran) {
        die("Error dalam query: " . pg_last_error($conn));
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorium Software Engineer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleTentang.css">
    <link rel="stylesheet" href="css/styleFooter.css">
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

    <div class="hero-wrapper">
        <div class="hero-container">
            <div class="hero-frame">
                <img src="img/bgtentang.jpg" alt="Tentang Background">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                <h1 class="hero-title">
                    TENTANG LAB SE
                </h1>
            </div>
        </div>
    </div>

    <div class="sejarah-section py-1">
        <div class="sejarah-wrapper">
            <svg class="shape-svg" width="400" height="400" viewBox="0 0 546 526" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="imgPattern" patternUnits="objectBoundingBox" width="1" height="1">
                        <image href="img/segeeks1.jpg" width="546" height="526" preserveAspectRatio="xMidYMid slice" x="-80"></image>
                    </pattern>
                </defs>

                <path d="M0 60C0 26.8629 26.8629 0 60 0H204C237.137 0 264 26.8629 264 60V440H60C26.8629 440 0 413.137 0 380V60Z"
                    fill="url(#imgPattern)" />

                <path d="M282 86H486C519.137 86 546 112.863 546 146V466C546 499.137 519.137 526 486 526H342C308.863 526 282 499.137 282 466V86Z"
                    fill="url(#imgPattern)" />
            </svg>
        </div>


        <div class="text-content">
            <h2>SEJARAH</h2>
            <p><?php echo htmlspecialchars($rowDeskripsi['isi_segeeks']); ?></p>
        </div>
    </div>

    <div class="riset-section">
        <div class="riset-content">
            <div class="riset-title">
                FOKUS<br>RISET
            </div>

            <div class="riset-items">
                <?php while ($rowRiset = pg_fetch_assoc($rRiset)): ?>
                    <div class="riset-item">
                        <?php echo htmlspecialchars($rowRiset['nama_fokus']); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <div class="peran-lab-section">
        <div class="container">
            <h2 class="section-title">PERAN LAB DI JURUSAN</h2>
            
            <div class="peran-grid">
                <?php while ($rowPeran = pg_fetch_assoc($rPeran)): ?>
                    <div class="peran-card">
                        <div class="peran-icon">
                            <i class="<?php echo htmlspecialchars($rowPeran['icon']); ?>"></i>
                        </div>
                        <h3 class="peran-title"><?php echo htmlspecialchars($rowPeran['nama_peran']); ?></h3>
                        <button class="btn-selengkapnya" data-bs-toggle="modal" data-bs-target="#peranModal<?php echo $rowPeran['id_peran']; ?>">
                            Selengkapnya
                        </button>
                    </div>

                    <div class="modal fade" id="peranModal<?php echo $rowPeran['id_peran']; ?>" tabindex="-1" aria-labelledby="peranModalLabel<?php echo $rowPeran['id_peran']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="peranModalLabel<?php echo $rowPeran['id_peran']; ?>">
                                        <?php echo htmlspecialchars($rowPeran['nama_peran']); ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <?php echo nl2br(htmlspecialchars($rowPeran['deskripsi_peran'])); ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <div id="footer-container"></div>
    <script src="js/footer.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dropdown.js"></script>
</body>
</html>
