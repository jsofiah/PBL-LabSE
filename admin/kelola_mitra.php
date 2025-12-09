<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterJenis = isset($_GET['jenis']) ? trim($_GET['jenis']) : '';

$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$where_conditions = [];
$params = [];
$param_count = 1;

if ($search !== '') {
    $where_conditions[] = "(m.nama_mitra ILIKE $" . $param_count . " OR m.isi_mitra ILIKE $" . $param_count . ")";
    $params[] = "%$search%";
    $param_count++;
}

if ($filterJenis !== '') {
    $where_conditions[] = "m.id_jenismitra = $" . $param_count;
    $params[] = $filterJenis;
    $param_count++;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$qTotal = "
    SELECT COUNT(*)
    FROM mitra m
    JOIN jenis_mitra j ON j.id_jenismitra = m.id_jenismitra
    $where_clause
";
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
    $qViewMitra = "
        SELECT 
            m.id_mitra,
            m.nama_mitra,
            m.url_gambar_mitra,
            m.isi_mitra,
            j.nama_jenismitra,
            j.id_jenismitra
        FROM mitra m
        JOIN jenis_mitra j ON j.id_jenismitra = m.id_jenismitra
        $where_clause
        ORDER BY m.id_mitra ASC
        LIMIT $limit OFFSET $offset
    ";
    $rViewMitra = pg_query_params($conn, $qViewMitra, $params);

    if (!$rViewMitra) {
        die("Query error untuk data mitra: " . pg_last_error($conn));
    }
} else {
    $rViewMitra = false;
}

$qJenis = "SELECT id_jenismitra, nama_jenismitra FROM jenis_mitra ORDER BY nama_jenismitra ASC";
$rJenis = pg_query($conn, $qJenis);

if (!$rJenis) {
    die("Query error untuk jenis mitra: " . pg_last_error($conn));
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
    <title>Kelola Mitra</title>

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
        <h2 class="mb-2">Kelola Mitra</h2>
        <a href="tambah_mitra.php" class="btn btn-success">
            <i class="fa fa-plus"></i> Tambah Mitra
        </a>
    </div>

    <div class="search-filter-section mb-3">
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
                               placeholder="Cari nama atau deskripsi..." 
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-filter"></i> Jenis Mitra
                    </label>
                    <select name="jenis" class="form-select filter-select">
                        <option value="">Semua Jenis</option>
                        <?php foreach ($jenis_options as $jenis): ?>
                            <option value="<?= $jenis['id_jenismitra'] ?>" 
                                    <?= $filterJenis == $jenis['id_jenismitra'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($jenis['nama_jenismitra']) ?>
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

            <?php if (!empty($search) || !empty($filterJenis)): ?>
            <div class="active-filters">
                <span class="fw-semibold">Filter Aktif:</span>
                <?php if (!empty($search)): ?>
                    <span class="filter-badge">
                        <i class="fas fa-search"></i> "<?= htmlspecialchars($search) ?>"
                        <span class="remove-filter" onclick="removeFilter('search')">×</span>
                    </span>
                <?php endif; ?>
                <?php if (!empty($filterJenis)): 
                    $jenis_name = '';
                    foreach ($jenis_options as $jenis) {
                        if ($jenis['id_jenismitra'] == $filterJenis) {
                            $jenis_name = $jenis['nama_jenismitra'];
                            break;
                        }
                    }
                ?>
                    <span class="filter-badge">
                        <i class="fas fa-filter"></i> <?= htmlspecialchars($jenis_name) ?>
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
                    <col style="width:120px;">
                    <col style="width:180px;">
                    <col style="width:300px;">
                    <col style="width:150px;">
                    <col style="width:200px;">
                </colgroup>

                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Gambar</th>
                        <th class="text-center">Nama Mitra</th>
                        <th class="text-center">Isi Mitra</th>
                        <th class="text-center">Jenis Mitra</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($rViewMitra && pg_num_rows($rViewMitra) > 0): ?>
                    <?php $no = $offset + 1; ?>
                    <?php while ($m = pg_fetch_assoc($rViewMitra)): ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center">
                                <?php if (!empty($m['url_gambar_mitra'])): ?>
                                    <div class="avatar-container">
                                        <img src="../<?= htmlspecialchars($m['url_gambar_mitra']); ?>"
                                             alt="<?= htmlspecialchars($m['nama_mitra']) ?>"
                                             class="mitra-avatar">
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= htmlspecialchars($m['nama_mitra']); ?></td>
                            <td class="text-center"><?= htmlspecialchars(substr($m['isi_mitra'], 0, 100)); ?>...</td>
                            <td class="text-center"><?= htmlspecialchars($m['nama_jenismitra']); ?></td>
                            <td>
                                <div class="action-buttons text-center">
                                    <a href="edit_mitra.php?id=<?= $m['id_mitra']; ?>" 
                                       class="btn btn-action btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a href="hapus_mitra.php?id=<?= $m['id_mitra']; ?>"
                                       class="btn btn-action btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus mitra ini?')">
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
                                    <?php if (!empty($search) || !empty($filterJenis)): ?>
                                        Tidak ada mitra yang sesuai dengan filter yang dipilih.
                                    <?php else: ?>
                                        Tidak ada data mitra yang ditemukan.
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
        $other_param = http_build_query(['search' => $search, 'jenis' => $filterJenis]);
        $label = 'Mitra';
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
<style>
    .mitra-avatar {
        width: 100%;
        height: 100%;
    }
</style>
</body>
</html>