<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_tahun = isset($_GET['tahun']) ? trim($_GET['tahun']) : '';

$limit = 20;

$page_proyek = isset($_GET['page_proyek']) && is_numeric($_GET['page_proyek']) ? (int)$_GET['page_proyek'] : 1;
if ($page_proyek < 1) $page_proyek = 1;

$where_proyek = "WHERE 1=1";

if ($search !== '') {
    $safe = pg_escape_string($conn, $search);
    $where_proyek .= " AND (
        judul_proyek ILIKE '%$safe%' OR 
        isi_proyek ILIKE '%$safe%' OR
        penulis_proyek ILIKE '%$safe%'
    )";
}

if ($filter_tahun !== '') {
    $safeT = pg_escape_string($conn, $filter_tahun);
    $where_proyek .= " AND EXTRACT(YEAR FROM tanggal_terbit_proyek)::int = '$safeT'";
}

$qTotalProyek = "SELECT COUNT(*) FROM proyek $where_proyek";
$rTotalProyek = pg_query($conn, $qTotalProyek);
$total_records_proyek = $rTotalProyek ? (int)pg_fetch_result($rTotalProyek, 0, 0) : 0;
$total_pages_proyek = $total_records_proyek > 0 ? ceil($total_records_proyek / $limit) : 0;

if ($total_records_proyek === 0) {
    $page_proyek = 0;
    $offset_proyek = 0;
} else {
    if ($page_proyek > $total_pages_proyek) $page_proyek = $total_pages_proyek;
    $offset_proyek = ($page_proyek - 1) * $limit;
}

$qProyek = "
    SELECT * 
    FROM vw_proyek
    $where_proyek
    ORDER BY id_proyek DESC
    LIMIT $limit OFFSET $offset_proyek
";
$rProyek = pg_query($conn, $qProyek);

$page_mhs = isset($_GET['page_mhs']) && is_numeric($_GET['page_mhs']) ? (int)$_GET['page_mhs'] : 1;
if ($page_mhs < 1) $page_mhs = 1;

$qTotalMhs = "SELECT COUNT(*) FROM proyek_mhs";
$rTotalMhs = pg_query($conn, $qTotalMhs);
$total_records_mhs = $rTotalMhs ? pg_fetch_result($rTotalMhs, 0, 0) : 0;
$total_pages_mhs = $total_records_mhs > 0 ? ceil($total_records_mhs / $limit) : 0;

if ($total_records_mhs === 0) {
    $page_mhs = 0;
    $offset_mhs = 0;
} else {
    if ($page_mhs > $total_pages_mhs) $page_mhs = $total_pages_mhs;
    $offset_mhs = ($page_mhs - 1) * $limit;
}

$qPM = "SELECT * FROM vw_proyek_mhs ORDER BY id_proyek ASC LIMIT $limit OFFSET $offset_mhs";
$rPM = pg_query($conn, $qPM);

$page_dosen = isset($_GET['page_dosen']) && is_numeric($_GET['page_dosen']) ? (int)$_GET['page_dosen'] : 1;
if ($page_dosen < 1) $page_dosen = 1;

$qTotalDosen = "SELECT COUNT(*) FROM proyek_dosen";
$rTotalDosen = pg_query($conn, $qTotalDosen);
$total_records_dosen = $rTotalDosen ? pg_fetch_result($rTotalDosen, 0, 0) : 0;
$total_pages_dosen = $total_records_dosen > 0 ? ceil($total_records_dosen / $limit) : 0;

if ($total_records_dosen === 0) {
    $page_dosen = 0;
    $offset_dosen = 0;
} else {
    if ($page_dosen > $total_pages_dosen) $page_dosen = $total_pages_dosen;
    $offset_dosen = ($page_dosen - 1) * $limit;
}

$qPD = "SELECT * FROM vw_proyek_dosen ORDER BY id_proyek ASC LIMIT $limit OFFSET $offset_dosen";
$rPD = pg_query($conn, $qPD);

if (!$rProyek || !$rPM || !$rPD) {
    die("Gagal mengambil data dari database: " . pg_last_error($conn));
}

$qTahun = "
    SELECT DISTINCT EXTRACT(YEAR FROM tanggal_terbit_proyek)::int AS tahun
    FROM proyek
    WHERE tanggal_terbit_proyek IS NOT NULL
    ORDER BY tahun DESC
";
$rTahun = pg_query($conn, $qTahun);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Proyek</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
    <link rel="stylesheet" href="css/stylePaging.css">
</head>

<body>
<?php include 'sidebar.php'; ?>

