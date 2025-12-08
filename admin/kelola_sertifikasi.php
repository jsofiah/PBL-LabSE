<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once '../config.php';

$limit = 20;

$search_sertifikasi = isset($_GET['search_sertifikasi']) ? trim($_GET['search_sertifikasi']) : '';
$filter_tahun_sertifikasi = isset($_GET['tahun_sertifikasi']) ? $_GET['tahun_sertifikasi'] : '';

$where_sert = [];
$params_sert = [];
$idx_sert = 1;

if (!empty($search_sertifikasi)) {
    $where_sert[] = "(nama_sertifikasi ILIKE $" . $idx_sert . " OR penyelenggara ILIKE $" . $idx_sert . ")";
    $params_sert[] = "%$search_sertifikasi%";
    $idx_sert++;
}

if (!empty($filter_tahun_sertifikasi)) {
    $where_sert[] = "tahun_sertifikasi = $" . $idx_sert;
    $params_sert[] = $filter_tahun_sertifikasi;
    $idx_sert++;
}

$where_clause_sert = !empty($where_sert) ? "WHERE " . implode(" AND ", $where_sert) : "";

$page_sertifikasi = isset($_GET['page_sertifikasi']) && is_numeric($_GET['page_sertifikasi']) ? (int)$_GET['page_sertifikasi'] : 1;
if ($page_sertifikasi < 1) $page_sertifikasi = 1;

$qTotalSertifikasi = "SELECT COUNT(*) FROM vw_sertifikasi $where_clause_sert";
$rTotalSertifikasi = pg_query_params($conn, $qTotalSertifikasi, $params_sert);
$total_records_sertifikasi = pg_fetch_result($rTotalSertifikasi, 0, 0);
$total_pages_sertifikasi = ceil($total_records_sertifikasi / $limit);

if ($total_records_sertifikasi === 0) {
    $page_sertifikasi = 0;
    $offset_sertifikasi = 0;
} else {
    if ($page_sertifikasi > $total_pages_sertifikasi && $total_pages_sertifikasi > 0) {
        $page_sertifikasi = $total_pages_sertifikasi;
    }
    $offset_sertifikasi = ($page_sertifikasi - 1) * $limit;
}

$qViewSertifikasi = "
    SELECT *
    FROM vw_sertifikasi
    $where_clause_sert
    ORDER BY id_sertifikasi DESC
    LIMIT $limit OFFSET $offset_sertifikasi
";
$rViewSertifikasi = pg_query_params($conn, $qViewSertifikasi, $params_sert);

$qTahunSertifikasi = "
    SELECT DISTINCT tahun_sertifikasi
    FROM vw_sertifikasi
    ORDER BY tahun_sertifikasi DESC
";
$rTahunSertifikasi = pg_query($conn, $qTahunSertifikasi);

$search_dosen = isset($_GET['search_dosen']) ? trim($_GET['search_dosen']) : '';
$filter_tahun_dosen = isset($_GET['tahun_dosen']) ? $_GET['tahun_dosen'] : '';

$where_dosen = [];
$params_dosen = [];
$idx_dosen = 1;

if (!empty($search_dosen)) {
    $where_dosen[] = "(nama_dosen ILIKE $" . $idx_dosen . " OR nama_sertifikasi ILIKE $" . $idx_dosen . " OR penyelenggara ILIKE $" . $idx_dosen . ")";
    $params_dosen[] = "%$search_dosen%";
    $idx_dosen++;
}

if (!empty($filter_tahun_dosen)) {
    $where_dosen[] = "tahun_sertifikasi = $" . $idx_dosen;
    $params_dosen[] = $filter_tahun_dosen;
    $idx_dosen++;
}

$where_clause_dosen = !empty($where_dosen) ? "WHERE " . implode(" AND ", $where_dosen) : "";

$page_dosen = isset($_GET['page_dosen']) && is_numeric($_GET['page_dosen']) ? (int)$_GET['page_dosen'] : 1;
if ($page_dosen < 1) $page_dosen = 1;

$qTotalDosen = "
    SELECT COUNT(*)
    FROM vw_sertifikasi_dosen
    $where_clause_dosen
";
$rTotalDosen = pg_query_params($conn, $qTotalDosen, $params_dosen);
$total_records_dosen = pg_fetch_result($rTotalDosen, 0, 0);
$total_pages_dosen = ceil($total_records_dosen / $limit);

if ($total_records_dosen === 0) {
    $page_dosen = 0;
    $offset_dosen = 0;
} else {
    if ($page_dosen > $total_pages_dosen && $total_pages_dosen > 0) {
        $page_dosen = $total_pages_dosen;
    }
    $offset_dosen = ($page_dosen - 1) * $limit;
}

