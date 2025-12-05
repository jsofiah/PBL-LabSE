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

    $qTotal = "SELECT COUNT(*) FROM vw_fasilitas_lengkap";
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

    $qFasilitas = "SELECT * FROM vw_fasilitas_lengkap 
                   ORDER BY id_fasilitas ASC 
                   LIMIT $limit OFFSET $offset";
    $rFasilitas = pg_query($conn, $qFasilitas);

    if (!$rFasilitas) {
        die("Query error: " . pg_last_error($conn));
    }

    $fasilitas = [];
    while ($row = pg_fetch_assoc($rFasilitas)) {
        $fasilitas[] = $row;
    }
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
    <link rel="stylesheet" href="css/styleTabel.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="content-area container">

        <div class="mb-4">
            <h2 class="mb-2">Kelola Fasilitas</h2>
            <a href="tambah_fasilitas.php" class="btn btn-success btn-sm">
                <i class="fa fa-plus"></i> Tambah Fasilitas
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <colgroup>
                    <col style="width:40px">
                    <col style="width:120px">
                    <col style="width:200px">
                    <col style="width:300px">
                    <col style="width:160px">
                    <col style="width:200px">
                </colgroup>

                <thead class="table-primary">
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
                    <?php if (count($fasilitas) > 0): ?>
                        <?php $no = $offset + 1; ?>
                        <?php foreach($fasilitas as $row) : ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['nama_jenisfasilitas']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['nama_fasilitas']); ?></td>
                            <td><?= nl2br(htmlspecialchars($row['isi_fasilitas'])); ?></td>

                            <td class="text-center">
                                <img src="../<?= htmlspecialchars($row['url_gambar_fasilitas']); ?>"
                                    style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                            </td>

                            <td class="text-center">
                                <a href="edit_fasilitas.php?id=<?= $row['id_fasilitas']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="hapus_fasilitas.php?id=<?= $row['id_fasilitas']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin ingin menghapus fasilitas ini?');">
                                    <i class="fa fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data fasilitas.</td>
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