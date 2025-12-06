<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $limit = 20;

    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $filter_jenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';
    $filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

    $where_conditions = [];
    $params = [];
    $param_count = 1;

    if (!empty($search)) {
        $where_conditions[] = "(a.judul_artikel ILIKE $" . $param_count . 
                            " OR a.penulis_artikel ILIKE $" . $param_count . 
                            " OR ja.nama_jenisartikel ILIKE $" . $param_count . ")";
        $params[] = "%$search%";
        $param_count++;
    }

    if (!empty($filter_jenis)) {
        $where_conditions[] = "a.id_jenisartikel = $" . $param_count;
        $params[] = $filter_jenis;
        $param_count++;
    }

    if (!empty($filter_tahun)) {
        $where_conditions[] = "EXTRACT(YEAR FROM a.tanggal_terbit_artikel) = $" . $param_count;
        $params[] = $filter_tahun;
        $param_count++;
    }

    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

    $qTotal = "
        SELECT COUNT(*) 
        FROM artikel a
        JOIN jenis_artikel ja ON ja.id_jenisartikel = a.id_jenisartikel
        $where_clause
    ";
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
        $qArtikel = "
            SELECT a.*, ja.nama_jenisartikel
            FROM vw_artikel a
            JOIN jenis_artikel ja ON ja.id_jenisartikel = a.id_jenisartikel
            $where_clause
            ORDER BY a.id_artikel DESC
            LIMIT $limit OFFSET $offset
        ";
        $rArtikel = pg_query_params($conn, $qArtikel, $params);

        if (!$rArtikel) {
            die("Query error: " . pg_last_error($conn));
        }
    } else {
        $rArtikel = false;
    }

    $qJenis = "SELECT * FROM jenis_artikel ORDER BY nama_jenisartikel ASC";
    $rJenis = pg_query($conn, $qJenis);

    $qTahun = "
        SELECT DISTINCT EXTRACT(YEAR FROM tanggal_terbit_artikel)::int AS tahun
        FROM artikel
        WHERE tanggal_terbit_artikel IS NOT NULL
        ORDER BY tahun DESC
    ";
    $rTahun = pg_query($conn, $qTahun);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Artikel</title>

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
        <h2 class="mb-2">Kelola Artikel</h2>
        <a href="tambah_artikel.php" class="btn btn-success">
            <i class="fa fa-plus"></i> Tambah Artikel
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
                               placeholder="Cari judul, penulis, atau jenis..."
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-filter"></i> Jenis Artikel
                    </label>
                    <select name="jenis" class="form-select filter-select">
                        <option value="">Semua Jenis</option>
                        <?php 
                        if ($rJenis) {
                            while($j = pg_fetch_assoc($rJenis)): 
                        ?>
                            <option value="<?= htmlspecialchars($j['id_jenisartikel']) ?>"
                                <?= $filter_jenis == $j['id_jenisartikel'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($j['nama_jenisartikel']) ?>
                            </option>
                        <?php 
                            endwhile; 
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-calendar"></i> Tahun
                    </label>
                    <select name="tahun" class="form-select filter-select">
                        <option value="">Semua Tahun</option>
                        <?php 
                        if ($rTahun) {
                            while($t = pg_fetch_assoc($rTahun)): 
                        ?>
                            <option value="<?= htmlspecialchars($t['tahun']) ?>"
                                <?= $filter_tahun == $t['tahun'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['tahun']) ?>
                            </option>
                        <?php 
                            endwhile; 
                        }
                        ?>
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

            <?php if (!empty($search) || !empty($filter_jenis) || !empty($filter_tahun)): ?>
            <div class="active-filters">
                <span class="fw-semibold">Filter Aktif:</span>

                <?php if (!empty($search)): ?>
                <span class="filter-badge">
                    <i class="fas fa-search"></i>
                    "<?= htmlspecialchars($search) ?>"
                    <span class="remove-filter" onclick="removeFilter('search')">×</span>
                </span>
                <?php endif; ?>

                <?php if (!empty($filter_jenis)): ?>
                <span class="filter-badge">
                    <i class="fas fa-tag"></i>
                    Jenis
                    <span class="remove-filter" onclick="removeFilter('jenis')">×</span>
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
                    <col style="width:40px">
                    <col style="width:250px">
                    <col style="width:150px">
                    <col style="width:150px">
                    <col style="width:150px">
                    <col style="width:150px">
                </colgroup>

                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Judul Artikel</th>
                        <th class="text-center">Jenis</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Penulis</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php 
                if ($rArtikel && pg_num_rows($rArtikel) > 0):
                    $no = $offset + 1;
                    while($a = pg_fetch_assoc($rArtikel)): ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><?= htmlspecialchars($a['judul_artikel']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($a['nama_jenisartikel']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($a['tanggal_terbit_artikel']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($a['penulis_artikel']); ?></td>

                        <td class="text-center text-nowrap">
                            <a href="edit_artikel.php?id=<?= $a['id_artikel']; ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            <a href="hapus_artikel.php?id=<?= $a['id_artikel']; ?>"
                               onclick="return confirm('Yakin ingin menghapus artikel ini?')" 
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
                                    <?php if (!empty($search) || !empty($filter_jenis) || !empty($filter_tahun)): ?>
                                        Tidak ada artikel yang sesuai filter.
                                    <?php else: ?>
                                        Tidak ada data artikel yang ditemukan.
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