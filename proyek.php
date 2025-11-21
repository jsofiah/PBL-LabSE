<?php
require 'config.php';


$filter = isset($_GET['filter']) ? $_GET['filter'] : 'terbaru';
$cari   = isset($_GET['cari']) ? pg_escape_string($conn, $_GET['cari']) : '';
$orderDirection = ($filter == 'terlama') ? 'ASC' : 'DESC';

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

$whereClause = "";
if (!empty($cari)) {
    $whereClause = "WHERE judul_proyek ILIKE '%$cari%' OR preview_proyek ILIKE '%$cari%'";
}

$qTotal = "SELECT COUNT(*) as total FROM proyek $whereClause";
$rTotal = pg_query($conn, $qTotal);
$rowTotal = pg_fetch_assoc($rTotal);
$totalData = $rowTotal['total'];
$totalPages = ceil($totalData / $limit);

$qProyek = "SELECT * FROM proyek 
            $whereClause 
            ORDER BY tanggal_terbit_proyek $orderDirection 
            LIMIT $limit OFFSET $start";
$rProyek = pg_query($conn, $qProyek);



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
    $url = "dosen_detail.php?id=" . $d['id_dosen'];
    $navItems[3]['subnav'][] = ['nama_subnav' => $d['nama_dosen'], 'url_subnav' => $url];
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
    <title>Proyek - Laboratorium Software Engineer</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleIndex.css">
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
            <div class="hero-frame" style="height: 230px;">
                <img src="img/background_index.jpg" alt="Lab Background">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <h1 class="hero-title">PROJEK</h1>
                </div>
            </div>
        </div>
    </div>
    
    <div class="main-content-wrapper">
        <div class="main-content-padding">
        
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
                
                <?php
                if (pg_num_rows($rProyek) > 0) {
                    while ($p = pg_fetch_assoc($rProyek)) {
                        $id      = $p['id_proyek'];
                        $judul   = $p['judul_proyek'];
                        $preview = $p['preview_proyek'];
                        $gambar  = $p['url_gambar_proyek1']; 
                        $tanggal = date('d F Y', strtotime($p['tanggal_terbit_proyek']));
                ?>

                <div class="card project-card mb-4">
                    <div class="row g-0 align-items-center h-100">
                        <div class="col-md-4">
                            <div class="project-img-wrapper">
                                <img src="<?php echo htmlspecialchars($gambar); ?>" class="project-img" alt="Thumbnail">
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="card-body-custom">
                                <div>
                                    <h4 class="project-title"><?php echo htmlspecialchars($judul); ?></h4>
                                    <p class="project-desc">
                                        <?php echo htmlspecialchars($preview); ?>
                                    </p>
                                </div>
                                
                                <div class="d-flex align-items-center gap-4 mt-3">
                                    <span class="project-date">
                                        <i class="bi bi-calendar3 me-1"></i> <?php echo $tanggal; ?>
                                    </span>
                                    
                                    <a href="detail_proyek.php?id=<?php echo $id; ?>" class="btn-read-more">Baca selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php 
                    } 
                } else {
                    echo '<div class="alert alert-light text-center py-5 shadow-sm" role="alert">
                            <h4 class="text-muted"><i class="bi bi-folder-x me-2"></i>Proyek tidak ditemukan.</h4>
                          </div>';
                }
                ?>

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