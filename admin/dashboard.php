<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    $query = "SELECT * FROM get_dashboard_counts()";
    $result = pg_query($conn, $query);
    $data = pg_fetch_assoc($result);

    $queryTopDosen = "
        SELECT 
            d.id_dosen,
            d.nama_dosen,
            d.url_foto_dosen,
            COUNT(DISTINCT pub.id_publikasi) as total_publikasi,
            COUNT(DISTINCT pen.id_penelitian) as total_penelitian,
            COUNT(DISTINCT pd.id_proyek) as total_proyek,
            (COUNT(DISTINCT pub.id_publikasi) + 
             COUNT(DISTINCT pen.id_penelitian) + 
             COUNT(DISTINCT pd.id_proyek)) as total_score
        FROM dosen d
        LEFT JOIN publikasi pub ON d.id_dosen = pub.id_dosen
        LEFT JOIN penelitian pen ON d.id_dosen = pen.id_dosen
        LEFT JOIN proyek_dosen pd ON d.id_dosen = pd.id_dosen
        GROUP BY d.id_dosen, d.nama_dosen, d.url_foto_dosen
        ORDER BY total_score DESC
        LIMIT 3
    ";
    $resultTopDosen = pg_query($conn, $queryTopDosen);

    $queryArtikelDist = "
        SELECT 
            ja.nama_jenisArtikel as kategori,
            COUNT(a.id_artikel) as jumlah
        FROM jenis_artikel ja
        LEFT JOIN artikel a ON ja.id_jenisArtikel = a.id_jenisArtikel
        GROUP BY ja.nama_jenisArtikel
        ORDER BY jumlah DESC
    ";
    $resultArtikelDist = pg_query($conn, $queryArtikelDist);
    $artikelData = [];
    while($row = pg_fetch_assoc($resultArtikelDist)) {
        $artikelData[] = $row;
    }

    $queryTopMhs = "
        SELECT 
            m.id_mhs,
            m.nama_mhs,
            m.angkatan_mhs,
            m.prodi_mhs,
            COUNT(pm.id_proyek) as total_proyek
        FROM mhs_seGeeks m
        LEFT JOIN proyek_mhs pm ON m.id_mhs = pm.id_mhs
        WHERE m.status = true
        GROUP BY m.id_mhs, m.nama_mhs, m.angkatan_mhs, m.prodi_mhs
        HAVING COUNT(pm.id_proyek) > 0
        ORDER BY total_proyek DESC
        LIMIT 5
    ";
    $resultTopMhs = pg_query($conn, $queryTopMhs);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal LAB SE</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleDashboard.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content-area">
        <div class="welcome-header">
            <div class="welcome-content">
                <div class="welcome-text">
                    <h1 class="welcome-title">Dashboard LAB SE</h1>
                    <p class="welcome-subtitle">Selamat datang kembali, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>
                </div>
            </div>
        </div>
        
        <div class="stats-container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card blue">
                        <div class="card-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="card-count"><?php echo number_format($data['jumlah_dosen']); ?></div>
                        <div class="card-label">Dosen</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card yellow">
                        <div class="card-icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <div class="card-count"><?php echo number_format($data['jumlah_artikel']); ?></div>
                        <div class="card-label">Artikel</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card blue">
                        <div class="card-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="card-count"><?php echo number_format($data['jumlah_proyek']); ?></div>
                        <div class="card-label">Proyek</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card yellow">
                        <div class="card-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="card-count"><?php echo number_format($data['jumlah_mhs']); ?></div>
                        <div class="card-label">Mahasiswa seGeeks</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card yellow">
                        <div class="card-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="card-count"><?php echo number_format($data['jumlah_admin']); ?></div>
                        <div class="card-label">Administrator</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card blue">
                        <div class="card-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="card-count"><?php echo number_format($data['jumlah_fasilitas']); ?></div>
                        <div class="card-label">Fasilitas</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card yellow">
                        <div class="card-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <div class="card-count"><?php echo number_format($data['jumlah_galeri']); ?></div>
                        <div class="card-label">Galeri</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card blue">
                        <div class="card-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <div class="card-count"><?php echo number_format($data['jumlah_mitra']); ?></div>
                        <div class="card-label">Mitra</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 my-4">
            <div class="col-12">
                <div class="quick-actions-card">
                    <h5 class="section-title"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    <div class="quick-actions-grid">
                        <a href="tambah_artikel.php" class="action-btn blue">
                            <i class="fas fa-plus-circle"></i>
                            <span>Tambah Artikel</span>
                        </a>
                        <a href="tambah_proyek.php" class="action-btn purple">
                            <i class="fas fa-folder-plus"></i>
                            <span>Tambah Proyek</span>
                        </a>
                        <a href="tambah_galeri.php" class="action-btn green">
                            <i class="fas fa-upload"></i>
                            <span>Upload Galeri</span>
                        </a>
                        <a href="tambah_dosen.php" class="action-btn orange">
                            <i class="fas fa-user-plus"></i>
                            <span>Tambah Dosen</span>
                        </a>
                        <a href="tambah_mhs.php" class="action-btn pink">
                            <i class="fas fa-user-graduate"></i>
                            <span>Tambah Mahasiswa</span>
                        </a>
                        <a href="tambah_fasilitas.php" class="action-btn teal">
                            <i class="fas fa-building"></i>
                            <span>Tambah Fasilitas</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="analytics-card">
                    <h5 class="section-title"><i class="fas fa-trophy me-2"></i>Top 3 Dosen Paling Produktif</h5>
                    <div class="top-list">
                        <?php 
                            $rank = 1;
                            while($dosen = pg_fetch_assoc($resultTopDosen)): 
                            ?>
                            <div class="top-item">
                                <div class="rank-badge rank-<?php echo $rank; ?>">
                                    <?php echo $rank; ?>
                                </div>
                                <div class="top-avatar">
                                    <?php if($dosen['url_foto_dosen']): ?>
                                        <img src="../<?php echo htmlspecialchars($dosen['url_foto_dosen']); ?>" alt="Foto">
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="top-info">
                                    <h6><?php echo htmlspecialchars($dosen['nama_dosen']); ?></h6>
                                    <div class="top-stats">
                                        <span class="stat-badge blue">
                                            <i class="fas fa-book"></i> <?php echo $dosen['total_publikasi']; ?> Publikasi
                                        </span>
                                        <span class="stat-badge purple">
                                            <i class="fas fa-flask"></i> <?php echo $dosen['total_penelitian']; ?> Penelitian
                                        </span>
                                        <span class="stat-badge green">
                                            <i class="fas fa-project-diagram"></i> <?php echo $dosen['total_proyek']; ?> Proyek
                                        </span>
                                    </div>
                                </div>
                                <div class="top-score">
                                    <div class="score-number"><?php echo $dosen['total_score']; ?></div>
                                    <div class="score-label">Total</div>
                                </div>
                            </div>
                            <?php 
                            $rank++;
                            endwhile; 
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="analytics-card">
                    <h5 class="section-title"><i class="fas fa-chart-pie me-2"></i>Distribusi Artikel per Kategori</h5>
                    <div class="chart-container">
                        <canvas id="artikelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


        <div class="row g-4">
            <div class="col-12">
                <div class="analytics-card">
                    <h5 class="section-title"><i class="fas fa-star me-2"></i>Top 5 Mahasiswa Paling Aktif</h5>
                    <div class="row g-3">
                        <?php 
                            $rankMhs = 1;
                            $colors = ['blue', 'purple', 'green', 'orange', 'pink'];
                            $color = $colors[($rankMhs - 1) % count($colors)];

                            while($mhs = pg_fetch_assoc($resultTopMhs)): 
                            ?>
                            <div class="col-lg-4">
                                <div class="student-card">
                                    <div class="student-rank">
                                        <span class="rank-circle rank-<?php echo $rankMhs; ?>">#<?php echo $rankMhs; ?></span>
                                    </div>
                                    <div class="student-avatar-lg">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <h6 class="student-name"><?php echo htmlspecialchars($mhs['nama_mhs']); ?></h6>
                                    <p class="student-meta">
                                        <?php echo htmlspecialchars($mhs['prodi_mhs']); ?><br>
                                        Angkatan <?php echo htmlspecialchars($mhs['angkatan_mhs']); ?>
                                    </p>
                                    <div class="student-projects">
                                        <div class="project-count <?php echo $color; ?>">
                                            <i class="fas fa-project-diagram"></i>
                                            <span><?php echo $mhs['total_proyek']; ?> Proyek</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                            $rankMhs++;
                            endwhile; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const artikelData = <?php echo json_encode($artikelData); ?>;
    </script>
    <script src="js/chart.js"></script>
</body>