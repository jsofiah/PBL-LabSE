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

$qTotal = "SELECT COUNT(*) FROM jenis_fasilitas";
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

$qJenis = "SELECT * FROM jenis_fasilitas 
           ORDER BY id_jenisfasilitas ASC 
           LIMIT $limit OFFSET $offset";
$rJenis = pg_query($conn, $qJenis);

if (!$rJenis) {
    die("Query error: " . pg_last_error($conn));
}

$jenisFasilitas = [];
while ($row = pg_fetch_assoc($rJenis)) {
    $jenisFasilitas[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jenis Fasilitas</title>

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
            <h2 class="mb-2">Kelola Jenis Fasilitas</h2>
            <a href="tambah_jenisfasilitas.php" class="btn btn-success btn-sm">
                <i class="fa fa-plus"></i> Tambah Jenis Fasilitas
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <colgroup>
                    <col style="width:60px">
                    <col style="width:250px">
                    <col style="width:120px">
                </colgroup>

                <thead class="table-primary">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Nama Jenis Fasilitas</th>
                    <th class="text-center">Aksi</th>
                </tr>
                </thead>

                <tbody>
                    <?php if (count($jenisFasilitas) > 0) : ?>
                        <?php $no = $offset + 1; ?>
                        <?php foreach ($jenisFasilitas as $jf) : ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td class="text-center"><?= htmlspecialchars($jf['nama_jenisfasilitas']); ?></td>

                                <td class="text-center">
                                    <a href="edit_jenisfasilitas.php?id=<?= $jf['id_jenisfasilitas']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a href="hapus_jenisfasilitas.php?id=<?= $jf['id_jenisfasilitas']; ?>" 
                                    onclick="return confirm('Yakin ingin menghapus jenis fasilitas ini?')"
                                    class="btn btn-danger btn-sm">
                                        <i class="fa fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data jenis fasilitas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>

        <?php include 'paging.php'; ?>

    </div>

    <script src="js/sidebar.js"></script>
</body>

</html>