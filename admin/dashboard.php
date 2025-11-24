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
        <h2><i class="fas fa-chart-line me-2"></i>Dashboard LAB SE</h2>
        
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
    </div>
    <script src="js/sidebar.js"></script>
</body>