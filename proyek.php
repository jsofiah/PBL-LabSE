<?php
    require 'config.php'; 
    $qNav = "SELECT * FROM vw_nav";
    $rNav = pg_query($conn, $qNav);
    $navItems = [];
    while ($rowNav = pg_fetch_assoc($rNav)) {
        $id_nav = $rowNav['id_nav'];
        if (!isset($navItems[$id_nav])) {
            $navItems[$id_nav] = ['nama_nav' => $rowNav['nama_nav'], 'url_nav' => $rowNav['url_nav'], 'subnav' => []];
        }
        if ($rowNav['id_subnav']) {
            $navItems[$id_nav]['subnav'][] = ['nama_subnav' => $rowNav['nama_subnav'], 'url_subnav' => $rowNav['url_subnav']];
        }
    }

    $qDosen = "SELECT id_dosen, nama_dosen FROM vw_detail_dosen ORDER BY nama_dosen";
    $rDosen = pg_query($conn, $qDosen);
    while ($d = pg_fetch_assoc($rDosen)) {
        $navItems[3]['subnav'][] = ['nama_subnav' => $d['nama_dosen'], 'url_subnav' => "dosen_detail.php?id=" . $d['id_dosen']];
    }

    $qLogo = "SELECT * FROM vw_logo_cta";
    $rLogo = pg_query($conn, $qLogo);
    $rowLogo = pg_fetch_assoc($rLogo);

    //  LOGIKA PENCARIAN FTS ARTIKEL 
    
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'terbaru';
    $cari   = isset($_GET['cari']) ? trim($_GET['cari']) : ''; 
    $orderDirection = ($filter == 'terlama') ? 'ASC' : 'DESC';

    $limit = 5; 
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page > 1) ? ($page * $limit) - $limit : 0;

    $ts_query_str = "";
    $params = [];
    
    if (!empty($cari)) {
        $words = explode(" ", $cari);
        $search_terms = [];
        foreach ($words as $word) {
            $clean_word = preg_replace('/[^a-zA-Z0-9]/', '', $word);
            if (!empty($clean_word)) {
                $search_terms[] = $clean_word . ":*"; 
            }
        }
        if (!empty($search_terms)) {
            $ts_query_str = implode(" & ", $search_terms);
        }
    }

    $base_query = " FROM public.artikel a
                    LEFT JOIN public.jenis_artikel ja ON a.id_jenisartikel = ja.id_jenisartikel ";

    $where_clause = "";
    if (!empty($ts_query_str)) {
        $where_clause = " WHERE to_tsvector('indonesian', 
                            COALESCE(a.judul_artikel, '') || ' ' || 
                            COALESCE(a.isi_artikel, '') || ' ' || 
                            COALESCE(a.penulis_artikel, '') || ' ' || 
                            COALESCE(ja.nama_jenisartikel, '') 
                          ) @@ to_tsquery('indonesian', $1) ";
        $params[] = $ts_query_str;
    }

    $qTotal = "SELECT COUNT(*) as total $base_query $where_clause";
    $rTotal = pg_query_params($conn, $qTotal, $params);
    $rowTotal = pg_fetch_assoc($rTotal);
    $totalData = $rowTotal['total'];
    $totalPages = ceil($totalData / $limit);

    $qArtikel = "SELECT 
                    a.id_artikel,
                    a.judul_artikel,
                    a.isi_artikel,
                    a.tanggal_terbit_artikel,
                    a.url_gambar_artikel,
                    a.penulis_artikel,
                    ja.nama_jenisartikel
                 $base_query
                 $where_clause
                 ORDER BY a.tanggal_terbit_artikel $orderDirection 
                 LIMIT $limit OFFSET $start";

    $rArtikel = pg_query_params($conn, $qArtikel, $params);

    $listArtikel = [];
    if ($rArtikel) {
        while ($row = pg_fetch_assoc($rArtikel)) {
            $row['tgl_indo'] = formatTanggalIndonesia($row['tanggal_terbit_artikel']);
            $previewRaw = strip_tags($row['isi_artikel']);
            $row['preview_fmt'] = htmlspecialchars(substr($previewRaw, 0, 120)) . '...';
            $listArtikel[] = $row;
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
    <title>Artikel - Laboratorium Software Engineer</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleIndex.css">
    <link rel="stylesheet" href="css/styleFooter.css">
    <link rel="stylesheet" href="css/styleProyek.css"> 

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
                            <?php echo htmlspecialchars($nav['nama_nav']); ?> <i class="bi bi-chevron-down"></i>
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
                <img src="img/bgartikel.jpg" onerror="this.src='img/bgproyek.jpg'" alt="Artikel Background">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <h1 class="hero-title">ARTIKEL & BERITA</h1>
                </div>
            </div>
        </div>
    </div>
    
    <div class="content-wrapper mx-5 py-3">
        <div class="row mb-5 align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="d-flex gap-3">
                    <a href="?filter=terbaru&cari=<?php echo htmlspecialchars($cari); ?>" 
                       class="filter-btn text-decoration-none <?php echo ($filter == 'terbaru') ? 'active' : ''; ?>">
                       Terbaru
                    </a>
                    <a href="?filter=terlama&cari=<?php echo htmlspecialchars($cari); ?>" 
                       class="filter-btn text-decoration-none <?php echo ($filter == 'terlama') ? 'active' : ''; ?>">
                       Terlama
                    </a>
                </div>
            </div>
            
            <div class="col-md-6 d-flex justify-content-md-end">
                <form action="" method="GET" class="search-capsule shadow-sm">
                    <?php if (isset($filter)): ?>
                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                    <?php endif; ?>
                    <input type="text" name="cari" class="search-input" placeholder="Cari judul, topik, penulis..." value="<?php echo htmlspecialchars($cari); ?>">
                    <button type="submit" class="search-btn-icon">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="row">
            <?php if (!empty($listArtikel)): ?>
                <?php foreach ($listArtikel as $a): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                            <div style="height: 200px; overflow: hidden; position: relative;">
                                <img src="<?php echo htmlspecialchars($a['url_gambar_artikel']); ?>" 
                                     class="w-100 h-100" 
                                     style="object-fit: cover; transition: transform 0.3s;"
                                     alt="<?php echo htmlspecialchars($a['judul_artikel']); ?>">
                                
                                <?php if($a['nama_jenisartikel']): ?>
                                    <span class="badge bg-primary position-absolute top-0 end-0 m-3 shadow-sm">
                                        <?php echo htmlspecialchars($a['nama_jenisartikel']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="card-body d-flex flex-column p-4">
                                <div class="text-muted small mb-2">
                                    <i class="bi bi-calendar3 me-1"></i> <?php echo $a['tgl_indo']; ?>
                                    <span class="mx-2">•</span>
                                    <i class="bi bi-person me-1"></i> <?php echo htmlspecialchars($a['penulis_artikel']); ?>
                                </div>
                                
                                <h5 class="card-title fw-bold mb-3">
                                    <a href="artikelDetail.php?id=<?php echo $a['id_artikel']; ?>" class="text-decoration-none text-dark stretched-link">
                                        <?php echo htmlspecialchars($a['judul_artikel']); ?>
                                    </a>
                                </h5>
                                
                                <p class="card-text text-muted small flex-grow-1">
                                    <?php echo $a['preview_fmt']; ?>
                                </p>
                                
                                <div class="mt-3 text-end">
                                    <span class="text-primary fw-semibold small">Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-light text-center py-5 shadow-sm" role="alert">
                        <h4 class="text-muted"><i class="bi bi-newspaper me-2"></i>Artikel tidak ditemukan.</h4>
                        <?php if(!empty($cari)): ?>
                            <p>Tidak ada hasil untuk kata kunci: "<strong><?php echo htmlspecialchars($cari); ?></strong>"</p>
                        <?php endif; ?>
                    </div>
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

            <span class="text-muted fw-bold">Halaman <?php echo $page; ?> dari <?php echo $totalPages; ?></span>

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
    
    <div id="footer-container"></div>
    <script src="js/footer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dropdown.js"></script>
</body>
</html>

<?php
    pg_close($conn);
?>