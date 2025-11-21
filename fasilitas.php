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

    function getFasilitas($conn, $jenis) {
    $jenis = intval($jenis);
    return pg_query($conn, "SELECT * FROM fasilitas WHERE id_jenisFasilitas=$jenis ORDER BY id_fasilitas");
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fasilitas - Laboratorium SE</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleIndex.css">
    <link rel="stylesheet" href="css/styleFasilitas.css">
</head>

<body class="fasilitas-page">

<!-- LOGO -->
<div class="logo">
    <?php if ($rowLogo): ?>
        <img src="<?= htmlspecialchars($rowLogo['url_logo']) ?>" class="logo-img" alt="LABSE">
    <?php else: ?>
        <img src="img/logo.png" class="logo-img" alt="LABSE">
    <?php endif; ?>
</div>

<!-- NAVBAR (TIDAK DIUBAH) -->
<nav>
    <ul id="nav-list" class="nav-collapse">
        <?php foreach ($navItems as $nav): ?>
            <?php if (count($nav['subnav']) > 0): ?>
                <li class="dropdown">
                    <a href="<?= htmlspecialchars($nav['url_nav']) ?>" class="dropbtn">
                        <?= htmlspecialchars($nav['nama_nav']) ?>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="dropdown-content">
                        <div class="dropdown-scroll overflow-auto" style="max-height: 250px;">
                            <?php foreach ($nav['subnav'] as $sub): ?>
                                <a href="<?= htmlspecialchars($sub['url_subnav']) ?>">
                                    <?= htmlspecialchars($sub['nama_subnav']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </li>
            <?php else: ?>
                <li>
                    <a href="<?= htmlspecialchars($nav['url_nav']) ?>">
                        <?= htmlspecialchars($nav['nama_nav']) ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</nav>

<!-- CTA BUTTON -->
<?php if ($rowLogo): ?>
<a href="<?= htmlspecialchars($rowLogo['link_cta']) ?>" class="cta-button">
    <span class="cta-text"><?= htmlspecialchars($rowLogo['judul_cta']) ?></span>
</a>
<?php endif; ?>

<!-- HERO -->
<div class="fasilitas-hero">
    <img src="img/bgfasilitas.png" class="hero-img" alt="">
    <div class="hero-overlay"></div>
    <h1 class="hero-title">FASILITAS</h1>
</div>

<!-- CONTENT -->
<div class="container my-5">

    <h2 class="fw-bold text-center mb-5">SARANA DAN PRASARANA PENDUKUNG</h2>

    <!-- AKADEMIK -->
    <h3>FASILITAS AKADEMIK</h3>
    <div class="fasilitas-slider">
        <button class="slide-btn left" onclick="slideLeft('akad')">&#10094;</button>

        <div class="fasilitas-track" id="track-akad">
            <?php $r = getFasilitas($conn, 1); while ($row = pg_fetch_assoc($r)): ?>
                <div class="fasilitas-card">
                    <img src="<?= htmlspecialchars($row['url_gambar_fasilitas']) ?>">
                    <h5><?= htmlspecialchars($row['judul_fasilitas']) ?></h5>
                    <p><?= htmlspecialchars($row['isi_fasilitas']) ?></p>
                </div>
            <?php endwhile; ?>
        </div>

        <button class="slide-btn right" onclick="slideRight('akad')">&#10095;</button>
    </div>

    <hr class="my-5">

    <!-- ADMINISTRASI -->
    <h3>FASILITAS ADMINISTRASI</h3>
    <div class="fasilitas-slider">
        <button class="slide-btn left" onclick="slideLeft('adm')">&#10094;</button>

        <div class="fasilitas-track" id="track-adm">
            <?php $r = getFasilitas($conn, 3); while ($row = pg_fetch_assoc($r)): ?>
                <div class="fasilitas-card">
                    <img src="<?= htmlspecialchars($row['url_gambar_fasilitas']) ?>">
                    <h5><?= htmlspecialchars($row['judul_fasilitas']) ?></h5>
                    <p><?= htmlspecialchars($row['isi_fasilitas']) ?></p>
                </div>
            <?php endwhile; ?>
        </div>

        <button class="slide-btn right" onclick="slideRight('adm')">&#10095;</button>
    </div>

    <hr class="my-5">

    <!-- NON AKADEMIK -->
    <h3>FASILITAS NON AKADEMIK</h3>
    <div class="fasilitas-slider">
        <button class="slide-btn left" onclick="slideLeft('non')">&#10094;</button>

        <div class="fasilitas-track" id="track-non">
            <?php $r = getFasilitas($conn, 2); while ($row = pg_fetch_assoc($r)): ?>
                <div class="fasilitas-card">
                    <img src="<?= htmlspecialchars($row['url_gambar_fasilitas']) ?>">
                    <h5><?= htmlspecialchars($row['judul_fasilitas']) ?></h5>
                    <p><?= htmlspecialchars($row['isi_fasilitas']) ?></p>
                </div>
            <?php endwhile; ?>
        </div>

        <button class="slide-btn right" onclick="slideRight('non')">&#10095;</button>
    </div>

</div>

<!-- FOOTER -->
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
