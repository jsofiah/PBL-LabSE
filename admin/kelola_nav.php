<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once '../config.php';

$limit = 20;

$search_nav = isset($_GET['search_nav']) ? trim($_GET['search_nav']) : '';
$where_nav = [];
$params_nav = [];
$idx_nav = 1;

if (!empty($search_nav)) {
    $where_nav[] = "(nama_nav ILIKE $" . $idx_nav . " OR url_nav ILIKE $" . $idx_nav . ")";
    $params_nav[] = "%$search_nav%";
    $idx_nav++;
}

$where_clause_nav = !empty($where_nav) ? "WHERE " . implode(" AND ", $where_nav) : "";

$page_nav = isset($_GET['page_nav']) && is_numeric($_GET['page_nav']) ? (int)$_GET['page_nav'] : 1;
if ($page_nav < 1) $page_nav = 1;

$qTotalNav = "SELECT COUNT(*) FROM nav $where_clause_nav";
$rTotalNav = pg_query_params($conn, $qTotalNav, $params_nav);

if (!$rTotalNav) {
    die("Query hitung total data gagal: " . pg_last_error($conn));
}

$total_nav_records = pg_fetch_result($rTotalNav, 0, 0);
$total_nav_pages = ceil($total_nav_records / $limit);

if ($total_nav_records === 0) {
    $offset_nav = 0;
    $page_nav = 1;
} else {
    if ($page_nav > $total_nav_pages) {
        $page_nav = $total_nav_pages;
    }
    $offset_nav = ($page_nav - 1) * $limit;
}

if ($total_nav_records > 0) {
    $qNav = "
        SELECT id_nav, nama_nav, url_nav
        FROM nav
        $where_clause_nav
        ORDER BY id_nav ASC
        LIMIT $limit OFFSET $offset_nav
    ";
    $rNav = pg_query_params($conn, $qNav, $params_nav);

    if (!$rNav) {
        die("Query error untuk nav: " . pg_last_error($conn));
    }
} else {
    $rNav = false;
}

$search_subnav = isset($_GET['search_subnav']) ? trim($_GET['search_subnav']) : '';
$filter_parent = isset($_GET['parent']) ? $_GET['parent'] : '';

$where_subnav = [];
$params_subnav = [];
$idx_sub = 1;

if (!empty($search_subnav)) {
    $where_subnav[] = "(s.nama_subnav ILIKE $" . $idx_sub . " OR s.url_subnav ILIKE $" . $idx_sub . " OR n.nama_nav ILIKE $" . $idx_sub . ")";
    $params_subnav[] = "%$search_subnav%";
    $idx_sub++;
}

if (!empty($filter_parent)) {
    $where_subnav[] = "s.id_nav = $" . $idx_sub;
    $params_subnav[] = $filter_parent;
    $idx_sub++;
}

$where_clause_subnav = !empty($where_subnav) ? "WHERE " . implode(" AND ", $where_subnav) : "";

$page_subnav = isset($_GET['page_subnav']) && is_numeric($_GET['page_subnav']) ? (int)$_GET['page_subnav'] : 1;
if ($page_subnav < 1) $page_subnav = 1;

$qTotalSubnav = "
    SELECT COUNT(*) 
    FROM subnav s
    JOIN nav n ON s.id_nav = n.id_nav
    $where_clause_subnav
";
$rTotalSubnav = pg_query_params($conn, $qTotalSubnav, $params_subnav);

if (!$rTotalSubnav) {
    die("Query hitung total data subnav gagal: " . pg_last_error($conn));
}

$total_subnav_records = pg_fetch_result($rTotalSubnav, 0, 0);
$total_subnav_pages = ceil($total_subnav_records / $limit);

if ($total_subnav_records === 0) {
    $offset_subnav = 0;
    $page_subnav = 1;
} else {
    if ($page_subnav > $total_subnav_pages) {
        $page_subnav = $total_subnav_pages;
    }
    $offset_subnav = ($page_subnav - 1) * $limit;
}

