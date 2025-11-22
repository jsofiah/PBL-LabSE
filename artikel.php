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


    $filter = $_GET['filter'] ?? 'terbaru';
    $cari   = $_GET['cari'] ?? '';
    $jenis  = $_GET['jenis'] ?? '';

    $qJenis = "
        SELECT DISTINCT id_jenisartikel, nama_jenisartikel
        FROM vw_artikel
        ORDER BY nama_jenisartikel
    ";
    $rJenis = pg_query($conn, $qJenis);

    $limit = 8;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    $where = "WHERE 1=1";

    if (!empty($cari)) {
        $where .= " AND judul_artikel ILIKE '%$cari%'";
    }
    if ($filter == 'jenis' && !empty($jenis)) {
        $where .= " AND id_jenisartikel = '$jenis'";
    }

    $qCount = "SELECT COUNT(*) AS total FROM vw_artikel $where";
    $rCount = pg_query($conn, $qCount);
    $totalData = pg_fetch_assoc($rCount)['total'];
    $totalPages = ceil($totalData / $limit);

    $order = "ORDER BY tanggal_terbit_artikel DESC";
    if ($filter == 'terlama') {
        $order = "ORDER BY tanggal_terbit_artikel ASC";
    }

    $filter   = isset($_GET['filter']) ? $_GET['filter'] : 'terbaru';
    $kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'semua';
    $cari     = isset($_GET['cari']) ? $_GET['cari'] : '';

    if ($filter == 'terlama') {
        $order = "ASC";
    } else {
        $order = "DESC";
    }

    $whereKategori = ($kategori == 'semua') 
        ? "" 
        : " AND id_jenisartikel = '$kategori' ";

    $whereCari = ($cari != '')
    ? " AND (judul_artikel ILIKE '%$cari%' OR isi_artikel ILIKE '%$cari%') "
    : "";


    $qArtikel = "
        SELECT *
        FROM vw_artikel
        WHERE 1=1
            $whereKategori
            $whereCari
        ORDER BY tanggal_terbit_artikel $order
        LIMIT $limit OFFSET $start
    ";
    $rArtikel = pg_query($conn, $qArtikel);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikel - Laboratorium Software Engineer</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleArtikel.css">
    <link rel="stylesheet" href="css/styleFooter.css">
</head>

