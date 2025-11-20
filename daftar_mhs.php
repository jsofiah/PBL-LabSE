<?php
    require 'config.php';
    $limit = 10; 
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page > 1) ? ($page * $limit) - $limit : 0;
    $qTotalMhs = "SELECT COUNT(id_mhs) as total FROM mhs_segeeks";
    $rTotalMhs = pg_query($conn, $qTotalMhs);
    $rowTotalMhs = pg_fetch_assoc($rTotalMhs);
    $totalData = $rowTotalMhs['total'];
    $totalPages = ceil($totalData / $limit);

    $qMhs = "
        SELECT 
            m.id_mhs,
            m.nama_mhs,
            m.prodi_mhs,
            m.angkatan_mhs,

            -- Subquery 1: Ambil Semua Bidang Keahlian (Digabung Koma)
            (
                SELECT STRING_AGG(bk.nama_keahlian, ', ')
                FROM memuat_mhs_keahlian mmk
                JOIN bidang_keahlian bk ON mmk.id_keahlian = bk.id_keahlian
                WHERE mmk.id_mhs = m.id_mhs
            ) as list_bidang,

            -- Subquery 2: Ambil HANYA 3 Proyek Terbaru
            (
                SELECT STRING_AGG(temp_proyek.judul_proyek, ', ')
                FROM (
                    SELECT p.judul_proyek
                    FROM proyek_mhs pm
                    JOIN proyek p ON pm.id_proyek = p.id_proyek
                    WHERE pm.id_mhs = m.id_mhs
                    -- Urutkan dari tanggal terbaru, lalu ID terbesar
                    ORDER BY p.tanggal_terbit_proyek DESC, p.id_proyek DESC
                    LIMIT 3 -- BATASI CUMA 3
                ) as temp_proyek
            ) as list_proyek

        FROM 
            mhs_segeeks m
        ORDER BY 
            m.nama_mhs ASC
        LIMIT $limit OFFSET $offset
    ";
    
    $rMhs = pg_query($conn, $qMhs);

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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa - Laboratorium Software Engineer</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/styleIndex.css">
    <link rel="stylesheet" href="css/styleDaftarMhs.css">
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
                    <h1 class="hero-title"> Daftar Mahasiswa SE Geeks </h1>
                </div>
            </div>
        </div>
    </div>
    

    <div class="container my-5">
        
        <h2 class="text-center fw-bold text-uppercase text-secondary mb-4" style="letter-spacing: 1px;">
            TERDAFTAR, TERLIBAT, BERKARYA
        </h2>

        <div class="table-responsive shadow-sm rounded">
            <table class="table table-borderless mb-0">
                <thead>
                    <tr>
                        <th class="custom-header text-center" style="width: 5%;">NO</th>
                        <th class="custom-header text-start" style="width: 25%;">NAMA LENGKAP</th>
                        <th class="custom-header text-center" style="width: 15%;">PRODI</th>
                        <th class="custom-header text-center" style="width: 10%;">ANGKATAN</th>
                        <th class="custom-header text-start" style="width: 20%;">BIDANG</th>
                        <th class="custom-header text-start" style="width: 25%;">PROYEK Terbaru</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (pg_num_rows($rMhs) > 0) {
                        $no = $offset + 1; 
                        while ($row = pg_fetch_assoc($rMhs)) {
                            $bidang = $row['list_bidang'] ? $row['list_bidang'] : '-';
                            $proyek = $row['list_proyek'] ? $row['list_proyek'] : '-';
                    ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($row['nama_mhs']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row['prodi_mhs']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row['angkatan_mhs']); ?></td>
                            <td><?php echo htmlspecialchars($bidang); ?></td>
                            <td><?php echo htmlspecialchars($proyek); ?></td>
                        </tr>
                    <?php 
                        }
                    } else {
                        echo '<tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data mahasiswa.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 px-2">
            
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="btn btn-nav-custom">
                    <i class="fa-solid fa-caret-left me-1"></i> Previous
                </a>
            <?php else: ?>
                <button class="btn btn-nav-custom disabled" disabled>
                    <i class="fa-solid fa-caret-left me-1"></i> Previous
                </button>
            <?php endif; ?>

            <span class="text-muted fw-medium">
                Slide <?php echo $page; ?> of <?php echo $totalPages > 0 ? $totalPages : 1; ?>
            </span>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="btn btn-nav-custom">
                    Next <i class="fa-solid fa-caret-right ms-1"></i>
                </a>
            <?php else: ?>
                <button class="btn btn-nav-custom disabled" disabled>
                    Next <i class="fa-solid fa-caret-right ms-1"></i>
                </button>
            <?php endif; ?>

        </div>

    </div>

    <div id="footer-container"></div>
    <script src="js/footer.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dropdown.js"></script>
</body>
</html>