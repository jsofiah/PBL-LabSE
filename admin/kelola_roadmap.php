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
    $where_conditions[] = "(judul_roadmap ILIKE $".$param_count." OR deskripsi_roadmap ILIKE $".$param_count.")";
    $params[] = "%$search%";
    $param_count++;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$qTotal = "SELECT COUNT(*) FROM vw_roadmap $where_clause";
$rTotal = pg_query_params($conn, $qTotal, $params);
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

$qView = "SELECT * FROM vw_roadmap $where_clause ORDER BY id_roadmap ASC LIMIT $limit OFFSET $offset";
$rView = pg_query_params($conn, $qView, $params);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Roadmap</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/styleSidebar.css">
<link rel="stylesheet" href="css/stylePaging.css">
<link rel="stylesheet" href="css/styleTabel.css">
</head>

<body>
<?php include 'sidebar.php'; ?>

<div class="content-area container-fluid px-3">
    <div class="mb-4">
        <h2 class="mb-2">Kelola Roadmap</h2>
        <a href="tambah_roadmap.php" class="btn btn-success"><i class="fa fa-plus"></i> Tambah Roadmap</a>
    </div>

    <!-- SEARCH BAR -->
    <div class="search-filter-section mb-3">
        <form method="GET" action="" id="filterForm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><i class="fas fa-search"></i> Pencarian</label>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" class="form-control" placeholder="Cari judul atau deskripsi..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2"><i class="fas fa-search"></i> Cari</button>
                    <a href="?" class="btn btn-outline-secondary"><i class="fas fa-redo"></i> Reset</a>
                </div>
            </div>

            <?php if (!empty($search)): ?>
            <div class="active-filters">
                <span class="fw-semibold">Filter Aktif:</span>
                <span class="filter-badge">
                    <i class="fas fa-search"></i> "<?= htmlspecialchars($search) ?>"
                    <span class="remove-filter" onclick="removeFilter('search')">×</span>
                </span>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- TABLE -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table modern-table">
                <colgroup>
                    <col style="width:60px;">
                    <col style="width:200px;">
                    <col style="width:450px;">
                    <col style="width:160px;">
                    <col style="width:200px;">
                </colgroup>

                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Judul</th>
                        <th class="text-center">Deskripsi</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (pg_num_rows($rView) > 0): $no = $offset + 1; ?>
                    <?php while ($row = pg_fetch_assoc($rView)) : ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['judul_roadmap']); ?></td>
                            <td><?= nl2br(htmlspecialchars($row['deskripsi_roadmap'])); ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['tanggal_roadmap']); ?></td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="edit_roadmap.php?id=<?= $row['id_roadmap']; ?>" class="btn btn-action btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a href="hapus_roadmap.php?id=<?= $row['id_roadmap']; ?>" onclick="return confirm('Yakin ingin menghapus roadmap ini?')" class="btn btn-action btn-danger btn-sm">
                                        <i class="fa fa-trash"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>Tidak Ada Data</h5>
                                <p class="text-muted">Tidak ada data roadmap yang ditemukan.</p>
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
        input.value = '';
        form.submit();
    }
}
</script>

</body>
</html>
