<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require '../config.php';

$limit = 20;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$qTotal = "SELECT COUNT(*) FROM jenis_mitra";
$rTotal = pg_query($conn, $qTotal);
$total_records = pg_fetch_result($rTotal, 0, 0);

$total_pages = ceil($total_records / $limit);

if ($total_records === 0) {
    $page = 0;
    $offset = 0;
} else {
    if ($page > $total_pages) {
        $page = $total_pages;
    }
    $offset = ($page - 1) * $limit;
}

$qJenis = "SELECT * FROM jenis_mitra 
           ORDER BY id_jenismitra ASC 
           LIMIT $limit OFFSET $offset";
$rJenis = pg_query($conn, $qJenis);

if (!$rJenis) {
    die("Query error: " . pg_last_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jenis Mitra</title>

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
        <h2 class="mb-2">Kelola Jenis Mitra</h2>
        <a href="tambah_jenisMitra.php" class="btn btn-success">
            <i class="fa fa-plus"></i> Tambah Jenis Mitra
        </a>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table modern-table">
                <colgroup>
                    <col style="width:50px;">
                    <col style="width:400px;">
                    <col style="width:200px;">
                </colgroup>

                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Nama Jenis Mitra</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (pg_num_rows($rJenis) > 0) : ?>
                        <?php $no = $offset + 1; ?>
                        <?php while ($j = pg_fetch_assoc($rJenis)) : ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td class="text-center"><?= htmlspecialchars($j['nama_jenismitra']); ?></td>
                                <td>
                                    <div class="action-buttons text-center">
                                        <a href="edit_jenisMitra.php?id=<?= $j['id_jenismitra']; ?>" 
                                           class="btn btn-action btn-warning btn-sm">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <a href="hapus_jenisMitra.php?id=<?= $j['id_jenismitra']; ?>"
                                           onclick="return confirm('Yakin ingin menghapus jenis mitra ini?')"
                                           class="btn btn-action btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h5>Tidak Ada Data</h5>
                                    <p class="text-muted">
                                        Tidak ada data jenis mitra yang ditemukan.
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
</body>
</html>