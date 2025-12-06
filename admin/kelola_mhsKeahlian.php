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

    $where_conditions = [];
    $params = [];
    $param_count = 1;

    if (!empty($search)) {
        $where_conditions[] = "(nama_mhs ILIKE $" . $param_count . " OR nama_keahlian ILIKE $" . $param_count . ")";
        $params[] = "%$search%";
        $param_count++;
    }

    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

    $qTotal = "SELECT COUNT(*) FROM vw_mhs_keahlian $where_clause"; 
    $rTotal = pg_query_params($conn, $qTotal, $params);

    if (!$rTotal) {
        die("Query hitung total data gagal: " . pg_last_error($conn));
    }

    $total_records = (int)pg_fetch_result($rTotal, 0, 0);
    $total_pages = ($total_records > 0) ? ceil($total_records / $limit) : 1;

    if ($page > $total_pages && $total_pages > 0) {
        $page = $total_pages;
    }
    
    $offset = max(0, ($page - 1) * $limit);

    if ($total_records > 0) {
        $qViewKeahlian = "
            SELECT * FROM vw_mhs_keahlian 
            $where_clause
            ORDER BY nama_mhs ASC, nama_keahlian ASC
            LIMIT $limit OFFSET $offset
        ";
        
        $rViewKeahlian = pg_query_params($conn, $qViewKeahlian, $params);
        
        if (!$rViewKeahlian) {
            die("Query error: " . pg_last_error($conn));
        }
    } else {
        $rViewKeahlian = false;
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Keahlian Mahasiswa</title>
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
            <h2 class="mb-2">Kelola Keahlian Mahasiswa</h2>
            <a href="tambah_keahlianMhs.php" class="btn btn-success"><i class="fa fa-plus"></i> Tambah Keahlian Mahasiswa</a>
        </div>
        
        <div class="search-filter-section">
            <form method="GET" action="" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-search"></i> Pencarian
                        </label>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Cari nama mahasiswa atau keahlian..."
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
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

                <?php if (!empty($search)): ?>
                <div class="active-filters">
                    <span class="fw-semibold">Filter Aktif:</span>
                    <span class="filter-badge">
                        <i class="fas fa-search"></i>
                        "<?= htmlspecialchars($search) ?>"
                        <span class="remove-filter" onclick="removeFilter('search')">×</span>
                    </span>
                </div>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table modern-table">
                    <colgroup>
                        <col style="width:50px;">
                        <col style="width:250px;">
                        <col style="width:250px;">
                        <col style="width:200px;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama Mahasiswa</th>
                            <th class="text-center">Nama Keahlian</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
    
                    <tbody>
                    <?php 
                    if ($rViewKeahlian && pg_num_rows($rViewKeahlian) > 0) :
                        $no = $offset + 1;
                        while($keahlian = pg_fetch_assoc($rViewKeahlian)) : 
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center"><?= htmlspecialchars($keahlian['nama_mhs']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($keahlian['nama_keahlian']); ?></td>

                            <td>
                                <div class="action-buttons text-center">
                                    <a href="edit_keahlianMhs.php?id_mhs=<?= $keahlian['id_mhs'] ?>&id_keahlian=<?= $keahlian['id_keahlian'] ?>" 
                                       class="btn btn-action btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
        
                                    <a href="hapus_keahlianMhs.php?id_mhs=<?= $keahlian['id_mhs']; ?>&id_keahlian=<?= $keahlian['id_keahlian']; ?>"
                                       class="btn btn-action btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus Keahlian ini?')">
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
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h5>Tidak Ada Data</h5>
                                    <p class="text-muted">
                                        <?php if (!empty($search)): ?>
                                            Tidak ada data yang sesuai dengan pencarian "<?= htmlspecialchars($search) ?>".
                                        <?php else: ?>
                                            Tidak ada data keahlian mahasiswa yang ditemukan.
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
        
        <?php 
        if ($total_records > 0) {
            include 'paging.php'; 
        }
        ?>
    </div>

    <script src="js/sidebar.js"></script>
    <script>
        function removeFilter(filterName) {
            const form = document.getElementById('filterForm');
            const input = form.querySelector(`[name="${filterName}"]`);
            if (input) {
                input.value = '';
                form.submit();
            }
        }
    </script>
</body>
</html>