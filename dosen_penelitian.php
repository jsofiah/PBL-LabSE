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
    <link rel="stylesheet" href="css/styleIndex.css">
    <link rel="stylesheet" href="css/styleFooter.css">
    <link rel="stylesheet" href="css/styleRoot.css">
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
                <img src="img/background_index.jpg" alt="Lab Background">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                <h1 class="hero-title"> PENELITIAN </h1>
            </div>
        </div>
    </div>
    
     <div class="container main-container">
        <div class="row">

            <div class="col-lg-2 d-none d-lg-block sidebar-wrapper">
                <div class="d-flex flex-column gap-2">
                    <a href="#" class="btn-menu">Profil</a>
                    <a href="#" class="btn-menu active">Penelitian</a>
                    <a href="#" class="btn-menu">Publikasi</a>
                </div>
            </div>

            <div class="col-lg-9 content-wrapper">

                <div class="d-flex align-items-center mb-4">
                    <a href="#" class="btn-back me-3"><i class="bi bi-arrow-left"></i></a>
                    <h2 class="page-title">PENELITIAN</h2>
                </div>

                <div class="card content-card mb-4">
                    <div class="card-body p-4">
                        <div class="dosen-profile-header d-flex align-items-center">
                            <div class="profile-img-wrapper me-4">
                                <img src="img/artikel/artikel1.jpg" class="profile-img">
                            </div>
                            <div class="profile-info">
                                <h6 class="text-primary fw-bold mb-1">Program Studi S-2 Rekayasa TI</h6>
                                <h4 class="fw-bold mb-1">Yan Watequlis Syaifudin, S.T., M.MT, Ph.D.</h4>
                                <p class="text-muted mb-0">qulis@polinema.ac.id</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="research-card">
                    <div class="research-icon-wrapper">
                        <div class="icon-box"><i class="bi bi-journal-text"></i></div>
                    </div>
                    <div class="research-info">
                        <h5 class="research-title">Inovasi Platform Interaktif Berbasis Web untuk Optimalisasi Pembelajaran Berkelanjutan</h5>
                        <div class="research-details">
                            Ketua Peneliti : Rudy Ariyanto, ST., M.Cs.<br>
                            Tahun : 2023
                        </div>
                    </div>
                </div>

                <div class="research-card">
                    <div class="research-icon-wrapper">
                        <div class="icon-box"><i class="bi bi-journal-text"></i></div>
                    </div>
                    <div class="research-info">
                        <h5 class="research-title">Penerapan Big Data dan Collaborative Filtering pada Sistem Rekomendasi E-Learning</h5>
                        <div class="research-details">
                            Ketua Peneliti : Rudy Ariyanto, ST., M.Cs.<br>
                            Tahun : 2023
                        </div>
                    </div>
                </div>

                <div class="research-card">
                    <div class="research-icon-wrapper">
                        <div class="icon-box"><i class="bi bi-journal-text"></i></div>
                    </div>
                    <div class="research-info">
                        <h5 class="research-title">Pengembangan Sistem Keamanan Jaringan Berbasis AI untuk Infrastruktur Kampus</h5>
                        <div class="research-details">
                            Ketua Peneliti : Yan Watequlis S., ST., M.MT<br>
                            Tahun : 2024
                        </div>
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