<div class="content-area container">
    <div class="mb-4">
        <div>
            <h2 class="mb-2">Kelola Proyek</h2>
        </div>
        <a href="tambah_proyek.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Proyek
        </a>
    </div>

    <div class="search-filter-section">
        <form method="GET" action="" id="filterForm" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold"><i class="fas fa-search"></i> Pencarian</label>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Cari judul, penulis, atau deskripsi..."
                        value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold"><i class="fas fa-calendar"></i> Tahun</label>
                <select name="tahun" class="form-select filter-select">
                    <option value="">Semua Tahun</option>
                    <?php while($t = pg_fetch_assoc($rTahun)): ?>
                        <option value="<?= $t['tahun'] ?>" <?= $filter_tahun == $t['tahun'] ? 'selected' : '' ?>>
                            <?= $t['tahun'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
                <a href="kelola_proyek.php" class="btn btn-outline-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>

            <?php if (!empty($search) || !empty($filter_tahun)): ?>
            <div class="col-12">
                <div class="active-filters">
                    <span class="fw-semibold">Filter Aktif:</span>
                    <?php if (!empty($search)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-search"></i> "<?= htmlspecialchars($search) ?>"
                            <span class="remove-filter" onclick="removeFilter('search')">×</span>
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($filter_tahun)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-calendar"></i> <?= htmlspecialchars($filter_tahun) ?>
                            <span class="remove-filter" onclick="removeFilter('tahun')">×</span>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table modern-table">
                <colgroup>
                    <col style="width:40px;">
                    <col style="width:180px;">
                    <col style="width:200px;">
                    <col style="width:300px;">
                    <col style="width:140px;">
                    <col style="width:180px;">
                    <col style="width:180px;">
                </colgroup>

                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Gambar</th>
                        <th class="text-center">Judul</th>
                        <th class="text-center">Deskripsi</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Penulis</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (pg_num_rows($rProyek) > 0): 
                    $no = $offset_proyek + 1;
                    while($pm = pg_fetch_assoc($rProyek)):
                ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center">
                            <?php if (!empty($pm['url_gambar_proyek1'])): ?>
                                <img src="../<?= htmlspecialchars($pm['url_gambar_proyek1']) ?>" alt="Gambar Proyek" class="tbl-thumb">
                            <?php else: ?>
                                <span class="text-muted">No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($pm['judul_proyek']) ?></td>
                        <td class="text-truncate-custom"><?= htmlspecialchars(substr($pm['isi_proyek'],0,140)) ?>...</td>
                        <td class="text-center"><?= htmlspecialchars($pm['tanggal_terbit_proyek']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($pm['penulis_proyek']) ?></td>
                        <td class="text-center text-nowrap">
                            <a href="edit_proyek.php?id=<?= $pm['id_proyek'] ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="hapus_proyek.php?id=<?= $pm['id_proyek'] ?>"
                               onclick="return confirm('Yakin ingin menghapus?')"
                               class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>Tidak Ada Data</h5>
                                <p class="text-muted">
                                    <?php if (!empty($search) || !empty($filter_tahun)): ?>
                                        Tidak ada proyek yang sesuai filter.
                                    <?php else: ?>
                                        Tidak ada data proyek.
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
        $page = $page_proyek;
        $total_pages = $total_pages_proyek;
        $offset = $offset_proyek;
        $total_records = $total_records_proyek;
        $param_name = 'page_proyek';
        $other_param = 'page_mhs=' . $page_mhs . '&page_dosen=' . $page_dosen . '&search=' . urlencode($search) . '&tahun=' . urlencode($filter_tahun);
        $label = 'Proyek';
        include 'paging.php';
    ?>

    <hr class="my-5">

    <div class="mb-4">
        <h2 class="mb-2">Kelola Proyek Mahasiswa</h2>
        <a href="tambah_proyek_mhs.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Proyek
        </a>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table modern-table">
                <colgroup>
                    <col style="width:40px;">
                    <col style="width:120px;">
                    <col style="width:200px;">
                    <col style="width:300px;">
                    <col style="width:140px;">
                </colgroup>

                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">ID Mahasiswa</th>
                        <th class="text-center">Judul Proyek</th>
                        <th class="text-center">Nama Mahasiswa</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (pg_num_rows($rPM) > 0):
                    $no = $offset_mhs + 1;
                    while($pm = pg_fetch_assoc($rPM)):
                ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= htmlspecialchars($pm['id_mhs']) ?></td>
                        <td><?= htmlspecialchars($pm['judul_proyek']) ?></td>
                        <td><?= htmlspecialchars($pm['nama_mhs']) ?></td>
                        <td class="text-center text-nowrap">
                            <a href="edit_proyek_mhs.php?id_proyek=<?= $pm['id_proyek'] ?>&id_mhs=<?= $pm['id_mhs'] ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="hapus_proyek_mhs.php?id_proyek=<?= $pm['id_proyek'] ?>&id_mhs=<?= $pm['id_mhs'] ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data Proyek Mahasiswa.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php 
        $page = $page_mhs;
        $total_pages = $total_pages_mhs;
        $offset = $offset_mhs;
        $total_records = $total_records_mhs;
        $param_name = 'page_mhs';
        $other_param = 'page_proyek=' . $page_proyek . '&page_dosen=' . $page_dosen;
        $label = 'Proyek Mahasiswa';
        include 'paging.php';
    ?>

    <hr class="my-5">

    <div class="mb-4">
        <h2 class="mb-2">Kelola Proyek Dosen</h2>
        <a href="tambah_proyek_dosen.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Proyek
        </a>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table modern-table">
                <colgroup>
                    <col style="width:40px;">
                    <col style="width:100px;">
                    <col style="width:200px;">
                    <col style="width:300px;">
                    <col style="width:140px;">
                </colgroup>

                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">ID Dosen</th>
                        <th class="text-center">Judul Proyek</th>
                        <th class="text-center">Nama Dosen</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (pg_num_rows($rPD) > 0):
                    $no = $offset_dosen + 1;
                    while($pd = pg_fetch_assoc($rPD)):
                ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= htmlspecialchars($pd['id_dosen']) ?></td>
                        <td><?= htmlspecialchars($pd['judul_proyek']) ?></td>
                        <td><?= htmlspecialchars($pd['nama_dosen']) ?></td>
                        <td class="text-center text-nowrap">
                            <a href="edit_proyek_dosen.php?id_proyek=<?= $pd['id_proyek'] ?>&id_dosen=<?= $pd['id_dosen'] ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="hapus_proyek_dosen.php?id_proyek=<?= $pd['id_proyek'] ?>&id_dosen=<?= $pd['id_dosen'] ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data Proyek Dosen.</td>
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
        $other_param = 'page_proyek=' . $page_proyek . '&page_mhs=' . $page_mhs;
        $label = 'Proyek Dosen';
        include 'paging.php';
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
