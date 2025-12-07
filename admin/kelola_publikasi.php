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

    $qTotal = "SELECT COUNT(*) FROM vw_publikasi_dosen"; 
    $rTotal = pg_query($conn, $qTotal);

    if (!$rTotal) {

        die("Query hitung total data gagal: " . pg_last_error($conn));
    }

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

    $qView = "
        SELECT 
            * FROM vw_publikasi_dosen 
        ORDER BY id_publikasi ASC
        LIMIT $limit OFFSET $offset;
    ";
    
    $rView = pg_query($conn, $qView);
    
    if (!$rView) {
        die("Query error: " . pg_last_error($conn));
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Publikasi</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
    <link rel="stylesheet" href="css/stylePaging.css">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container-fluid px-3">

    <div class="mb-4">
        <h2 class="mb-2">Kelola Publikasi</h2>
        <a href="tambah_publikasi.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Publikasi
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-fixed">
            <colgroup>
                <col style="width:40px;">
                <col style="width:300px;">
                <col style="width:120px;">
                <col style="width:180px;">
                <col style="width:170px;">
                <col style="width:150px;"> 
            </colgroup>

            <thead class="table-primary">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Judul</th>
                    <th class="text-center">Tahun</th>
                    <th class="text-center">Jenis</th>
                    <th class="text-center">Dosen</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php 
                if (pg_num_rows($rView) > 0) :
                    $no = $offset + 1; 
                    while($p = pg_fetch_assoc($rView)) : 
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= htmlspecialchars($p['judul_publikasi']); ?></td>
                <td class="text-center"><?= htmlspecialchars($p['tahun_publikasi']); ?></td>
                <td class="text-center"><?= htmlspecialchars($p['nama_jenispublikasi']); ?></td>
                <td class="text-center"><?= htmlspecialchars($p['nama_dosen']); ?></td>

                <td class="text-center">
                    <a href="edit_publikasi.php?id=<?= $p['id_publikasi']; ?>" class="btn btn-warning btn-sm">
                        <i class="fa fa-edit"></i> Edit
                    </a>

                    <a href="hapus_publikasi.php?id=<?= $p['id_publikasi']; ?>"
                    onclick="return confirm('Yakin ingin menghapus publikasi ini?')"
                    class="btn btn-danger btn-sm">
                        <i class="fa fa-trash"></i> Hapus
                    </a>
                </td>
            </tr>
            <?php 
                    endwhile; 
                else: 
            ?>
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data publikasi dosen yang ditemukan.</td>
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