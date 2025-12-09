<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    $limit = 20; 
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $filter_prodi = isset($_GET['prodi']) ? $_GET['prodi'] : '';
    $filter_angkatan = isset($_GET['angkatan']) ? $_GET['angkatan'] : '';

    $where_conditions = [];
    $params = [];
    $param_count = 1;

    if (!empty($search)) {
        $where_conditions[] = "(nama_pendaftar ILIKE $" . $param_count . " OR nim_pendaftar ILIKE $" . $param_count . " OR prodi_pendaftar ILIKE $" . $param_count . ")";
        $params[] = "%$search%";
        $param_count++;
    }

    if (!empty($filter_prodi)) {
        $where_conditions[] = "prodi_pendaftar = $" . $param_count;
        $params[] = $filter_prodi;
        $param_count++;
    }

    if (!empty($filter_angkatan)) {
        $where_conditions[] = "angkatan_pendaftar = $" . $param_count;
        $params[] = $filter_angkatan;
        $param_count++;
    }

    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

    $qTotal = "SELECT COUNT(*) FROM vw_pendaftaran_segeeks $where_clause"; 
    $rTotal = pg_query_params($conn, $qTotal, $params);

    if (!$rTotal) {
        die("Query hitung total data gagal: " . pg_last_error($conn));
    }

    $total_records = pg_fetch_result($rTotal, 0, 0);
    $total_pages = ceil($total_records / $limit);

    if ($total_records === 0) {
        $page = 0;
        $offset = 0;
    } else {
        if ($page > $total_pages) {
            $page = $total_pages;
        }
        $offset = ($page - 1) * $limit;
    }

    $qViewPendaftar = "
        SELECT * FROM vw_pendaftaran_segeeks 
        $where_clause
        ORDER BY id_pendaftar DESC
        LIMIT $limit OFFSET $offset
    ";
    
    $rViewPendaftar = pg_query_params($conn, $qViewPendaftar, $params);
    
    if (!$rViewPendaftar) {
        die("Query error: " . pg_last_error($conn));
    }

    $qProdi = "SELECT DISTINCT prodi_pendaftar FROM vw_pendaftaran_segeeks WHERE prodi_pendaftar IS NOT NULL ORDER BY prodi_pendaftar ASC";
    $rProdi = pg_query($conn, $qProdi);

    $qAngkatan = "SELECT DISTINCT angkatan_pendaftar FROM vw_pendaftaran_segeeks WHERE angkatan_pendaftar IS NOT NULL ORDER BY angkatan_pendaftar DESC";
    $rAngkatan = pg_query($conn, $qAngkatan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pendaftaran</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
    <link rel="stylesheet" href="css/stylePaging.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content-area container-fluid px-3">
        <div class="mb-4">
            <h2 class="mb-2">Kelola Pendaftaran SE Geeks</h2>
            <a href="../form_daftar.php" class="btn btn-success"><i class="fa fa-plus"></i> Tambah Pendaftar</a>
        </div>

        <div class="search-filter-section">
            <form method="GET" action="" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-search"></i> Pencarian
                        </label>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Cari nama, NIM, atau program studi..."
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-graduation-cap"></i> Program Studi
                        </label>
                        <select name="prodi" class="form-select filter-select">
                            <option value="">Semua Prodi</option>
                            <?php while($prodi = pg_fetch_assoc($rProdi)): ?>
                                <option value="<?= htmlspecialchars($prodi['prodi_pendaftar']) ?>" 
                                        <?= $filter_prodi == $prodi['prodi_pendaftar'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($prodi['prodi_pendaftar']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calendar"></i> Angkatan
                        </label>
                        <select name="angkatan" class="form-select filter-select">
                            <option value="">Semua</option>
                            <?php while($angkatan = pg_fetch_assoc($rAngkatan)): ?>
                                <option value="<?= htmlspecialchars($angkatan['angkatan_pendaftar']) ?>" 
                                        <?= $filter_angkatan == $angkatan['angkatan_pendaftar'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($angkatan['angkatan_pendaftar']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Cari
                            </button>
                            <a href="?" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>

                <?php if (!empty($search) || !empty($filter_prodi) || !empty($filter_angkatan)): ?>
                <div class="active-filters">
                    <span class="fw-semibold">Filter Aktif:</span>
                    <?php if (!empty($search)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-search"></i>
                            "<?= htmlspecialchars($search) ?>"
                            <span class="remove-filter" onclick="removeFilter('search')">×</span>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($filter_prodi)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-graduation-cap"></i>
                            <?= htmlspecialchars($filter_prodi) ?>
                            <span class="remove-filter" onclick="removeFilter('prodi')">×</span>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($filter_angkatan)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-calendar"></i>
                            <?= htmlspecialchars($filter_angkatan) ?>
                            <span class="remove-filter" onclick="removeFilter('angkatan')">×</span>
                        </span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="table-container">
            <div class="table-responsive">
                <table class="table modern-table">
                    <colgroup>
                        <col style="width:50px;">
                        <col style="width:180px;">
                        <col style="width:140px;">
                        <col style="width:200px;">
                        <col style="width:120px;">
                        <col style="width:150px;">
                        <col style="width:200px;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama Pendaftar</th>
                            <th class="text-center">NIM</th>
                            <th class="text-center">Program Studi</th>
                            <th class="text-center">Angkatan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php 
                    if (pg_num_rows($rViewPendaftar) > 0) :
                        $no = $offset + 1;
                        while($pendaftar = pg_fetch_assoc($rViewPendaftar)) : 
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center"><?= htmlspecialchars($pendaftar['nama_pendaftar']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($pendaftar['nim_pendaftar']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($pendaftar['prodi_pendaftar']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($pendaftar['angkatan_pendaftar']); ?></td>

                            <td class="text-center">
                                <?php 
                                    $status = $pendaftar['status_pendaftaran'];
                                    $display_status = 'Menunggu Konfirmasi';
                                    $class = 'text-warning';
                                    
                                    if ($status === 'Diterima') {
                                        $display_status = 'Diterima';
                                        $class = 'text-success fw-bold';
                                    } elseif ($status === 'Ditolak') {
                                        $display_status = 'Ditolak';
                                        $class = 'text-danger fw-bold';
                                    }
                                ?>
                                <span class="<?= $class; ?>"><?= $display_status; ?></span>
                            </td>

                            <td>
                                <div class="action-buttons text-center">
                                    <?php if (!$status): ?>
                                        <a href="terima_pendaftar.php?id=<?= $pendaftar['id_pendaftar'] ?>" 
                                           class="btn btn-action btn-success btn-sm">
                                            <i class="fa fa-check"></i> Terima
                                        </a>
                                        <a href="tolak_pendaftar.php?id=<?= $pendaftar['id_pendaftar'] ?>" 
                                           class="btn btn-action btn-danger btn-sm"
                                           onclick="return confirm('Tolak pendaftar ini?')">
                                            <i class="fa fa-times"></i> Tolak
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Sudah diproses</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h5>Tidak Ada Data</h5>
                                    <p class="text-muted">
                                        <?php if (!empty($search) || !empty($filter_prodi) || !empty($filter_angkatan)): ?>
                                            Tidak ada data yang sesuai dengan filter yang dipilih.
                                        <?php else: ?>
                                            Tidak ada data pendaftar yang ditemukan.
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>

        <?php include 'paging.php'; ?>
    </div>

    <script src="js/sidebar.js"></script>
    <script>
        function removeFilter(filterName) {
            const form = document.getElementById('filterForm');
            const input = form.querySelector(`[name="${filterName}"]`);
            if (input) {
                if (input.tagName === 'SELECT') {
                    input.value = '';
                } else {
                    input.value = '';
                }
                form.submit();
            }
        }
    </script>
</body>
</html>