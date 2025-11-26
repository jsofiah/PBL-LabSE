<?php
    require 'config.php';
    $qNav = "SELECT * FROM vw_nav";
    $rNav = pg_query($conn, $qNav);

    $navItems = [];
    if ($rNav) {
        while ($rowNav = pg_fetch_assoc($rNav)) {
            $id_nav = $rowNav['id_nav'];
            if (!isset($navItems[$id_nav])) {
                $navItems[$id_nav] = [
                    'nama_nav' => $rowNav['nama_nav'],
                    'url_nav'  => $rowNav['url_nav'],
                    'subnav'   => []
                ];
            }
            if (!empty($rowNav['id_subnav'])) {
                $navItems[$id_nav]['subnav'][] = [
                    'nama_subnav' => $rowNav['nama_subnav'],
                    'url_subnav'  => $rowNav['url_subnav']
                ];
            }
        }
    }

    $qDosen = "SELECT id_dosen, nama_dosen FROM vw_detail_dosen ORDER BY nama_dosen";
        $rDosen = pg_query($conn, $qDosen);

        while ($d = pg_fetch_assoc($rDosen)) {
            $url = "dosen_detail.php?id=" . $d['id_dosen'];
            $navItems[3]['subnav'][] = [
                'nama_subnav' => $d['nama_dosen'],
                'url_subnav'  => $url
            ];
        }

    $qLogo = "SELECT * FROM vw_logo_cta";
    $rLogo = pg_query($conn, $qLogo);
    $rowLogo = $rLogo ? pg_fetch_assoc($rLogo) : null;


    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        header('Location: daftar_dosen.php');
        exit;
    }

    $qDosenDetail = "SELECT * FROM vw_detail_dosen WHERE id_dosen = $id";
    $rDosenDetail = pg_query($conn, $qDosenDetail);
    $profil = pg_fetch_assoc($rDosenDetail);

    if (!$profil) {
        header('Location: daftar_dosen.php');
        exit;
    }

    $emailDosen = $profil['email_dosen'] ?? '';
    $default_avatar = 'img/default_dosen.png';

    $qKeahlian = "SELECT nama_keahlian FROM vw_dosen_keahlian WHERE id_dosen = $id ORDER BY nama_keahlian";
    $rKeahlian = pg_query($conn, $qKeahlian);
    $keahlian = $rKeahlian ? pg_fetch_all($rKeahlian) : [];

    $qPendidikan = "SELECT * FROM vw_riwayat_pendidikan WHERE id_dosen = $id ORDER BY tahun_lulus DESC";
    $rPendidikan = pg_query($conn, $qPendidikan);
    $pendidikan = $rPendidikan ? pg_fetch_all($rPendidikan) : [];

    $qPendidikanTerakhir = "SELECT * FROM vw_riwayat_pendidikan WHERE id_dosen = $id ORDER BY tahun_lulus DESC LIMIT 1";
    $rPendidikanTerakhir = pg_query($conn, $qPendidikanTerakhir);
    $pendidikanTerakhir = pg_fetch_assoc($rPendidikanTerakhir);

    $qSertifikasi = "SELECT * FROM vw_sertifikasi_dosen WHERE id_dosen = $id ORDER BY tahun_sertifikasi DESC ";
    $rSertifikasi = pg_query($conn, $qSertifikasi);
    $sertifikasi = $rSertifikasi ? pg_fetch_all($rSertifikasi) : [];

    function h($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Profil Dosen - <?= h($profil['nama_dosen']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleFooter.css">
    <link rel="stylesheet" href="css/styleDosenDetail.css">
</head>
<body>
    <div class="logo">
        <?php if ($rowLogo): ?>
          <img src="<?= h($rowLogo['url_logo']) ?>" class="logo-img" alt="LABSE">
        <?php else: ?>
            <img src="img/logo.png" class="logo-img" alt="LABSE">
        <?php endif; ?>
    </div>

    <nav>
        <ul id="nav-list" class="nav-collapse">
            <?php foreach ($navItems as $nav): ?>
                <?php if (!empty($nav['subnav'])): ?>
                    <li class="dropdown">
                        <a href="<?= h($nav['url_nav']) ?>" class="dropbtn"><?= h($nav['nama_nav']) ?> <i class="bi bi-chevron-down"></i></a>
                        <div class="dropdown-content">
                            <div class="dropdown-scroll overflow-auto" style="max-height: 250px;">
                                <?php foreach ($nav['subnav'] as $sub): ?>
                                    <a href="<?= h($sub['url_subnav']) ?>"><?= h($sub['nama_subnav']) ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="<?= h($nav['url_nav']) ?>"><?= h($nav['nama_nav']) ?></a></li>
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
                <img src="img/gedung_lab.png" alt="Dosen Background">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                <h1 class="hero-title">PROFIL DOSEN</h1>
            </div>
        </div>
        <div class="container my-5" style="margin-left: 0px; padding-left: 0px; margin-right: 0px; padding-right: 0px;">
            <div class="row" style="width: 1350px;">
                <div class="sidebar-wrapper">
                    <a href="dosen_detail.php?id=<?php echo $id; ?>" class="btn-menu active">Profil</a>
                    <a href="dosen_penelitian.php?id=<?php echo $id; ?>" class="btn-menu">Penelitian</a>
                    <a href="dosen_publikasi.php?id=<?php echo $id; ?>" class="btn-menu">Publikasi</a>
                </div>
    
                <div class="col-lg-9">
                    <div class="d-flex align-items-center mb-4">
                        <a href="dosen_detail.php?id=<?php echo $id; ?>" class="btn-back me-3"><i class="bi bi-arrow-left"></i></a>
                        <h2 class="fw-bold mb-0">PROFIL DOSEN</h2>
                    </div>
    
                    <div class="profil-card mb-4">
                        <div class="d-flex">
                            <div class="profil-img-wrapper me-4">
                                <img class="profil-img"
                                    src="<?= h($profil['url_foto_dosen'] ?: 'img/avatar-placeholder.png') ?>"
                                    alt="<?= h($profil['nama_dosen']) ?>">
                            </div>
    
                            <div class="profil-text">
                                <?php if (!empty($pendidikanTerakhir)): ?>
                                    <div class="text-primary fw-semibold">
                                        <?= h($pendidikanTerakhir['jenjang']) ?> – <?= h($pendidikanTerakhir['universitas']) ?>
                                    </div>
                                <?php endif; ?>
    
                                <h4 class="mt-1"><?= h($profil['nama_dosen']) ?></h4>
    
                                <?php if (!empty($profil['email_dosen'])): ?>
                                    <p><?= h($profil['email_dosen']) ?></p>
                                <?php endif; ?>
    
                                <?php if (!empty($profil['jabatan_lab'])): ?>
                                    <p><strong>Jabatan Lab:</strong> <?= h($profil['jabatan_lab']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
    
                    <div class="section-card">
                        <h5>Bidang Keahlian</h5>
                        <?php if (!empty($keahlian)): ?>
                            <ul>
                                <?php foreach ($keahlian as $k): ?>
                                    <li><?= h($k['nama_keahlian']) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">Belum ada data keahlian.</p>
                        <?php endif; ?>
                    </div>
    
                    <div class="section-card mt-4">
                        <h5>Riwayat Pendidikan</h5>
                        <?php if (!empty($pendidikan)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:60px">No</th>
                                            <th>Perguruan Tinggi</th>
                                            <th>Bidang Studi</th>
                                            <th>Gelar</th>
                                            <th style="width:120px">Tahun Ijazah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no=1; foreach ($pendidikan as $p): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= h($p['universitas']) ?></td>
                                                <td><?= h($p['bidang_studi']) ?></td>
                                                <td><?= h($p['gelar']) ?></td>
                                                <td><?= h($p['tahun_lulus']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">Belum ada data riwayat pendidikan.</p>
                        <?php endif; ?>
                    </div>
    
                    <div class="section-card mt-4">
                        <h5>Sertifikasi</h5>
                        <?php if (!empty($sertifikasi)): ?>
                            <ul>
                                <?php foreach ($sertifikasi as $s): ?>
                                    <li><?= h($s['nama_sertifikasi']) ?> – <?= h($s['penyelenggara']) ?> (<?= h($s['tahun_sertifikasi']) ?>)</li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted mb-0">Belum ada data sertifikasi.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTENT MAIN -->

    <div id="footer-container"></div>
    <script src="js/footer.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dropdown.js"></script>
</body>
</html>
