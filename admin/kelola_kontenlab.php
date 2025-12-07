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
$filter_judul = isset($_GET['judul']) ? trim($_GET['judul']) : '';

$where_conditions = [];
$params = [];
$param_count = 1;

if (!empty($search)) {
    $where_conditions[] = "(judul_konten ILIKE $" . $param_count . " OR isi_konten ILIKE $" . $param_count . ")";
    $params[] = "%$search%";
    $param_count++;
}

if (!empty($filter_judul)) {
    $where_conditions[] = "judul_konten = $" . $param_count;
    $params[] = $filter_judul;
    $param_count++;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$qTotal = "SELECT COUNT(*) FROM vw_konten_lab $where_clause";
$rTotal = pg_query_params($conn, $qTotal, $params);

if (!$rTotal) {
    die("Query hitung total gagal: " . pg_last_error($conn));
}

$total_records = pg_fetch_result($rTotal, 0, 0);
$total_pages = ceil($total_records / $limit);

if ($total_records == 0) {
    $page = 0;
    $offset = 0;
} else {
    if ($page > $total_pages) $page = $total_pages;
    $offset = ($page - 1) * $limit;
}

$qView = "
    SELECT * FROM vw_konten_lab
    $where_clause
    ORDER BY id_konten ASC
    LIMIT $limit OFFSET $offset
";
$rView = pg_query_params($conn, $qView, $params);

$qJudul = "SELECT DISTINCT judul_konten FROM vw_konten_lab ORDER BY judul_konten ASC";
$rJudul = pg_query($conn, $qJudul);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Konten Laboratorium</title>

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
        <h2 class="mb-2">Kelola Konten Laboratorium</h2>
        <a href="tambah_kontenLab.php" class="btn btn-success">
            <i class="fa fa-plus"></i> Tambah Konten
        </a>
    </div>

    <div class="search-filter-section">
        <form method="GET" id="filterForm">
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
                               placeholder="Cari judul atau isi konten..."
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-file-alt"></i> Judul Konten
                    </label>
                    <select name="judul" class="form-select filter-select">
                        <option value="">Semua Judul</option>
                        <?php while($j = pg_fetch_assoc($rJudul)): ?>
                            <option value="<?= $j['judul_konten'] ?>"
                                <?= $filter_judul == $j['judul_konten'] ? 'selected' : '' ?>>
                                <?= $j['judul_konten'] ?>
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

            <?php if (!empty($search) || !empty($filter_judul)): ?>
                <div class="active-filters mt-2">
                    <span class="fw-semibold">Filter Aktif:</span>

                    <?php if (!empty($search)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-search"></i>
                            "<?= htmlspecialchars($search) ?>"
                            <span class="remove-filter" onclick="removeFilter('search')">×</span>
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($filter_judul)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-file-alt"></i>
                            <?= htmlspecialchars($filter_judul) ?>
                            <span class="remove-filter" onclick="removeFilter('judul')">×</span>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container mt-3">
        <div class="table-responsive">
            <table class="table modern-table">

                <colgroup>
                    <col style="width:50px;">
                    <col style="width:150px;">
                    <col style="width:400px;">
                    <col style="width:150px;">
                </colgroup>

                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Judul Konten</th>
                        <th class="text-center">Isi Konten</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (pg_num_rows($rView) > 0): ?>
                    <?php $no = $offset + 1; ?>
                    <?php while($row = pg_fetch_assoc($rView)): ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['judul_konten']); ?></td>
                            <td><?= nl2br(htmlspecialchars(substr($row['isi_konten'], 0, 80))); ?>...</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_kontenLab.php?id=<?= $row['id_konten']; ?>" 
                                       class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a href="hapus_kontenLab.php?id=<?= $row['id_konten']; ?>"
                                       onclick="return confirm('Yakin ingin menghapus data ini?')"
                                       class="btn btn-danger btn-sm">
                                        <i class="fa fa-trash"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h5>Tidak Ada Data</h5>
                                    <p class="text-muted">
                                        Tidak ada data konten laboratorium yang ditemukan.
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
    input.value = '';
    form.submit();
}
</script>

</body>
</html>
