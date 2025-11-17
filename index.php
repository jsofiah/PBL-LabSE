<?php
    require 'config.php';

    // QUERY NAV JGN DIHAPUS
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

    // QUERY STATS
    $qStats = "SELECT * FROM get_statistik()";
    $rStats = pg_query($conn, $qStats);
    $rowStats = pg_fetch_assoc($rStats);

    //QUERY VISI MISI
    $qVisi = "SELECT * FROM vw_visi_lab";
    $rVisi = pg_query($conn, $qVisi);
    $rowVisi = pg_fetch_assoc($rVisi);

    $qMisi = "SELECT * FROM vw_misi_lab";
    $rMisi = pg_query($conn, $qMisi);
    $rowMisi = pg_fetch_assoc($rMisi);
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
    <link rel="stylesheet" href="css/styleIndex.css">
</head>
<body>
    <!-- BAGIAN NAV -->
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

    <!-- BAGIAN BANNER -->
    <div class="hero-wrapper">
        <div class="hero-container">
            <div class="hero-frame">
                <img src="img/background_index.jpg" alt="Lab Background">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                <h1 class="hero-title">
                    LABORATORIUM<br>
                    SOFTWARE ENGINEER
                </h1>

                <div class="hero-boxes">
                    <div class="hero-box hero-box-left">
                        <h3>Ruang Inovasi, Riset, dan Kolaborasi Teknologi</h3>
                    </div>
                    
                    <div class="stats-box">
                        <svg width="355" height="206" viewBox="0 0 355 206" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M335 0C346.046 0 355 8.95431 355 20V186C355 197.046 346.046 206 335 206H20C8.95431 206 0 197.046 0 186V76.084C0 65.0383 8.95431 56.084 20 56.084H118.818C129.864 56.084 138.818 47.1296 138.818 36.084V20C138.818 8.95431 147.773 0 158.818 0H335Z" fill="white"/>
                            <rect x="155" y="11" width="183" height="46" rx="15" fill="#FFD601"/>
                        </svg>

                        <div class="stats-content">
                            <div class="badge">Bergabung ↗</div>
                            <ul class="stats-list">
                                <li><?php echo htmlspecialchars($rowStats['jumlah_dosen']); ?> Dosen</li>
                                <li><?php echo htmlspecialchars($rowStats['jumlah_mahasiswa']); ?> Mahasiswa</li>
                                <li><?php echo htmlspecialchars($rowStats['jumlah_proyek']); ?> Proyek</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- BAGIAN VISI MISI -->
    <div class="visi_misi-container visi_misi justify-content-center">
        <div class="row justify-content-center" style="gap: 50px;">
            <div class="col-md-5 mb-4">
                <h2 class="text-center fw-bold mb-4">VISI</h2>
                <div class="visi-card bg-white shadow p-3 rounded">
                    <p><?php echo htmlspecialchars($rowVisi['isi_visi']); ?></p>
                </div>
            </div>

            <div class="col-md-5">
                <h2 class="text-center fw-bold mb-4">MISI</h2>
                <?php while ($rowMisi = pg_fetch_assoc($rMisi)): ?>
                    <div class="misi-item">
                        <?php echo htmlspecialchars($rowMisi['isi_misi']); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>


    <!-- JS FOOTER DROPDOWN -->
    <div id="footer-container"></div>
    <script src="js/footer.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dropdown.js"></script>
</body>
</html>
