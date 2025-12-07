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
$idx = 1;

if (!empty($search)) {
    $where_conditions[] = "(g.deskripsi_galeri ILIKE $" . $idx . ")";
    $params[] = "%$search%";
    $idx++;
}

$where_clause = !empty($where_conditions)
    ? "WHERE " . implode(" AND ", $where_conditions)
    : "";

$qTotal = "
    SELECT COUNT(*)
    FROM vw_galeri g
    $where_clause
";
$rTotal = pg_query_params($conn, $qTotal, $params);
$total_records = pg_fetch_result($rTotal, 0, 0);

$total_pages = ceil($total_records / $limit);

if ($total_records == 0) {
    $page = 0;
    $offset = 0;
} else {
    if ($page > $total_pages) $page = $total_pages;
    $offset = ($page - 1) * $limit;
}

$qGaleri = "
    SELECT *
    FROM vw_galeri g
    $where_clause
    ORDER BY id_galeri DESC
    LIMIT $limit OFFSET $offset
";
$rGaleri = pg_query_params($conn, $qGaleri, $params);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/stylePaging.css">
    <link rel="stylesheet" href="css/styleTabel.css">
    <link rel="stylesheet" href="css/stylePaging.css">
</head>

<body>
<?php include 'sidebar.php'; ?>

<div class="content-area container-fluid px-3">

    <div class="mb-4">
        <h2 class="mb-2">Kelola Galeri</h2>
        <a href="tambah_galeri.php" class="btn btn-success">
            <i class="fa fa-plus"></i> Tambah Gambar
        </a>
    </div>

    <div class="search-filter-section">
        <form method="GET" id="filterForm">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold"><i class="fas fa-search"></i> Pencarian</label>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Cari deskripsi gambar..."
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
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

            <?php if (!empty($search)): ?>
            <div class="active-filters mt-2">
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
                    <col style="width:50px">
                    <col style="width:200px">
                    <col style="width:200px">
                    <col style="width:100px">
                </colgroup>

                <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Deskripsi</th>
                    <th class="text-center">Gambar</th>
                    <th class="text-center">Aksi</th>
                </tr>
                </thead>

                <tbody>
                <?php if (pg_num_rows($rGaleri) > 0): ?>
                    <?php $no = $offset + 1; ?>
                    <?php while($g = pg_fetch_assoc($rGaleri)): ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= htmlspecialchars($g['deskripsi_galeri']); ?></td>

                        <td class="text-center">
                            <img src="../<?= htmlspecialchars($g['url_gambar_galeri']); ?>"
                                 style="width:120px; height:80px; object-fit:cover; border-radius:6px;">
                        </td>

                        <td class="text-center text-nowrap">
                            <a href="edit_galeri.php?id=<?= $g['id_galeri'] ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="hapus_galeri.php?id=<?= $g['id_galeri'] ?>"
                               onclick="return confirm('Yakin ingin menghapus gambar ini?')"
                               class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>Tidak Ada Data</h5>
                                <p class="text-muted">Tidak ada data galeri yang ditemukan.</p>
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
function removeFilter(name) {
    const form = document.getElementById('filterForm');
    const input = form.querySelector(`[name="${name}"]`);
    if (input) {
        input.value = '';
        form.submit();
    }
}
</script>

</body>
</html>
