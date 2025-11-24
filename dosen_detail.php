<?php
// profil_dosen.php
require 'config.php';

/*
  Memuat data navbar / logo (template yang Anda minta tetap digunakan)
*/
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

$qLogo = "SELECT * FROM vw_logo_cta";
$rLogo = pg_query($conn, $qLogo);
$rowLogo = $rLogo ? pg_fetch_assoc($rLogo) : null;

/* ========== Ambil id dosen dari query string ========== */
if (!isset($_GET['id'])) {
    // bisa diganti redirect ke halaman tim dosen
    die('ID dosen tidak diberikan.'); 
}
$id = intval($_GET['id']);
if ($id <= 0) {
    die('ID dosen tidak valid.');
}

/* ========== Fungsi helper ========== */
function fetch_one($conn, $sql, $params = []) {
    $res = pg_query_params($conn, $sql, $params);
    if (!$res) return false;
    return pg_fetch_assoc($res);
}

function fetch_all($conn, $sql, $params = []) {
    $res = pg_query_params($conn, $sql, $params);
    if (!$res) return false;
    $rows = [];
    while ($r = pg_fetch_assoc($res)) $rows[] = $r;
    return $rows;
}

/* ========== Ambil data profil dosen ========== */
$sqlProfil = "SELECT * FROM vw_detail_dosen WHERE id_dosen = $1";
$resProfil = pg_query_params($conn, $sqlProfil, [$id]);

if (!$resProfil) {
    // debug: tampilkan error SQL (hapus/ubah di production)
    $err = pg_last_error($conn);
    die("Terjadi kesalahan saat memuat profil dosen: " . htmlspecialchars($err));
}

$profil = pg_fetch_assoc($resProfil);
if (!$profil) {
    die("Profil dosen dengan ID " . htmlspecialchars($id) . " tidak ditemukan.");
}

/* ========== Ambil keahlian, pendidikan, sertifikasi, penelitian ========== */
function getKeahlian($conn, $id_dosen) {
    $sql = "
        SELECT k.nama_keahlian
        FROM dosen_menguasai_keahlian mk
        JOIN bidang_keahlian k ON k.id_keahlian = mk.id_keahlian
        WHERE mk.id_dosen = $1
        ORDER BY k.nama_keahlian
    ";
    return fetch_all($conn, $sql, [$id_dosen]) ?: [];
}

function getPendidikan($conn, $id_dosen) {
    $sql = "
        SELECT universitas, bidang_studi, gelar, tahun_lulus
        FROM riwayat_pendidikan
        WHERE id_dosen = $1
        ORDER BY tahun_lulus ASC NULLS LAST
    ";
    return fetch_all($conn, $sql, [$id_dosen]) ?: [];
}

function getSertifikasi($conn, $id_dosen) {
    $sql = "
        SELECT s.nama_sertifikasi, s.penyelenggara, s.tahun_sertifikasi
        FROM mempunyai_sertifikasi ms
        JOIN sertifikasi s ON s.id_sertifikasi = ms.id_sertifikasi
        WHERE ms.id_dosen = $1
        ORDER BY s.tahun_sertifikasi DESC NULLS LAST
    ";
    return fetch_all($conn, $sql, [$id_dosen]) ?: [];
}

function getPenelitian($conn, $id_dosen) {
    $sql = "
        SELECT judul_penelitian, tahun, abstrak
        FROM penelitian
        WHERE id_dosen = $1
        ORDER BY tahun DESC NULLS LAST
    ";
    return fetch_all($conn, $sql, [$id_dosen]) ?: [];
}

$keahlian    = getKeahlian($conn, $id);
$pendidikan  = getPendidikan($conn, $id);
$sertifikasi = getSertifikasi($conn, $id);
$penelitian  = getPenelitian($conn, $id);