<body>
    <div class="logo">
        <?php if ($rowLogo): ?>
            <img src="<?php echo htmlspecialchars($rowLogo['url_logo']); ?>" class="logo-img">
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
                    <li><a href="<?php echo htmlspecialchars($nav['url_nav']); ?>"><?php echo htmlspecialchars($nav['nama_nav']); ?></a></li>
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
                <img src="img/bgartikel.jpg">
                <div class="hero-overlay"></div>
                <div class="hero-content"><h1 class="hero-title">ARTIKEL</h1></div>
            </div>
        </div>
    </div>

    <div class="artikel-section mx-5 py-3">
        <div class="row align-items-center">
            <div class="row mb-5 align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="d-flex gap-3">
                        <div class="dropdown">
                            <button class="filter-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-sort-down-alt me-2"></i> Urutkan
                            </button>

                            <ul class="dropdown-menu filter-menu">
                                <li>
                                    <a class="dropdown-item <?php echo ($filter=='terbaru') ? 'active' : ''; ?>"
                                    href="?filter=terbaru&kategori=<?php echo $kategori; ?>&cari=<?php echo $cari; ?>">
                                    Terbaru
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item <?php echo ($filter=='terlama') ? 'active' : ''; ?>"
                                    href="?filter=terlama&kategori=<?php echo $kategori; ?>&cari=<?php echo $cari; ?>">
                                    Terlama
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="dropdown">
                            <button class="filter-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-tags me-2"></i> Kategori
                            </button>

                            <ul class="dropdown-menu filter-menu">
                                <li>
                                    <a class="dropdown-item <?php echo ($kategori=='semua') ? 'active' : ''; ?>"
                                    href="?kategori=semua&filter=<?php echo $filter; ?>&cari=<?php echo $cari; ?>">
                                    Semua Artikel
                                    </a>
                                </li>

                                <?php pg_result_seek($rJenis, 0); ?>
                                <?php while ($j = pg_fetch_assoc($rJenis)): ?>
                                    <li>
                                        <a class="dropdown-item <?php echo ($kategori==$j['id_jenisartikel']) ? 'active' : ''; ?>"
                                        href="?kategori=<?php echo $j['id_jenisartikel']; ?>&filter=<?php echo $filter; ?>&cari=<?php echo $cari; ?>">
                                        <?php echo htmlspecialchars($j['nama_jenisartikel']); ?>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>

                    </div>
                </div>

                <div class="col-md-6 d-flex justify-content-md-end">
                    <form method="GET" class="search-capsule shadow-sm">
                        <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                        <input type="hidden" name="kategori" value="<?php echo $kategori; ?>">

                        <input type="text" name="cari" class="search-input" 
                            placeholder="Telusuri..." value="<?php echo $cari; ?>">

                        <button class="search-btn-icon">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="artikel-container">
            <?php if (pg_num_rows($rArtikel) > 0): ?>
                <?php while ($rowArtikel = pg_fetch_assoc($rArtikel)): ?>
                    <div class="artikel-card">
                        <div class="row g-0">
                            <div class="col-md-5">
                                <div class="artikel-thumbnail">
                                    <?php if (!empty($rowArtikel['url_gambar_artikel'])): ?>
                                        <img src="<?php echo htmlspecialchars($rowArtikel['url_gambar_artikel']); ?>">
                                    <?php else: ?>
                                        <div class="thumbnail-placeholder"></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-7">
                                <div class="artikel-content">
                                    
                                    <div>
                                        <h3 class="artikel-title"><?php echo htmlspecialchars($rowArtikel['judul_artikel']); ?></h3>
                                        <p class="artikel-excerpt">
                                            <?php
                                                $preview = strip_tags($rowArtikel['isi_artikel']);
                                                echo htmlspecialchars(substr($preview, 0, 150)) . '...';
                                            ?>
                                        </p>
                                    </div>

                                    <div class="artikel-footer">
                                        <div class="artikel-info">

                                            <span class="artikel-date">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                <?php echo date('d F Y', strtotime($rowArtikel['tanggal_terbit_artikel'])); ?>
                                            </span>

                                            <span class="artikel-badge"><?php echo htmlspecialchars($rowArtikel['nama_jenisartikel']); ?></span>

                                            <a href="artikel_detail.php?id=<?php echo $rowArtikel['id_artikel']; ?>" 
                                               class="artikel-read-more">Baca selengkapnya</a>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-light text-center py-5 shadow-sm">
                    <h4 class="text-muted"><i class="bi bi-folder-x me-2"></i>Artikel tidak ditemukan.</h4>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination-wrapper">

                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&filter=<?php echo $filter; ?>&cari=<?php echo $cari; ?>&jenis=<?php echo $jenis; ?>"
                       class="btn-pagination">
                       <i class="bi bi-caret-left-fill me-1"></i> Previous
                    </a>
                <?php else: ?>
                    <button class="btn-pagination" disabled style="opacity: .5;">Previous</button>
                <?php endif; ?>

                <span class="pagination-info">Slide <?php echo $page; ?> of <?php echo $totalPages; ?></span>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&filter=<?php echo $filter; ?>&cari=<?php echo $cari; ?>&jenis=<?php echo $jenis; ?>"
                       class="btn-pagination">
                       Next <i class="bi bi-caret-right-fill ms-1"></i>
                    </a>
                <?php else: ?>
                    <button class="btn-pagination" disabled style="opacity: .5;">Next</button>
                <?php endif; ?>

            </div>
        <?php endif; ?>

    </div>

    <div id="footer-container"></div>
    <script src="js/footer.js"></script>
    <script src="js/dropdown.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php pg_close($conn); ?>
