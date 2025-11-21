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

    function getMitra($conn, $jenis) {
        $jenis = intval($jenis);
        $q = "SELECT m.*, jm.nama_jenismitra 
              FROM public.mitra m
              JOIN public.jenis_mitra jm ON m.id_jenismitra = jm.id_jenismitra
              WHERE m.id_jenismitra = $jenis 
              ORDER BY m.id_mitra";
        
        return pg_query($conn, $q);
    }

    // Ambil data untuk setiap kategori
    $mitraAkademik = getMitra($conn, 1);
    $mitraTeknologi = getMitra($conn, 2);
    $mitraIndustri = getMitra($conn, 3);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorium Software Engineer - Mitra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleMitra.css">
    <link rel="stylesheet" href="css/styleFooter.css">

</head>

<body class="Mitra-page">
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
                <img src="img/background_form_pendaftaran.jpg" alt="mitra Background">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                <h1 class="hero-title">MITRA</h1>
            </div>
        </div>
    </div>

    <div class="container py-3">
    <h2 class="fw-bold text-center mb-5">DIDUKUNG OLEH MITRA TERPERCAYA KAMI</h2>

    <h3>MITRA AKADEMIK</h3>
    <div class="mitra-slider">
        <button class="slide-btn left" onclick="slideLeft('akad')">
            <i class="bi bi-chevron-left"></i>
        </button>
        
        <div class="mitra-track" id="track-akad">
            <?php $r = getMitra($conn, 1); while ($row = pg_fetch_assoc($r)): ?>
                <div class="mitra-card">
                    <div class="mitra-icon-wrapper">
                        <img src="<?= htmlspecialchars($row['url_gambar_mitra']) ?>" alt="Logo Mitra">
                    </div>
                    <h5><?= htmlspecialchars($row['nama_mitra']) ?></h5>
                    <p><?= htmlspecialchars($row['isi_mitra']) ?></p>
                </div>
            <?php endwhile; ?>
        </div>

        <button class="slide-btn right" onclick="slideRight('akad')">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>
    
    <hr class="section-divider">

    <h3>MITRA TEKNOLOGI</h3>
    <div class="mitra-slider">
        <button class="slide-btn left" onclick="slideLeft('non')">
            <i class="bi bi-chevron-left"></i>
        </button>
            <div class="mitra-track" id="track-non">
                <?php $r = getMitra($conn, 2); while ($row = pg_fetch_assoc($r)): ?>
                    <div class="mitra-card">
                        <div class="mitra-icon-wrapper">
                            <img src="<?= htmlspecialchars($row['url_gambar_mitra']) ?>" alt="Logo Mitra">
                        </div>
                        <h5><?= htmlspecialchars($row['nama_mitra']) ?></h5>
                        <p><?= htmlspecialchars($row['isi_mitra']) ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <button class="slide-btn right" onclick="slideRight('non')">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>

    <hr class="section-divider">

    <h3>MITRA INDUSTRI</h3>
    <div class="mitra-slider">
        <button class="slide-btn left" onclick="slideLeft('adm')">
            <i class="bi bi-chevron-left"></i>
        </button>
            <div class="mitra-track" id="track-adm">
                <?php $r = getMitra($conn, 3); while ($row = pg_fetch_assoc($r)): ?>
                    <div class="mitra-card">
                        <div class="mitra-icon-wrapper">
                            <img src="<?= htmlspecialchars($row['url_gambar_mitra']) ?>" alt="Logo Mitra">
                        </div>
                        <h5><?= htmlspecialchars($row['nama_mitra']) ?></h5>
                        <p><?= htmlspecialchars($row['isi_mitra']) ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <button class="slide-btn right" onclick="slideRight('adm')">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>
</div>

    <div id="footer-container"></div>
    <script src="js/footer.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dropdown.js"></script>

    <script>
        function slideLeft(id){document.getElementById("track-"+id).scrollBy({left:-330,behavior:'smooth'})}
        function slideRight(id){document.getElementById("track-"+id).scrollBy({left:330,behavior:'smooth'})}
    </script>
</body>
</html>
