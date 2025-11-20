<?php
require 'config.php';

// ==========================================================
// 1. QUERY BAGIAN WEBSITE RESMI (NAV, LOGO, DLL) - JANGAN UBAH
// ==========================================================

// QUERY NAV
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

// QUERY VISI MISI
$qVisi = "SELECT * FROM vw_visi_lab";
$rVisi = pg_query($conn, $qVisi);
$rowVisi = pg_fetch_assoc($rVisi);

$qMisi = "SELECT * FROM vw_misi_lab";
$rMisi = pg_query($conn, $qMisi);
$rowMisi = pg_fetch_assoc($rMisi);


// ==========================================================
// 2. LOGIKA FITUR GALERI (MASONRY + PAGINATION)
// ==========================================================

$limit = 14; // Wajib 14 agar layout grid pas

// Ambil halaman saat ini
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Hitung Total Data
$queryTotal = "SELECT count(*) as total FROM galeri";
$resultTotal = pg_query($conn, $queryTotal);
$rowTotal = pg_fetch_assoc($resultTotal);
$totalData = $rowTotal['total'];

// Hitung Total Halaman
$totalPage = ceil($totalData / $limit);

// Ambil Data Galeri (Terbaru diatas)
$query = "SELECT * FROM galeri ORDER BY id_galeri DESC LIMIT $limit OFFSET $start";
$result = pg_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorium Software Engineer - Galeri</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleIndex.css">
    
    <link rel="stylesheet" href="css/styleGalery.css">
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
                <h1 class="hero-title">GALERI</h1>
            </div>
        </div>
    </div>
    </div>
    
    <div class="container py-5">
        <h2 class="gallery-title">Potret Kegiatan dan Prestasi Kami</h2>

        <div class="custom-grid-container">
            
            <?php
            if (pg_num_rows($result) > 0) {
                while($row = pg_fetch_assoc($result)) {
                    $gambar = $row['url_gambar_galeri']; 
                    $deskripsi = $row['deskripsi_galeri'];
            ?>
            
                <div class="grid-item" onclick="previewImage(this)">
                    <img src="<?php echo htmlspecialchars($gambar); ?>" alt="<?php echo htmlspecialchars($deskripsi); ?>">
                    <div class="grid-overlay">
                        <div class="overlay-content">
                            <h5 class="overlay-title"><?php echo htmlspecialchars($deskripsi); ?></h5>
                            <i class="bi bi-plus-circle fs-3 text-white"></i>
                        </div>
                    </div>
                </div>

            <?php 
                } 
            } else {
                echo "<div style='grid-column: span 4; text-align: center; padding: 50px;'>Belum ada foto yang diunggah.</div>";
            }
            ?>

        </div>
        
        <?php if($totalPage > 1): ?>
        <div class="d-flex justify-content-center gap-3 mt-5">
            
            <?php if($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="btn btn-outline-dark px-4 rounded-pill shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i> Sebelumnya
                </a>
            <?php endif; ?>

            <span class="align-self-center fw-bold text-muted">
                Halaman <?php echo $page; ?> dari <?php echo $totalPage; ?>
            </span>

            <?php if($page < $totalPage): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="btn btn-dark px-4 rounded-pill shadow-sm">
                    Selanjutnya <i class="bi bi-arrow-right ms-2"></i>
                </a>
            <?php endif; ?>
            
        </div>
        <?php endif; ?>

    </div> <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
          <div class="modal-body p-0 text-center position-relative">
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <img src="" id="modalImage" class="img-fluid rounded shadow-lg">
          </div>
        </div>
      </div>
    </div>

    <div id="footer-container"></div>
    <script src="js/footer.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dropdown.js"></script>
    <script src="js/galeri.js"></script>
</body>
</html>

<?php
pg_close($conn);
?>