<?php
require 'config.php';

/* LOAD NAVBAR */

$qNav = "SELECT * FROM vw_nav";
$rNav = pg_query($conn, $qNav);

$navItems = [];
while ($rowNav = pg_fetch_assoc($rNav)) {
    $id_nav = $rowNav['id_nav'];

    if (!isset($navItems[$id_nav])) {
        $navItems[$id_nav] = [
            'nama_nav' => $rowNav['nama_nav'],
            'url_nav'  => $rowNav['url_nav'],
            'subnav'   => []
        ];
    }

    if ($rowNav['id_subnav']) {
        $navItems[$id_nav]['subnav'][] = [
            'nama_subnav' => $rowNav['nama_subnav'],
            'url_subnav'  => $rowNav['url_subnav']
        ];
    }
}

/* Tambahkan list dosen ke NAV */
$qDosen = "SELECT id_dosen, nama_dosen FROM dosen ORDER BY nama_dosen";
$rDosen = pg_query($conn, $qDosen);

while ($d = pg_fetch_assoc($rDosen)) {
    $url = "dosen_detail.php?id=" . $d['id_dosen'];
    $navItems[3]['subnav'][] = [
        'nama_subnav' => $d['nama_dosen'],
        'url_subnav' => $url
    ];
}

/* LOGO */
$qLogo = "SELECT * FROM vw_logo_cta";
$rLogo = pg_query($conn, $qLogo);
$rowLogo = pg_fetch_assoc($rLogo);


/* AMBIL DATA DOSEN */
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$qProfil = "SELECT * FROM dosen WHERE id_dosen = $id";
$rProfil = pg_query($conn, $qProfil);
$profil = pg_fetch_assoc($rProfil);

if (!$profil) {
    die("<h2>Dosen tidak ditemukan.</h2>");
}

/* KEAHLIAN */
$qKeahlian = "
    SELECT k.nama_keahlian 
    FROM dosen_menguasai_keahlian mk
    JOIN bidang_keahlian k ON k.id_keahlian = mk.id_keahlian
    WHERE mk.id_dosen = $id
";
$rKeahlian = pg_query($conn, $qKeahlian);

/* RIWAYAT PENDIDIKAN */
$qPendidikan = "
    SELECT * 
    FROM riwayat_pendidikan 
    WHERE id_dosen = $id
    ORDER BY tahun_lulus ASC
";
$rPendidikan = pg_query($conn, $qPendidikan);

/* PENELITIAN */
$qPenelitian = "
    SELECT * FROM penelitian 
    WHERE id_dosen = $id
";
$rPenelitian = pg_query($conn, $qPenelitian);

/* SERTIFIKASI */
$qSertifikasi = "
    SELECT s.nama_sertifikasi, s.penyelenggara, s.tahun_sertifikasi
    FROM mempunyai_sertifikasi ms
    JOIN sertifikasi s ON s.id_sertifikasi = ms.id_sertifikasi
    WHERE ms.id_dosen = $id
";
$rSertifikasi = pg_query($conn, $qSertifikasi);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Dosen</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleFooter.css">
    <link rel="stylesheet" href="css/styleDosenDetail.css">

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .profil-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        .profil-img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            background: #eee;
        }
    </style>
</head>
<body>

<!-- ================= NAVBAR ================= -->
<div class="logo">
    <?php if ($rowLogo): ?>
        <img src="<?= $rowLogo['url_logo']; ?>" alt="LAB SE" class="logo-img">
    <?php else: ?>
        <img src="img/logo.png" class="logo-img">
    <?php endif; ?>
</div>

<nav>
    <ul id="nav-list" class="nav-collapse">
        <?php foreach ($navItems as $nav): ?>
            <?php if (count($nav['subnav']) > 0): ?>
                <li class="dropdown">
                    <a href="<?= $nav['url_nav']; ?>" class="dropbtn">
                        <?= $nav['nama_nav']; ?> <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="dropdown-content">
                        <div class="dropdown-scroll overflow-auto" style="max-height: 250px;">
                            <?php foreach ($nav['subnav'] as $sub): ?>
                                <a href="<?= $sub['url_subnav']; ?>"><?= $sub['nama_subnav']; ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </li>
            <?php else: ?>
                <li><a href="<?= $nav['url_nav']; ?>"><?= $nav['nama_nav']; ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</nav>

<!-- CONTENT -->
<div class="container my-5">

    <!-- PROFIL -->
    <div class="profil-card">
        <div class="d-flex">
            <img class="profil-img me-4" src="<?= $profil['url_foto_dosen']; ?>">
            <div>
                <h4><b><?= $profil['nama_dosen']; ?></b></h4>
                <p><?= $profil['email_dosen']; ?></p>
                <p><b>Jabatan Lab:</b> <?= $profil['jabatan_lab']; ?></p>
            </div>
        </div>
    </div>

    <!-- KEAHLIAN -->
    <div class="profil-card">
        <h5><b>Bidang Keahlian / Minat Penelitian</b></h5>
        <ul>
        <?php while ($k = pg_fetch_assoc($rKeahlian)): ?>
            <li><?= $k['nama_keahlian']; ?></li>
        <?php endwhile; ?>
        </ul>
    </div>

    <!-- PENDIDIKAN -->
    <div class="profil-card">
        <h5><b>Riwayat Pendidikan</b></h5>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Perguruan Tinggi</th>
                    <th>Bidang Studi</th>
                    <th>Gelar</th>
                    <th>Tahun Lulus</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($p = pg_fetch_assoc($rPendidikan)): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $p['universitas']; ?></td>
                    <td><?= $p['bidang_studi']; ?></td>
                    <td><?= $p['gelar']; ?></td>
                    <td><?= $p['tahun_lulus']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- SERTIFIKASI -->
    <div class="profil-card">
        <h5><b>Kompetensi / Sertifikasi</b></h5>
        <ul>
        <?php while ($s = pg_fetch_assoc($rSertifikasi)): ?>
            <li><?= $s['nama_sertifikasi']; ?> – <?= $s['penyelenggara']; ?> (<?= $s['tahun_sertifikasi']; ?>)</li>
        <?php endwhile; ?>
        </ul>
    </div>

</div>

<div id="footer-container"></div>
<script src="js/footer.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/dropdown.js"></script>

</body>
</html>
