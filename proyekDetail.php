<?php
require 'config.php';

$qNav = "SELECT * FROM vw_nav";
$rNav = pg_query($conn, $qNav);
$navItems = [];
while ($row = pg_fetch_assoc($rNav)) {
    $id = $row['id_nav'];
    if (!isset($navItems[$id])) $navItems[$id] = ['nama' => $row['nama_nav'], 'url' => $row['url_nav'], 'sub' => []];
    if ($row['id_subnav']) $navItems[$id]['sub'][] = ['nama' => $row['nama_subnav'], 'url' => $row['url_subnav']];
}

$qDosenMenu = "SELECT id_dosen, nama_dosen FROM vw_detail_dosen ORDER BY nama_dosen";
$rDosenMenu = pg_query($conn, $qDosenMenu);
while ($d = pg_fetch_assoc($rDosenMenu)) {
    $navItems[3]['sub'][] = ['nama' => $d['nama_dosen'], 'url' => "dosen_detail.php?id=" . $d['id_dosen']];
}

$rLogo = pg_query($conn, "SELECT * FROM vw_logo_cta");
$rowLogo = pg_fetch_assoc($rLogo);

$id_proyek = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$qDetail = "SELECT * FROM vw_proyek WHERE id_proyek = $id_proyek";
$rDetail = pg_query($conn, $qDetail);
$data    = pg_fetch_assoc($rDetail);

if (!$data) {
    header("Location: proyek.php");
    exit;
}

$judul   = htmlspecialchars($data['judul_proyek']);
$isi     = nl2br($data['isi_proyek']);
$penulis = htmlspecialchars($data['penulis_proyek']);
$tanggal = date('l, d M Y H:i', strtotime($data['tanggal_terbit_proyek'])) . ' WIB';

$img1 = htmlspecialchars($data['url_gambar_proyek1']);
$img2 = !empty($data['url_gambar_proyek2']) ? htmlspecialchars($data['url_gambar_proyek2']) : $img1;
$img3 = !empty($data['url_gambar_proyek3']) ? htmlspecialchars($data['url_gambar_proyek3']) : $img1;

$qDosenP = "SELECT d.nama_dosen FROM proyek_dosen pd JOIN dosen d ON pd.id_dosen = d.id_dosen WHERE pd.id_proyek = $id_proyek LIMIT 1";
$rDosenP = pg_query($conn, $qDosenP);
$dosen   = pg_fetch_assoc($rDosenP);
$namaDosen = $dosen ? $dosen['nama_dosen'] : "Belum Ditentukan";

$qMhs = "SELECT m.nama_mhs FROM proyek_mhs pm JOIN mhs_segeeks m ON pm.id_mhs = m.id_mhs WHERE pm.id_proyek = $id_proyek";
$rMhs = pg_query($conn, $qMhs);
$listAnggota = [];
while ($m = pg_fetch_assoc($rMhs)) {
    $listAnggota[] = $m['nama_mhs'];
}

$qLain = "SELECT id_proyek, judul_proyek FROM vw_proyek WHERE id_proyek != $id_proyek ORDER BY tanggal_terbit_proyek DESC LIMIT 3";
$rLain = pg_query($conn, $qLain);
$listLain = [];
while ($l = pg_fetch_assoc($rLain)) {
    $listLain[] = $l;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $judul; ?> - Lab SE</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleIndex.css">
    <link rel="stylesheet" href="css/styleProyekDetail.css">
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
                <?php if (count($nav['sub']) > 0): ?>
                    <li class="dropdown">
                        <a href="<?php echo htmlspecialchars($nav['url']); ?>" class="dropbtn">
                            <?php echo htmlspecialchars($nav['nama']); ?> <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="dropdown-content">
                            <div class="dropdown-scroll overflow-auto" style="max-height: 250px;">
                                <?php foreach ($nav['sub'] as $sub): ?>
                                    <a href="<?php echo htmlspecialchars($sub['url']); ?>">
                                        <?php echo htmlspecialchars($sub['nama']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($nav['url']); ?>">
                            <?php echo htmlspecialchars($nav['nama']); ?>
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

    <div class="detail-wrapper">
        
        <div class="gallery-grid">
            <div class="img-box img-main">
                <img src="<?php echo $img1; ?>" alt="Main Project Image">
            </div>
            <div class="img-box img-sub-left">
                <img src="<?php echo $img2; ?>" alt="Sub Project Image">
            </div>
            <div class="img-box img-sub-right">
                <img src="<?php echo $img3; ?>" alt="Sub Project Image">
            </div>
        </div>

        <h1 class="detail-title">
            <?php echo $judul; ?>
        </h1>

        <div class="row">
            
            <div class="col-md-8 pe-md-5">
                <div class="content-text">
                    <?php echo $isi; ?>
                </div>
            </div>

            <div class="col-md-4">
                
                <div class="sidebar-card">
                    <div class="dosen-name">
                        <?php echo htmlspecialchars($namaDosen); ?>
                    </div>

                    <hr class="sidebar-divider">
                    <h6 class="sidebar-subtitle">Anggota Proyek</h6>
                    <ul class="member-list">
                        <?php if (!empty($listAnggota)): ?>
                            <?php foreach ($listAnggota as $mhs): ?>
                                <li><?php echo htmlspecialchars($mhs); ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Belum ada anggota</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="sidebar-card">
                    <h6 class="sidebar-subtitle mb-3">Proyek Lainnya</h6>
                    
                    <?php if (!empty($listLain)): ?>
                        <?php foreach ($listLain as $lain): ?>
                            <div class="other-project-item">
                                <a href="proyekDetail.php?id=<?php echo $lain['id_proyek']; ?>" class="other-project-link">
                                    <?php echo htmlspecialchars($lain['judul_proyek']); ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted small">Tidak ada proyek lain.</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <div class="meta-footer">
            <div class="meta-text">
                <strong><?php echo $penulis; ?></strong> - Lab SE<br>
                <span class="text-muted"><?php echo $tanggal; ?></span>
            </div>
            <div class="meta-line"></div>
        </div>

    </div>

    <div id="footer-container"></div>
    <script src="js/footer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dropdown.js"></script>
    <script src="js/script.js"></script> 
</body>
</html>

<?php
pg_close($conn);
?>