$qViewSertifikasiDosen = "
    SELECT *
    FROM vw_sertifikasi_dosen
    $where_clause_dosen
    ORDER BY id_sertifikasi DESC
    LIMIT $limit OFFSET $offset_dosen
";
$rViewSertifikasiDosen = pg_query_params($conn, $qViewSertifikasiDosen, $params_dosen);

$qTahunDosen = "
    SELECT DISTINCT tahun_sertifikasi
    FROM vw_sertifikasi_dosen
    ORDER BY tahun_sertifikasi DESC
";
$rTahunDosen = pg_query($conn, $qTahunDosen);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Sertifikasi</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
    <link rel="stylesheet" href="css/stylePaging.css">
    <style>
        .action-btns {
            white-space: nowrap;
            display: flex;
            gap: 5px;
            justify-content: center;
        }
    </style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container-fluid px-3">

    <div class="mb-4">
        <h2 class="mb-2">Kelola Sertifikasi</h2>
        <a href="tambah_sertifikasi.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Sertifikasi
        </a>
    </div>

    <div class="search-filter-section">
        <form method="GET" action="" id="filterSertForm">
            <input type="hidden" name="page_dosen" value="<?= $page_dosen ?>">

            <div class="row g-3">

                <div class="col-md-5">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-search"></i> Pencarian Sertifikasi
                    </label>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input
                            type="text"
                            name="search_sertifikasi"
                            class="form-control"
                            placeholder="Cari nama sertifikasi atau penyelenggara..."
                            value="<?= htmlspecialchars($search_sertifikasi) ?>">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-calendar"></i> Tahun
                    </label>
                    <select name="tahun_sertifikasi" class="form-select filter-select">
                        <option value="">Semua Tahun</option>
                        <?php while($t = pg_fetch_assoc($rTahunSertifikasi)): ?>
                            <option
                                value="<?= $t['tahun_sertifikasi'] ?>"
                                <?= $filter_tahun_sertifikasi == $t['tahun_sertifikasi'] ? 'selected' : '' ?>>
                                <?= $t['tahun_sertifikasi'] ?>
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
                        <a href="?page_dosen=<?= $page_dosen ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>

            </div>

            <?php if (!empty($search_sertifikasi) || !empty($filter_tahun_sertifikasi)): ?>
                <div class="active-filters">
                    <span class="fw-semibold">Filter Aktif:</span>

                    <?php if (!empty($search_sertifikasi)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-search"></i>
                            "<?= htmlspecialchars($search_sertifikasi) ?>"
                            <span class="remove-filter" onclick="removeSertFilter('search_sertifikasi')">×</span>
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($filter_tahun_sertifikasi)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-calendar"></i>
                            <?= htmlspecialchars($filter_tahun_sertifikasi) ?>
                            <span class="remove-filter" onclick="removeSertFilter('tahun_sertifikasi')">×</span>
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
                    <col style="width:290px;">
                    <col style="width:290px;">
                    <col style="width:100px;">
                    <col style="width:200px;">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Nama Sertifikasi</th>
                        <th class="text-center">Penyelenggara</th>
                        <th class="text-center">Tahun Sertifikasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (pg_num_rows($rViewSertifikasi) > 0): ?>
                    <?php $no = $offset_sertifikasi + 1; ?>
                    <?php while($sertifikasi = pg_fetch_assoc($rViewSertifikasi)): ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center"><?= htmlspecialchars($sertifikasi['nama_sertifikasi']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($sertifikasi['penyelenggara']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($sertifikasi['tahun_sertifikasi']); ?></td>
                            <td class="text-center action-btns">
                                <a href="edit_sertifikasi.php?id=<?= $sertifikasi['id_sertifikasi']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a
                                    href="hapus_sertifikasi.php?id=<?= $sertifikasi['id_sertifikasi']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus Sertifikasi ini?')">
                                    <i class="fa fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>Tidak Ada Data</h5>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>

    <?php
    $page = $page_sertifikasi;
    $total_pages = $total_pages_sertifikasi;
    $offset = $offset_sertifikasi;
    $total_records = $total_records_sertifikasi;
    $param_name = 'page_sertifikasi';

    $other_params_sert = [
        'page_dosen'         => $page_dosen,
        'search_sertifikasi' => $search_sertifikasi,
        'tahun_sertifikasi'  => $filter_tahun_sertifikasi,
        'search_dosen'       => $search_dosen,
        'tahun_dosen'        => $filter_tahun_dosen
    ];
    $other_param = http_build_query($other_params_sert);
    $label = 'Sertifikasi';

    include 'paging.php';
    ?>

    <div class="mt-5 mb-4">
        <h2 class="mb-2">Kelola Sertifikasi Dosen</h2>
        <a href="tambah_sertifikasi_dosen.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Sertifikasi Dosen
        </a>
    </div>

    <div class="search-filter-section">
        <form method="GET" action="" id="filterDosenForm">
            <input type="hidden" name="page_sertifikasi" value="<?= $page_sertifikasi ?>">

            <div class="row g-3">

                <div class="col-md-5">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-search"></i> Pencarian Sertifikasi Dosen
                    </label>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input
                            type="text"
                            name="search_dosen"
                            class="form-control"
                            placeholder="Cari nama dosen, sertifikasi, atau penyelenggara..."
                            value="<?= htmlspecialchars($search_dosen) ?>">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-calendar"></i> Tahun
                    </label>
                    <select name="tahun_dosen" class="form-select filter-select">
                        <option value="">Semua Tahun</option>
                        <?php while($t2 = pg_fetch_assoc($rTahunDosen)): ?>
                            <option
                                value="<?= $t2['tahun_sertifikasi'] ?>"
                                <?= $filter_tahun_dosen == $t2['tahun_sertifikasi'] ? 'selected' : '' ?>>
                                <?= $t2['tahun_sertifikasi'] ?>
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
                        <a href="?page_sertifikasi=<?= $page_sertifikasi ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>

            </div>

            <?php if (!empty($search_dosen) || !empty($filter_tahun_dosen)): ?>
                <div class="active-filters">
                    <span class="fw-semibold">Filter Aktif:</span>

                    <?php if (!empty($search_dosen)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-search"></i>
                            "<?= htmlspecialchars($search_dosen) ?>"
                            <span class="remove-filter" onclick="removeDosenFilter('search_dosen')">×</span>
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($filter_tahun_dosen)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-calendar"></i>
                            <?= htmlspecialchars($filter_tahun_dosen) ?>
                            <span class="remove-filter" onclick="removeDosenFilter('tahun_dosen')">×</span>
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
                    <col style="width:290px;">
                    <col style="width:290px;">
                    <col style="width:290px;">
                    <col style="width:100px;">
                    <col style="width:200px;">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Nama Dosen</th>
                        <th class="text-center">Nama Sertifikasi</th>
                        <th class="text-center">Penyelenggara</th>
                        <th class="text-center">Tahun Sertifikasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (pg_num_rows($rViewSertifikasiDosen) > 0): ?>
                    <?php $no = $offset_dosen + 1; ?>
                    <?php while($dosen = pg_fetch_assoc($rViewSertifikasiDosen)): ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center"><?= htmlspecialchars($dosen['nama_dosen']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($dosen['nama_sertifikasi']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($dosen['penyelenggara']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($dosen['tahun_sertifikasi']); ?></td>
                            <td class="text-center action-btns">
                                <a
                                    href="edit_sertifikasi_dosen.php?id=<?= $dosen['id_sertifikasi']; ?>"
                                    class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a
                                    href="hapus_sertifikasi_dosen.php?id_dosen=<?= $dosen['id_dosen']; ?>&id_sertifikasi=<?= $dosen['id_sertifikasi']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus Sertifikasi Dosen ini?')">
                                    <i class="fa fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>Tidak Ada Data</h5>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>

    <?php
    $page = $page_dosen;
    $total_pages = $total_pages_dosen;
    $offset = $offset_dosen;
    $total_records = $total_records_dosen;
    $param_name = 'page_dosen';

    $other_params_dosen = [
        'page_sertifikasi'   => $page_sertifikasi,
        'search_sertifikasi' => $search_sertifikasi,
        'tahun_sertifikasi'  => $filter_tahun_sertifikasi,
        'search_dosen'       => $search_dosen,
        'tahun_dosen'        => $filter_tahun_dosen
    ];
    $other_param = http_build_query($other_params_dosen);
    $label = 'Sertifikasi Dosen';

    include 'paging.php';
    ?>

</div>

<script src="js/sidebar.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script>
function removeSertFilter(name) {
    const form = document.getElementById('filterSertForm');
    const input = form.querySelector('[name="' + name + '"]');
    if (input) {
        input.value = '';
        form.submit();
    }
}
function removeDosenFilter(name) {
    const form = document.getElementById('filterDosenForm');
    const input = form.querySelector('[name="' + name + '"]');
    if (input) {
        input.value = '';
        form.submit();
    }
}
</script>

</body>
</html>
