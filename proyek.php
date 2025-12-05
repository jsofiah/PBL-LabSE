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
        $navItems[3]['subnav'][] = [
            'nama_subnav' => $d['nama_dosen'], 
            'url_subnav' => "dosen_detail.php?id=" . $d['id_dosen']
        ];
    }

    $qLogo = "SELECT * FROM vw_logo_cta";
    $rLogo = pg_query($conn, $qLogo);
    $rowLogo = pg_fetch_assoc($rLogo);

    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'terbaru';
    $cari   = isset($_GET['cari']) ? trim($_GET['cari']) : ''; 
    $orderDirection = ($filter == 'terlama') ? 'ASC' : 'DESC';

    $limit = 5;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page > 1) ? ($page * $limit) - $limit : 0;

    $base_join = " FROM vw_proyek p
                   LEFT JOIN public.proyek_mhs pm ON p.id_proyek = pm.id_proyek
                   LEFT JOIN public.mhs_segeeks m ON pm.id_mhs = m.id_mhs
                   LEFT JOIN public.proyek_dosen pd ON p.id_proyek = pd.id_proyek
                   LEFT JOIN public.dosen d ON pd.id_dosen = d.id_dosen
                   GROUP BY 
                        p.id_proyek, 
                        p.judul_proyek, 
                        p.isi_proyek, 
                        p.tanggal_terbit_proyek, 
                        p.penulis_proyek,
                        p.url_gambar_proyek1, 
                        p.url_gambar_proyek2, 
                        p.url_gambar_proyek3 ";

    $having_clause = "";

    if (!empty($cari)) {
        $clean_cari = preg_replace('/[^a-zA-Z0-9 ]/', '', $cari);
        $words = explode(" ", $clean_cari);
        $ts_terms = [];
        
        foreach ($words as $w) {
            $t = trim($w);
            if (!empty($t)) $ts_terms[] = $t . ":*"; 
        }

        if (!empty($ts_terms)) {
            $query_str = implode(" & ", $ts_terms);
            
            $search_targets = " COALESCE(p.judul_proyek, '') || ' ' || 
                                COALESCE(p.isi_proyek, '') || ' ' || 
                                COALESCE(p.penulis_proyek, '') || ' ' || 
                                COALESCE(STRING_AGG(m.nama_mhs, ' '), '') || ' ' || 
                                COALESCE(STRING_AGG(d.nama_dosen, ' '), '') ";

            $having_clause = " HAVING (
                                to_tsvector('indonesian', $search_targets) @@ to_tsquery('indonesian', '$query_str')
                                OR
                                to_tsvector('simple', $search_targets) @@ to_tsquery('simple', '$query_str')
                               ) ";
        }
    }

    $qTotal = "SELECT COUNT(*) as total FROM (SELECT p.id_proyek $base_join $having_clause) as sub";
    $rTotal = pg_query($conn, $qTotal);
    
    if (!$rTotal) {
        $totalData = 0; $totalPages = 1;
    } else {
        $rowTotal = pg_fetch_assoc($rTotal);
        $totalData = $rowTotal['total'];
        $totalPages = ceil($totalData / $limit);
    }

    $qProyek = "SELECT 
                    p.*,
                    STRING_AGG(DISTINCT m.nama_mhs, ', ') AS anggota_mahasiswa,
                    STRING_AGG(DISTINCT d.nama_dosen, ', ') AS dosen_pembimbing
                $base_join
                $having_clause
                ORDER BY p.tanggal_terbit_proyek $orderDirection 
                LIMIT $limit OFFSET $start";

    $rProyek = pg_query($conn, $qProyek);

    $listProyek = [];
    if ($rProyek) {
        while ($row = pg_fetch_assoc($rProyek)) {
            $row['tgl_indo'] = formatTanggalIndonesia($row['tanggal_terbit_proyek']);
            $previewRaw = strip_tags($row['isi_proyek']);
            $row['preview_fmt'] = htmlspecialchars(substr($previewRaw, 0, 150)) . '...';
            $listProyek[] = $row;
        }
    }

    function formatTanggalIndonesia($tanggal) {
        $bulan = array(
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        );
        $timestamp = strtotime($tanggal);
        if ($timestamp) {
            $hari = date('d', $timestamp);
            $bulanAngka = (int)date('n', $timestamp);
            $tahun = date('Y', $timestamp);
            return $hari . ' ' . $bulan[$bulanAngka] . ' ' . $tahun;
        }
        return $tanggal;
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyek - Laboratorium Software Engineer</title>
    <link rel="icon" href="img/Logo-hitam.png" type="image">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleFooter.css">
    <link rel="stylesheet" href="css/styleProyek.css">
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
                <img src="img/bgproyek.jpg" alt="Proyek Background">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <h1 class="hero-title">PROYEK</h1>
                </div>
            </div>
        </div>
    </div>
    
    <div class="content-wrapper mx-5 py-3">
        <div class="row mb-5 align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="d-flex gap-3">
                    <a href="?filter=terbaru&cari=<?php echo $cari; ?>" 
                       class="filter-btn text-decoration-none <?php echo ($filter == 'terbaru') ? 'active' : ''; ?>">
                       Terbaru
                    </a>
                    <a href="?filter=terlama&cari=<?php echo $cari; ?>" 
                       class="filter-btn text-decoration-none <?php echo ($filter == 'terlama') ? 'active' : ''; ?>">
                       Terlama
                    </a>
                </div>
            </div>
            
            <div class="col-md-6 d-flex justify-content-md-end">
                <form action="" method="GET" class="search-capsule shadow-sm">
                    <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                    <input type="text" name="cari" class="search-input" placeholder="Telusuri..." value="<?php echo htmlspecialchars($cari); ?>">
                    <button type="submit" class="search-btn-icon">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="project-list">
            
            <?php if (!empty($listProyek)): ?>
                <?php foreach ($listProyek as $p): ?>
                
                <div class="card project-card mb-4">
                    <div class="row g-0 align-items-center h-100">
                        <div class="col-md-4">
                            <div class="project-img-wrapper">
                                <img src="<?php echo htmlspecialchars($p['url_gambar_proyek1']); ?>" 
                                     class="project-img" 
                                     alt="<?php echo htmlspecialchars($p['judul_proyek']); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="card-body-custom">
                                <div>
                                    <h4 class="project-title"><?php echo htmlspecialchars($p['judul_proyek']); ?></h4>
                                    <p class="project-desc">
                                        <?php echo $p['preview_fmt']; ?>
                                    </p>
                                </div>
                                
                                <div class="d-flex align-items-center gap-3">
                                    <span class="project-date">
                                        <i class="bi bi-calendar3 me-1"></i> <?php echo $p['tgl_indo']; ?>
                                    </span>
                                    
                                    <a href="proyekDetail.php?id=<?php echo $p['id_proyek']; ?>" class="btn-read-more">Baca selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-light text-center py-5 shadow-sm" role="alert">
                    <h4 class="text-muted"><i class="bi bi-folder-x me-2"></i>Proyek tidak ditemukan.</h4>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-between align-items-center mt-5">
            
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&filter=<?php echo $filter; ?>&cari=<?php echo $cari; ?>" class="btn-pagination">
                    <i class="bi bi-caret-left-fill me-1"></i> Previous
                </a>
            <?php else: ?>
                <button class="btn-pagination" style="opacity: 0.5; cursor: not-allowed;">
                    <i class="bi bi-caret-left-fill me-1"></i> Previous
                </button>
            <?php endif; ?>

            <span class="text-muted fw-bold">Slide <?php echo $page; ?> of <?php echo $totalPages; ?></span>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&filter=<?php echo $filter; ?>&cari=<?php echo $cari; ?>" class="btn-pagination">
                    Next <i class="bi bi-caret-right-fill ms-1"></i>
                </a>
            <?php else: ?>
                <button class="btn-pagination" style="opacity: 0.5; cursor: not-allowed;">
                    Next <i class="bi bi-caret-right-fill ms-1"></i>
                </button>
            <?php endif; ?>

        </div>
        <?php endif; ?>
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

<?php
    pg_close($conn);
?>
