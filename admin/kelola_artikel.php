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

$qTotal = "SELECT COUNT(*) FROM artikel"; 
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

$qArtikel = "
    SELECT * FROM vw_artikel 
    ORDER BY id_artikel DESC -- Urutkan artikel terbaru di atas
    LIMIT $limit OFFSET $offset
";
$rArtikel = pg_query($conn, $qArtikel);

if (!$rArtikel) {
    die("Query error: " . pg_last_error($conn));
}
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
    <link rel="stylesheet" href="css/styleTabel.css">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content-area container-fluid px-3">

    <div class="mb-4">
        <h2 class="mb-2">Kelola Artikel</h2>

        <a href="tambah_artikel.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Artikel
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-fixed">
            <colgroup>
                <col style="width:40px">
                <col style="width:250px">
                <col style="width:150px">
                <col style="width:150px">
                <col style="width:150px">
                <col style="width:150px">
            </colgroup>

            <thead class="table-primary">
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
                if (pg_num_rows($rArtikel) > 0) :
                    $no = $offset + 1; 
                    while($a = pg_fetch_assoc($rArtikel)) : 
                ?>
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
                <?php 
                    endwhile; 
                else: 
                ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data Artikel yang ditemukan.</td>
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