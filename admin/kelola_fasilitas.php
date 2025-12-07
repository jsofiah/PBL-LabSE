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
$filter_jenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';

$where = [];
$params = [];
$idx = 1;

if (!empty($search)) {
    $where[] = "(f.nama_fasilitas ILIKE $" . $idx . " OR f.isi_fasilitas ILIKE $" . $idx . ")";
    $params[] = "%$search%";
    $idx++;
}

if (!empty($filter_jenis)) {
    $where[] = "f.id_jenisfasilitas = $" . $idx;
    $params[] = $filter_jenis;
    $idx++;
}

$where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$qTotal = "
    SELECT COUNT(*) 
    FROM vw_fasilitas_lengkap f
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

$qFasilitas = "
    SELECT * 
    FROM vw_fasilitas_lengkap f
    $where_clause
    ORDER BY id_fasilitas ASC
    LIMIT $limit OFFSET $offset
";
$rFasilitas = pg_query_params($conn, $qFasilitas, $params);

$qJenis = "SELECT * FROM jenis_fasilitas ORDER BY nama_jenisfasilitas ASC";
$rJenis = pg_query($conn, $qJenis);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Fasilitas</title>
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
        <h2 class="mb-2">Kelola Fasilitas</h2>

        <a href="tambah_fasilitas.php" class="btn btn-success">
            <i class="fa fa-plus"></i> Tambah Fasilitas
        </a>
    </div>

    <div class="search-filter-section">
        <form method="GET" id="filterForm">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label fw-semibold"><i class="fas fa-search"></i> Pencarian</label>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" class="form-control"
                               placeholder="Cari nama atau isi fasilitas..."
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold"><i class="fas fa-filter"></i> Jenis Fasilitas</label>
                    <select name="jenis" class="form-select filter-select">
                        <option value="">Semua Jenis</option>
                        <?php while($j = pg_fetch_assoc($rJenis)): ?>
                            <option value="<?= $j['id_jenisfasilitas'] ?>"
                                <?= $filter_jenis == $j['id_jenisfasilitas'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($j['nama_jenisfasilitas']) ?>
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

            <?php if (!empty($search) || !empty($filter_jenis)): ?>
            <div class="active-filters">
                <span class="fw-semibold">Filter Aktif:</span>

                <?php if (!empty($search)): ?>
                <span class="filter-badge">
                    <i class="fas fa-search"></i> "<?= htmlspecialchars($search) ?>"
                    <span class="remove-filter" onclick="removeFilter('search')">×</span>
                </span>
                <?php endif; ?>

                <?php if (!empty($filter_jenis)): ?>
                <span class="filter-badge">
                    <i class="fas fa-tag"></i> Jenis
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
                    <col style="width:40px">
                    <col style="width:120px">
                    <col style="width:200px">
                    <col style="width:300px">
                    <col style="width:160px">
                    <col style="width:200px">
                </colgroup>

                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Jenis</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center">Isi</th>
                        <th class="text-center">Preview Gambar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (pg_num_rows($rFasilitas) > 0):
                    $no = $offset + 1;
                    while($row = pg_fetch_assoc($rFasilitas)): ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['nama_jenisfasilitas']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['nama_fasilitas']); ?></td>
                        <td><?= nl2br(htmlspecialchars($row['isi_fasilitas'])); ?></td>

                        <td class="text-center">
                            <img src="../<?= htmlspecialchars($row['url_gambar_fasilitas']); ?>"
                                style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
                        </td>

                        <td class="text-center text-nowrap">
                            <a href="edit_fasilitas.php?id=<?= $row['id_fasilitas']; ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            <a href="hapus_fasilitas.php?id=<?= $row['id_fasilitas']; ?>"
                               onclick="return confirm('Yakin ingin menghapus fasilitas ini?')"
                               class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>Tidak Ada Data</h5>
                                <p class="text-muted">
                                <?php if (!empty($search) || !empty($filter_jenis)): ?>
                                    Tidak ada fasilitas yang cocok dengan filter.
                                <?php else: ?>
                                    Tidak ada data fasilitas.
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
function removeFilter(name) {
    const form = document.getElementById('filterForm');
    const input = form.querySelector(`[name="${name}"]`);
    input.value = "";
    form.submit();
}
</script>

</body>
</html>