if ($total_subnav_records > 0) {
    $qSubnav = "
        SELECT s.id_subnav, s.nama_subnav, s.url_subnav, n.nama_nav as parent_nav, s.id_nav
        FROM subnav s
        JOIN nav n ON s.id_nav = n.id_nav
        $where_clause_subnav
        ORDER BY s.id_subnav ASC
        LIMIT $limit OFFSET $offset_subnav
    ";
    $rSubnav = pg_query_params($conn, $qSubnav, $params_subnav);

    if (!$rSubnav) {
        die("Query error untuk subnav: " . pg_last_error($conn));
    }
} else {
    $rSubnav = false;
}

$qParentNav = "SELECT id_nav, nama_nav FROM nav ORDER BY nama_nav ASC";
$rParentNav = pg_query($conn, $qParentNav);

if (!$rParentNav) {
    die("Query error untuk parent nav: " . pg_last_error($conn));
}

$parent_nav_labels = [];
$parent_nav_options = [];
while ($row = pg_fetch_assoc($rParentNav)) {
    $parent_nav_labels[$row['id_nav']] = $row['nama_nav'];
    $parent_nav_options[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Nav</title>
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

    <!-- Nav Section -->
    <div class="mb-4">
        <h2 class="mb-2">Kelola Navigasi Utama</h2>
        <a href="tambah_nav.php" class="btn btn-success">
            <i class="fa fa-plus"></i> Tambah Nav
        </a>
    </div>

    <div class="search-filter-section">
        <form method="GET" action="" id="filterNavForm">
            <input type="hidden" name="page_subnav" value="<?= $page_subnav ?>">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-search"></i> Pencarian Nav
                    </label>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input
                            type="text"
                            name="search_nav"
                            class="form-control"
                            placeholder="Cari nama nav atau URL nav..."
                            value="<?= htmlspecialchars($search_nav) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <a href="?page_subnav=<?= $page_subnav ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </div>

            <?php if (!empty($search_nav)): ?>
                <div class="active-filters">
                    <span class="fw-semibold">Filter Aktif:</span>
                    <span class="filter-badge">
                        <i class="fas fa-search"></i>
                        "<?= htmlspecialchars($search_nav) ?>"
                        <span class="remove-filter" onclick="removeNavFilter('search_nav')">×</span>
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
                    <col style="width:200px;">
                    <col style="width:300px;">
                    <col style="width:200px;">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Nama Nav</th>
                        <th class="text-center">URL Nav</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($rNav && pg_num_rows($rNav) > 0):
                    $no = $offset_nav + 1;
                    while ($nav = pg_fetch_assoc($rNav)): ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center"><?= htmlspecialchars($nav['nama_nav']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($nav['url_nav']); ?></td>
                            <td>
                                <div class="action-buttons text-center">
                                    <a href="edit_nav.php?id=<?= $nav['id_nav']; ?>" 
                                       class="btn btn-action btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a href="hapus_nav.php?id=<?= $nav['id_nav']; ?>" 
                                       class="btn btn-action btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus Nav ini?')">
                                        <i class="fa fa-trash"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h5>Tidak Ada Data</h5>
                                    <p class="text-muted">
                                        <?php if (!empty($search_nav)): ?>
                                            Tidak ada data navigasi yang sesuai dengan pencarian.
                                        <?php else: ?>
                                            Tidak ada data navigasi yang ditemukan.
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

    <!-- Pagination for Nav -->
    <?php if ($total_nav_records > 0): ?>
        <?php
        $page = $page_nav;
        $total_pages = $total_nav_pages;
        $offset = $offset_nav;
        $total_records = $total_nav_records;
        $param_name = 'page_nav';

        $other_nav_params = [
            'page_subnav'    => $page_subnav,
            'search_nav'     => $search_nav,
            'search_subnav'  => $search_subnav,
            'parent'         => $filter_parent
        ];
        $other_param = http_build_query($other_nav_params);
        $label = 'Navigasi';

        include 'paging.php';
        ?>
    <?php endif; ?>

    <!-- Subnav Section -->
    <div class="mt-5 mb-4">
        <h2 class="mb-2">Kelola Subnavigasi</h2>
        <a href="tambah_subnav.php" class="btn btn-success">
            <i class="fa fa-plus"></i> Tambah Subnav
        </a>
    </div>

    <div class="search-filter-section">
        <form method="GET" action="" id="filterSubnavForm">
            <input type="hidden" name="page_nav" value="<?= $page_nav ?>">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-search"></i> Pencarian Subnav
                    </label>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input
                            type="text"
                            name="search_subnav"
                            class="form-control"
                            placeholder="Cari nama subnav, URL, atau parent nav..."
                            value="<?= htmlspecialchars($search_subnav) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-sitemap"></i> Parent Nav
                    </label>
                    <select name="parent" class="form-select filter-select">
                        <option value="">Semua Parent</option>
                        <?php foreach ($parent_nav_options as $pn): ?>
                            <option value="<?= $pn['id_nav']; ?>" <?= $filter_parent == $pn['id_nav'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($pn['nama_nav']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <a href="?page_nav=<?= $page_nav ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </div>

            <?php if (!empty($search_subnav) || !empty($filter_parent)): ?>
            <div class="active-filters">
                <span class="fw-semibold">Filter Aktif:</span>
                <?php if (!empty($search_subnav)): ?>
                    <span class="filter-badge">
                        <i class="fas fa-search"></i>
                        "<?= htmlspecialchars($search_subnav) ?>"
                        <span class="remove-filter" onclick="removeSubnavFilter('search_subnav')">×</span>
                    </span>
                <?php endif; ?>
                <?php if (!empty($filter_parent)): ?>
                    <span class="filter-badge">
                        <i class="fas fa-sitemap"></i>
                        <?= isset($parent_nav_labels[$filter_parent]) ? htmlspecialchars($parent_nav_labels[$filter_parent]) : ''; ?>
                        <span class="remove-filter" onclick="removeSubnavFilter('parent')">×</span>
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
                    <col style="width:200px;">
                    <col style="width:250px;">
                    <col style="width:150px;">
                    <col style="width:200px;">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Nama Subnav</th>
                        <th class="text-center">URL Subnav</th>
                        <th class="text-center">Parent Nav</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($rSubnav && pg_num_rows($rSubnav) > 0):
                    $noSub = $offset_subnav + 1;
                    while ($sub = pg_fetch_assoc($rSubnav)): ?>
                        <tr>
                            <td class="text-center"><?= $noSub++; ?></td>
                            <td class="text-center"><?= htmlspecialchars($sub['nama_subnav']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($sub['url_subnav']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($sub['parent_nav']); ?></td>
                            <td>
                                <div class="action-buttons text-center">
                                    <a href="edit_subnav.php?id=<?= $sub['id_subnav']; ?>" 
                                       class="btn btn-action btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a href="hapus_subnav.php?id=<?= $sub['id_subnav']; ?>" 
                                       class="btn btn-action btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus Subnav ini?')">
                                        <i class="fa fa-trash"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>Tidak Ada Data</h5>
                                <p class="text-muted">
                                    <?php if (!empty($search_subnav) || !empty($filter_parent)): ?>
                                        Tidak ada data subnavigasi yang sesuai dengan filter yang dipilih.
                                    <?php else: ?>
                                        Tidak ada data subnavigasi yang ditemukan.
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

    <!-- Pagination for Subnav -->
    <?php if ($total_subnav_records > 0): ?>
        <?php
        $page = $page_subnav;
        $total_pages = $total_subnav_pages;
        $offset = $offset_subnav;
        $total_records = $total_subnav_records;
        $param_name = 'page_subnav';

        $other_sub_params = [
            'page_nav'       => $page_nav,
            'search_nav'     => $search_nav,
            'search_subnav'  => $search_subnav,
            'parent'         => $filter_parent
        ];
        $other_param = http_build_query($other_sub_params);
        $label = 'Subnavigasi';

        include 'paging.php';
        ?>
    <?php endif; ?>

</div>

<script src="js/sidebar.js"></script>
<script>
function removeNavFilter(name) {
    const form = document.getElementById('filterNavForm');
    const input = form.querySelector(`[name="${name}"]`);
    if (input) {
        input.value = '';
        form.submit();
    }
}
function removeSubnavFilter(name) {
    const form = document.getElementById('filterSubnavForm');
    const input = form.querySelector(`[name="${name}"]`);
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