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
$filter_jenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';

$where_conditions = [];
$params = [];
$param_count = 1;

if (!empty($search)) {
    $where_conditions[] = "(judul_publikasi ILIKE $" . $param_count . " OR nama_dosen ILIKE $" . $param_count . " OR nama_jenispublikasi ILIKE $" . $param_count . ")";
    $params[] = "%$search%";
    $param_count++;
}

if (!empty($filter_tahun)) {
    $where_conditions[] = "tahun_publikasi = $" . $param_count;
    $params[] = $filter_tahun;
    $param_count++;
}

if (!empty($filter_jenis)) {
    $where_conditions[] = "nama_jenispublikasi = $" . $param_count;
    $params[] = $filter_jenis;
    $param_count++;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$qTotal = "SELECT COUNT(*) FROM vw_publikasi_dosen $where_clause";
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
    if ($page > $total_pages) $page = $total_pages;
    $offset = ($page - 1) * $limit;
}

if ($total_records > 0) {
    $qView = "
        SELECT * 
        FROM vw_publikasi_dosen
        $where_clause
        ORDER BY id_publikasi DESC
        LIMIT $limit OFFSET $offset
    ";
    $rView = pg_query_params($conn, $qView, $params);
    
    if (!$rView) {
        die("Query error untuk data publikasi: " . pg_last_error($conn));
    }
} else {
    $rView = false;
}

$qTahun = "SELECT DISTINCT tahun_publikasi FROM vw_publikasi_dosen ORDER BY tahun_publikasi DESC";
$rTahun = pg_query($conn, $qTahun);

if (!$rTahun) {
    die("Query error untuk tahun publikasi: " . pg_last_error($conn));
}

$tahun_options = [];
while ($row = pg_fetch_assoc($rTahun)) {
    $tahun_options[] = $row;
}

$qJenis = "SELECT DISTINCT nama_jenispublikasi FROM vw_publikasi_dosen ORDER BY nama_jenispublikasi ASC";
$rJenis = pg_query($conn, $qJenis);

if (!$rJenis) {
    die("Query error untuk jenis publikasi: " . pg_last_error($conn));
}

$jenis_options = [];
while ($row = pg_fetch_assoc($rJenis)) {
    $jenis_options[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Publikasi</title>

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
        <h2 class="mb-2">Kelola Publikasi Dosen</h2>
        <a href="tambah_publikasi.php" class="btn btn-success">
            <i class="fa fa-plus"></i> Tambah Publikasi
        </a>
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
                               placeholder="Cari judul publikasi, jenis, atau nama dosen..."
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
                        <option value="<?= $t['tahun_publikasi'] ?>" <?= $filter_tahun == $t['tahun_publikasi'] ? 'selected' : '' ?>>
                            <?= $t['tahun_publikasi'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-filter"></i> Jenis Publikasi
                    </label>
                    <select name="jenis" class="form-select filter-select">
                        <option value="">Semua Jenis</option>
                        <?php foreach ($jenis_options as $j): ?>
                        <option value="<?= $j['nama_jenispublikasi'] ?>" <?= $filter_jenis == $j['nama_jenispublikasi'] ? 'selected' : '' ?>>
                            <?= $j['nama_jenispublikasi'] ?>
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
                        <a href="?" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>

            </div>

            <?php if (!empty($search) || !empty($filter_tahun) || !empty($filter_jenis)): ?>
            <div class="active-filters mt-2">
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

                <?php if (!empty($filter_jenis)): ?>
                <span class="filter-badge">
                    <i class="fas fa-filter"></i>
                    <?= htmlspecialchars($filter_jenis) ?>
                    <span class="remove-filter" onclick="removeFilter('jenis')">×</span>
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
                    <col style="width:150px;">
                    <col style="width:150px;">
                    <col style="width:200px;">
                </colgroup>

                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Judul</th>
                        <th class="text-center">Tahun</th>
                        <th class="text-center">Jenis</th>
                        <th class="text-center">Dosen</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($rView && pg_num_rows($rView) > 0): ?>
                    <?php $no = $offset + 1; ?>
                    <?php while($p = pg_fetch_assoc($rView)): ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= htmlspecialchars($p['judul_publikasi']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($p['tahun_publikasi']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($p['nama_jenispublikasi']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($p['nama_dosen']); ?></td>

                        <td>
                            <div class="action-buttons text-center">
                                <a href="edit_publikasi.php?id=<?= $p['id_publikasi']; ?>" 
                                   class="btn btn-action btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="hapus_publikasi.php?id=<?= $p['id_publikasi']; ?>" 
                                   class="btn btn-action btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus publikasi ini?')">
                                    <i class="fa fa-trash"></i> Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>Tidak Ada Data</h5>
                                <p class="text-muted">
                                    <?php if (!empty($search) || !empty($filter_tahun) || !empty($filter_jenis)): ?>
                                        Tidak ada data yang sesuai dengan filter yang dipilih.
                                    <?php else: ?>
                                        Tidak ada data publikasi yang ditemukan.
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
        $other_param = http_build_query(['search' => $search, 'tahun' => $filter_tahun, 'jenis' => $filter_jenis]);
        $label = 'Publikasi';
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