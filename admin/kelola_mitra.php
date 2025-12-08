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

$where = "WHERE 1=1";

if ($search !== '') {
    $safe = pg_escape_string($conn, $search);
    $where .= " AND (
                    m.nama_mitra ILIKE '%$safe%' OR 
                    m.isi_mitra ILIKE '%$safe%'
                )";
}

if ($filterJenis !== '') {
    $safeJenis = pg_escape_string($conn, $filterJenis);
    $where .= " AND m.id_jenismitra = '$safeJenis'";
}

$qTotal = "
    SELECT COUNT(*)
    FROM mitra m
    JOIN jenis_mitra j ON j.id_jenismitra = m.id_jenismitra
    $where
";
$rTotal = pg_query($conn, $qTotal);
$total_records = pg_fetch_result($rTotal, 0, 0);

$total_pages = ($total_records > 0) ? ceil($total_records / $limit) : 1;
if ($page > $total_pages) $page = $total_pages;

$offset = ($page - 1) * $limit;

$qViewMitra = "
    SELECT 
        m.id_mitra,
        m.nama_mitra,
        m.url_gambar_mitra,
        m.isi_mitra,
        j.nama_jenismitra
    FROM mitra m
    JOIN jenis_mitra j ON j.id_jenismitra = m.id_jenismitra
    $where
    ORDER BY m.id_mitra ASC
    LIMIT $limit OFFSET $offset
";
$rViewMitra = pg_query($conn, $qViewMitra);

$qJenis = "SELECT id_jenismitra, nama_jenismitra FROM jenis_mitra ORDER BY nama_jenismitra ASC";
$rJenis = pg_query($conn, $qJenis);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Mitra</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
    <link rel="stylesheet" href="css/stylePaging.css">

    <style>
        .no-data-box {
            text-align: center;
            padding: 50px 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px dashed #c9c9c9;
        }
        .no-data-box i {
            font-size: 45px;
            color: #6c757d;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container-fluid px-3">
    <div class="mb-4">
        <h2 class="mb-2">Kelola Mitra</h2>
        <a href="tambah_mitra.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Mitra
        </a>
    </div>

    <div class="search-filter-section mb-3">
        <form method="GET" action="" id="filterForm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><i class="fas fa-search"></i> Pencarian</label>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama atau deskripsi..." value="<?= htmlspecialchars($search) ?>">
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

    <div class="table-container">

    <?php if ($total_records == 0): ?>
        <tr>
            <td colspan="7">
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h5>Tidak Ada Data</h5>
                    <p class="text-muted">
                        <?php if (!empty($search)): ?>
                            Tidak ada mitra yang sesuai filter.
                    <?php else: ?>
                            Tidak ada data mitra.
                    <?php endif; ?>
                    </p>
                </div>
            </td>
        </tr>

    <?php else: ?>

    <div class="table-responsive">
        <table class="table modern-table">

            <colgroup>
                <col style="width:40px;">
                <col style="width:180px;">
                <col style="width:200px;">
                <col style="width:300px;">
                <col style="width:200px;">
                <col style="width:180px;">
            </colgroup>

            <thead class="table-primary">
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
            <?php $no = $offset + 1; ?>
            <?php while ($m = pg_fetch_assoc($rViewMitra)) : ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td class="text-center">
                        <?php if (!empty($m['url_gambar_mitra'])) : ?>
                            <img src="../<?= htmlspecialchars($m['url_gambar_mitra']); ?>"
                                 style="width:100px; height:auto; border-radius:5px;">
                        <?php else : ?>
                            <span class="text-muted">Tidak ada gambar</span>
                        <?php endif; ?>
                    </td>

                    <td class="text-center"><?= htmlspecialchars($m['nama_mitra']); ?></td>

                    <td><?= htmlspecialchars(substr($m['isi_mitra'], 0, 100)); ?>...</td>

                    <td class="text-center"><?= htmlspecialchars($m['nama_jenismitra']); ?></td>

                    <td class="text-center">
                        <a href="edit_mitra.php?id=<?= $m['id_mitra']; ?>" class="btn btn-warning btn-sm">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="hapus_mitra.php?id=<?= $m['id_mitra']; ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin ingin menghapus mitra ini?')">
                            <i class="fa fa-trash"></i> Hapus
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php include 'paging.php'; ?>

    <?php endif; ?>

</div>

<script src="js/sidebar.js"></script>
</body>
</html>