/* ========== Helper escape ========= */
function h($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Profil Dosen - <?= h($profil['nama_dosen']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- bootstrap / icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <!-- style root + khusus halaman -->
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleFooter.css">
    <link rel="stylesheet" href="css/styleDosenDetail.css"> 
    <style>
        /* Penyesuaian kecil (jika perlu) */
        .back-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: none;
            margin-right: 12px;
        }
    </style>
</head>
<body class="dosen-page">

    <!-- LOGO -->
    <div class="logo">
        <?php if ($rowLogo): ?>
            <img src="<?= h($rowLogo['url_logo']) ?>" class="logo-img" alt="LABSE">
        <?php else: ?>
            <img src="img/logo.png" class="logo-img" alt="LABSE">
        <?php endif; ?>
    </div>

    <!-- NAVBAR (template yang sama) -->
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

    <!-- CTA -->
    <?php if($rowLogo): ?>
        <a href="<?php echo htmlspecialchars($rowLogo['link_cta']); ?>" class="cta-button">
            <span class="cta-text"><?php echo htmlspecialchars($rowLogo['judul_cta']); ?></span>
        </a>
    <?php endif; ?>

    <!-- HERO -->
    <div class="hero-wrapper">
        <div class="hero-container">
            <div class="hero-frame">
                <img src="img/bgdosen.webp" alt="Dosen Background">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                <h1 class="hero-title">PROFIL DOSEN</h1>
            </div>
        </div>
    </div>

    <!-- CONTENT MAIN -->
    <div class="container my-5">
        <div class="row">
            <!-- SIDEBAR -->
            <div class="col-lg-3 mb-4">
                <div class="side-menu">
                    <button class="btn btn-primary">Profil</button>
                    <button class="btn btn-outline-primary">Penelitian</button>
                    <button class="btn btn-outline-primary">Publikasi</button>
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <div class="col-lg-9">
                <div class="d-flex align-items-center mb-4">
                    <button class="back-btn" onclick="history.back()" title="Kembali">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <h2 class="fw-bold mb-0">PROFIL DOSEN</h2>
                </div>

                <!-- CARD PROFIL -->
                <div class="profil-card mb-4">
                    <div class="d-flex">
                        <img class="profil-img me-4" src="<?= h($profil['url_foto_dosen'] ?: 'img/avatar-placeholder.png') ?>" alt="<?= h($profil['nama_dosen']) ?>">
                        <div>
                            <?php if (!empty($profil['program_studi'])): ?>
                                <div class="text-muted" style="font-weight:600; color:#133A94;"><?= h($profil['program_studi']) ?></div>
                            <?php endif; ?>
                            <h4 class="mt-1"><?= h($profil['nama_dosen']) ?></h4>
                            <?php if (!empty($profil['email_dosen'])): ?>
                                <p class="mb-1"><?= h($profil['email_dosen']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($profil['jabatan_lab'])): ?>
                                <p class="mb-0"><strong>Jabatan Lab:</strong> <?= h($profil['jabatan_lab']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- KEAHLIAN -->
                <div class="section-card">
                    <h5>Bidang Keahlian / Minat Penelitian</h5>
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

                <!-- RIWAYAT PENDIDIKAN -->
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

                <!-- SERTIFIKASI -->
                <div class="section-card mt-4">
                    <h5>Kompetensi / Sertifikasi</h5>
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

                <!-- (Optional) PENELITIAN -->
                <div class="section-card mt-4">
                    <h5>Penelitian</h5>
                    <?php if (!empty($penelitian)): ?>
                        <ul>
                            <?php foreach ($penelitian as $pen): ?>
                                <li><strong><?= h($pen['judul_penelitian']) ?></strong> <?= $pen['tahun'] ? '(' . h($pen['tahun']) . ')' : '' ?>
                                    <?php if (!empty($pen['abstrak'])): ?>
                                        <div class="text-muted small mt-1"><?= h($pen['abstrak']) ?></div>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">Belum ada data penelitian.</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <!-- footer dynamic -->
    <div id="footer-container"></div>
    <script src="js/footer.js"></script>

    <!-- bootstrap + dropdown -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dropdown.js"></script>
</body>
</html>
