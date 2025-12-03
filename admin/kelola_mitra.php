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

$offset = ($page - 1) * $limit;

$qTotal = "SELECT COUNT(*) FROM vw_mitra";
$rTotal = pg_query($conn, $qTotal);
$total_records = pg_fetch_result($rTotal, 0, 0);

$total_pages = ceil($total_records / $limit);

if ($page > $total_pages && $total_pages > 0) {
    $page = $total_pages;
    $offset = ($page - 1) * $limit;
} elseif ($total_pages === 0) {
    $page = 0;
    $offset = 0;
}

$qViewMitra = "SELECT * FROM vw_mitra 
               ORDER BY id_mitra ASC 
               LIMIT $limit OFFSET $offset";
$rViewMitra = pg_query($conn, $qViewMitra);

if (!$rViewMitra) {
    die("Query error: " . pg_last_error($conn));
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
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container">

    <div class="mb-4">
        <h2 class="mb-2">Kelola Mitra</h2>
        <a href="tambah_mitra.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Mitra
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-fixed">

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
            <?php if (pg_num_rows($rViewMitra) > 0): ?>
                <?php $no = 1; ?>
                <?php while ($m = pg_fetch_assoc($rViewMitra)) : ?>
                    <tr>

                        <td class="text-center"><?= $no++; ?></td>

                        <td class="text-center">
                            <?php if (!empty($m['url_gambar_mitra'])) : ?>
                                <img src="../<?= htmlspecialchars($m['url_gambar_mitra']); ?>"
                                    alt="Logo Mitra"
                                    style="width:100px; border-radius:5px;">
                            <?php else : ?>
                                <span class="text-muted">Tidak ada gambar</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-center"><?= htmlspecialchars($m['nama_mitra']); ?></td>

                        <td class="text-center"><?= htmlspecialchars(substr($m['isi_mitra'], 0, 60)); ?>...</td>

                        <td class="text-center"><?= htmlspecialchars($m['nama_jenismitra']); ?></td>

                        <td class="text-center">
                            <a href="edit_mitra.php?id=<?= $m['id_mitra']; ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            <a href="hapus_mitra.php?id=<?= $m['id_mitra']; ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin ingin menghapus mitra ini?')">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>

                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data mitra.</td>
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