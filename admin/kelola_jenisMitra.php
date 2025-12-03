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

if ($total_pages === 0) {
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container">

    <div class="mb-4">
        <h2 class="mb-2">Kelola Jenis Mitra</h2>
        <a href="tambah_jenisMitra.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Jenis Mitra
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-fixed">
            <colgroup>
                <col style="width:50px;">
                <col style="width:250px;">
                <col style="width:120px;">
            </colgroup>

            <thead class="table-primary">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Nama Jenis Mitra</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php if (pg_num_rows($rJenis) > 0) : ?>
                    <?php while ($j = pg_fetch_assoc($rJenis)) : ?>
                        <tr>
                            <td class="text-center"><?= $j['id_jenismitra'] ?></td>

                            <td class="text-center"><?= htmlspecialchars($j['nama_jenismitra'] ?? '') ?></td>

                            <td class="text-center">
                                <a href="edit_jenisMitra.php?id=<?= $j['id_jenismitra'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                <a href="hapus_jenisMitra.php?id=<?= $j['id_jenismitra'] ?>"
                                   onclick="return confirm('Yakin ingin menghapus jenis mitra ini?')"
                                   class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data jenis mitra.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo; Sebelumnya</span>
                    </a>
                </li>

                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++): 
                ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">Berikutnya &raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <p class="text-center text-muted mt-2">
        Menampilkan data <?= $offset + 1; ?> - <?= min($offset + $limit, $total_records); ?> dari total **<?= $total_records; ?>** data. 
    </p>
    <?php endif; ?>

</div>

    <script src="js/sidebar.js"></script>
</body>
</html>