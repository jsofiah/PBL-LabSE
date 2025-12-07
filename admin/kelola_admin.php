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
        $where_conditions[] = "username ILIKE $" . $param_count;
        $params[] = "%$search%";
        $param_count++;
    }

    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

    $qTotal = "SELECT COUNT(*) FROM vw_admin_user $where_clause";
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
        $qViewAdmin = "
            SELECT * FROM vw_admin_user 
            $where_clause
            ORDER BY id ASC 
            LIMIT $limit OFFSET $offset
        ";
        $rViewAdmin = pg_query_params($conn, $qViewAdmin, $params);

        if (!$rViewAdmin) {
            die("Query error: " . pg_last_error($conn));
        }
    } else {
        $rViewAdmin = false;
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Admin User</title>
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
            <h2 class="mb-2">Kelola Admin User</h2>
            <a href="tambah_admin.php" class="btn btn-success"><i class="fa fa-plus"></i> Tambah Admin</a>
        </div>

        <div class="search-filter-section">
            <form method="GET" action="" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-search"></i> Pencarian
                        </label>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Cari username..."
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
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
                        <col style="width:120px;">
                        <col style="width:200px;">
                        <col style="width:200px;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Foto</th>
                            <th class="text-center">Username</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php 
                        if ($rViewAdmin && pg_num_rows($rViewAdmin) > 0) :
                            $no = $offset + 1; 
                            while($row = pg_fetch_assoc($rViewAdmin)) : 
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center">
                                <div class="avatar-container">
                                    <?php if (!empty($row['foto_admin'])): ?>
                                        <img src="<?= htmlspecialchars($row['foto_admin']); ?>" 
                                             alt="Foto Admin" 
                                             class="admin-avatar">
                                    <?php else: ?>
                                        <img src="https://placehold.co/80x80/cccccc/000000?text=No+Photo" 
                                             alt="No Photo" 
                                             class="admin-avatar">
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center"><?= htmlspecialchars($row['username']); ?></td>

                            <td>
                                <div class="action-buttons text-center">
                                    <a href="edit_admin.php?id=<?= $row['id']; ?>" 
                                       class="btn btn-action btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>

                                    <?php if ($row['username'] !== $_SESSION['username']): ?>
                                        <a href="hapus_admin.php?id=<?= $row['id']; ?>"
                                           class="btn btn-action btn-danger btn-sm"
                                           onclick="return confirm('Yakin ingin menghapus Admin ini?')">
                                            <i class="fa fa-trash"></i> Hapus
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-action btn-secondary btn-sm" disabled>
                                            <i class="fa fa-ban"></i> Hapus
                                        </button>
                                    <?php endif; ?>
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
                                            Tidak ada data admin user yang ditemukan.
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
    <style>
        .admin-avatar {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
        }
        
        .avatar-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
    </style>
</body>
</html>