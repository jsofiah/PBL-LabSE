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
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

$where_conditions = [];
$params = [];
$param_count = 1;

if (!empty($search)) {
    $where_conditions[] = "(judul_penelitian ILIKE $" . $param_count . " OR nama_dosen ILIKE $" . $param_count . ")";
    $params[] = "%$search%";
    $param_count++;
}

if (!empty($filter_tahun)) {
    $where_conditions[] = "tahun_penelitian = $" . $param_count;
    $params[] = $filter_tahun;
    $param_count++;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$qTotal = "SELECT COUNT(*) FROM vw_penelitian_dosen $where_clause";
$rTotal = pg_query_params($conn, $qTotal, $params);

if (!$rTotal) {
    die("Query hitung total data gagal: " . pg_last_error($conn));
}

$total_records = pg_fetch_result($rTotal, 0, 0);
$total_pages = ceil($total_records / $limit);

if ($total_records === 0) {
    $offset = 0;
    $page = 1;
} else {
    if ($page > $total_pages) {
        $page = $total_pages;
    }
    $offset = ($page - 1) * $limit;
}

if ($total_records > 0) {
    $qViewPenelitian = "
        SELECT *
        FROM vw_penelitian_dosen
        $where_clause
        ORDER BY id_penelitian DESC
        LIMIT $limit OFFSET $offset
    ";
    $rViewPenelitian = pg_query_params($conn, $qViewPenelitian, $params);

    if (!$rViewPenelitian) {
        die("Query error untuk data penelitian: " . pg_last_error($conn));
    }
} else {
    $rViewPenelitian = false;
}

$qTahun = "SELECT DISTINCT tahun_penelitian FROM vw_penelitian_dosen ORDER BY tahun_penelitian DESC";
$rTahun = pg_query($conn, $qTahun);

if (!$rTahun) {
    die("Query error untuk tahun penelitian: " . pg_last_error($conn));
}

$tahun_options = [];
while ($row = pg_fetch_assoc($rTahun)) {
    $tahun_options[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Penelitian</title>

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
        <h2 class="mb-2">Kelola Penelitian Dosen</h2>
        <a href="tambah_penelitian.php" class="btn btn-success">
            <i class="fa fa-plus"></i> Tambah Penelitian
        </a>
    </div>

    <div class="search-filter-section">
        <form method="GET" action="" id="filterForm">
            <div class="row g-3">

                <div class="col-md-5">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-search"></i> Pencarian
                    </label>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Cari judul penelitian atau nama dosen..."
                            value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-calendar"></i> Tahun
                    </label>
                    <select name="tahun" class="form-select filter-select">
                        <option value="">Semua Tahun</option>
                        <?php foreach ($tahun_options as $t): ?>
                            <option
                                value="<?= $t['tahun_penelitian'] ?>"
                                <?= $filter_tahun == $t['tahun_penelitian'] ? 'selected' : '' ?>>
                                <?= $t['tahun_penelitian'] ?>
                            </option>
                        <?php endforeach; ?>
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

            <?php if (!empty($search) || !empty($filter_tahun)): ?>
                <div class="active-filters">
                    <span class="fw-semibold">Filter Aktif:</span>

                    <?php if (!empty($search)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-search"></i>
                            "<?= htmlspecialchars($search) ?>"
                            <span class="remove-filter" onclick="removeFilter('search')">×</span>
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
                    <col style="width:50px;">
                    <col style="width:300px;">
                    <col style="width:100px;">
                    <col style="width:230px;">
                    <col style="width:200px;">
                </colgroup>

                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Judul Penelitian</th>
                        <th class="text-center">Tahun</th>
                        <th class="text-center">Nama Dosen</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($rViewPenelitian && pg_num_rows($rViewPenelitian) > 0):
                    $no = $offset + 1;
                    while($p = pg_fetch_assoc($rViewPenelitian)): ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= htmlspecialchars($p['judul_penelitian']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($p['tahun_penelitian']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($p['nama_dosen']); ?></td>
                        <td>
                            <div class="action-buttons text-center">
                                <a href="edit_penelitian.php?id=<?= $p['id_penelitian']; ?>"
                                   class="btn btn-action btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="hapus_penelitian.php?id=<?= $p['id_penelitian']; ?>"
                                   class="btn btn-action btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus penelitian ini?')">
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
                                    <?php if (!empty($search) || !empty($filter_tahun)): ?>
                                        Tidak ada data yang sesuai dengan filter yang dipilih.
                                    <?php else: ?>
                                        Tidak ada data penelitian yang ditemukan.
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

    <?php if ($total_records > 0): ?>
        <?php 
        $page = $page;
        $total_pages = $total_pages;
        $offset = $offset;
        $total_records = $total_records;
        $param_name = 'page';
        $other_param = http_build_query(['search' => $search, 'tahun' => $filter_tahun]);
        $label = 'Penelitian';
        ?>
        <?php include 'paging.php'; ?>
    <?php endif; ?>

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