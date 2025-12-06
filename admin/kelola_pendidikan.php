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
    $filter_jenjang = isset($_GET['jenjang']) ? $_GET['jenjang'] : '';
    $filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

    $where_conditions = [];
    $params = [];
    $param_count = 1;

    if (!empty($search)) {
        $where_conditions[] = "(d.nama_dosen ILIKE $" . $param_count . " OR rp.universitas ILIKE $" . $param_count . " OR rp.bidang_studi ILIKE $" . $param_count . ")";
        $params[] = "%$search%";
        $param_count++;
    }

    if (!empty($filter_jenjang)) {
        $where_conditions[] = "rp.jenjang = $" . $param_count;
        $params[] = $filter_jenjang;
        $param_count++;
    }

    if (!empty($filter_tahun)) {
        $where_conditions[] = "rp.tahun_lulus = $" . $param_count;
        $params[] = $filter_tahun;
        $param_count++;
    }

    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

    $qTotal = "SELECT COUNT(*) FROM vw_riwayat_pendidikan rp JOIN dosen d ON d.id_dosen = rp.id_dosen $where_clause";
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

    $qViewPendidikanDosen = "
        SELECT 
            rp.*, 
            d.nama_dosen
        FROM vw_riwayat_pendidikan rp
        JOIN dosen d ON d.id_dosen = rp.id_dosen
        $where_clause
        ORDER BY rp.id_pendidikan
        LIMIT $limit OFFSET $offset
    ";

    $rViewPendidikanDosen = pg_query_params($conn, $qViewPendidikanDosen, $params);
    
    if (!$rViewPendidikanDosen) {
        die("Query error: " . pg_last_error($conn));
    }

    $qTahun = "SELECT DISTINCT tahun_lulus FROM vw_riwayat_pendidikan ORDER BY tahun_lulus DESC";
    $rTahun = pg_query($conn, $qTahun);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pendidikan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/stylePaging.css">
    <link rel="stylesheet" href="css/styleTabel.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content-area container-fluid px-3">
        <div class="mb-4">
            <h2 class="mb-2">Kelola Riwayat Pendidikan Dosen</h2>
            <a href="tambah_pendidikan_dosen.php" class="btn btn-success"><i class="fa fa-plus"></i> Tambah Riwayat Pendidikan</a>
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
                                   placeholder="Cari nama dosen, universitas, atau bidang studi..."
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-filter"></i> Jenjang
                        </label>
                        <select name="jenjang" class="form-select filter-select">
                            <option value="">Semua Jenjang</option>
                            <option value="D3" <?= $filter_jenjang == 'D3' ? 'selected' : '' ?>>D3</option>
                            <option value="S1" <?= $filter_jenjang == 'S1' ? 'selected' : '' ?>>S1</option>
                            <option value="S2" <?= $filter_jenjang == 'S2' ? 'selected' : '' ?>>S2</option>
                            <option value="S3" <?= $filter_jenjang == 'S3' ? 'selected' : '' ?>>S3</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calendar"></i> Tahun Lulus
                        </label>
                        <select name="tahun" class="form-select filter-select">
                            <option value="">Semua Tahun</option>
                            <?php while($tahun = pg_fetch_assoc($rTahun)): ?>
                                <option value="<?= $tahun['tahun_lulus'] ?>" 
                                        <?= $filter_tahun == $tahun['tahun_lulus'] ? 'selected' : '' ?>>
                                    <?= $tahun['tahun_lulus'] ?>
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

                <?php if (!empty($search) || !empty($filter_jenjang) || !empty($filter_tahun)): ?>
                <div class="active-filters">
                    <span class="fw-semibold">Filter Aktif:</span>
                    <?php if (!empty($search)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-search"></i>
                            "<?= htmlspecialchars($search) ?>"
                            <span class="remove-filter" onclick="removeFilter('search')">×</span>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($filter_jenjang)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-graduation-cap"></i>
                            <?= htmlspecialchars($filter_jenjang) ?>
                            <span class="remove-filter" onclick="removeFilter('jenjang')">×</span>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($filter_tahun)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-calendar"></i>
                            <?= htmlspecialchars($filter_tahun) ?>
                            <span class="remove-filter" onclick="removeFilter('tahun')">×</span>
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
                        <col style="width:40px;">
                        <col style="width:250px;">
                        <col style="width:280px;">
                        <col style="width:120px;">
                        <col style="width:200px;">
                        <col style="width:120px;">
                        <col style="width:100px;">
                        <col style="width:250px;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama Dosen</th>
                            <th class="text-center">Universitas</th>
                            <th class="text-center">Jenjang</th>
                            <th class="text-center">Bidang Studi</th>
                            <th class="text-center">Gelar</th>
                            <th class="text-center">Tahun Lulus</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php 
                        if (pg_num_rows($rViewPendidikanDosen) > 0) :
                            $no = $offset + 1; 
                            while($dosen = pg_fetch_assoc($rViewPendidikanDosen)) : 
                                $initial = strtoupper(substr($dosen['nama_dosen'], 0, 1));
                                $jenjang = htmlspecialchars($dosen['jenjang']);
                                $jenjang_class = 'jenjang-' . strtolower($jenjang);
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td>
                                <div class="dosen-info">
                                    <div>
                                        <div class="fw-semibold"><?= htmlspecialchars($dosen['nama_dosen']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center"><?= htmlspecialchars($dosen['universitas']); ?></td>
                            <td class="text-center">
                                <span class="jenjang-badge <?= $jenjang_class ?>">
                                    <?= $jenjang ?>
                                </span>
                            </td>
                            <td class="text-center"><?= htmlspecialchars($dosen['bidang_studi']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($dosen['gelar']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($dosen['tahun_lulus']); ?></td>

                            <td>
                                <div class="action-buttons">
                                    <a href="edit_pendidikan_dosen.php?id=<?= $dosen['id_pendidikan']; ?>" 
                                       class="btn btn-action btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>

                                    <a href="hapus_pendidikan_dosen.php?id=<?= $dosen['id_pendidikan']; ?>"
                                       class="btn btn-action btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus Riwayat Pendidikan ini?')">
                                        <i class="fa fa-trash"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php 
                            endwhile; 
                        else: 
                    ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h5>Tidak Ada Data</h5>
                                    <p class="text-muted">
                                        <?php if (!empty($search) || !empty($filter_jenjang) || !empty($filter_tahun)): ?>
                                            Tidak ada data yang sesuai dengan filter yang dipilih.
                                        <?php else: ?>
                                            Tidak ada data riwayat pendidikan dosen yang ditemukan.
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