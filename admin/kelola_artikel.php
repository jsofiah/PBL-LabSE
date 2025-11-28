<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "../config.php";

// Ambil semua artikel
$qArtikel = "SELECT * FROM vw_artikel ORDER BY id_artikel ASC";
$rArtikel = pg_query($conn, $qArtikel);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Artikel</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

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
                    <th class="text-center">ID</th>
                    <th class="text-center">Judul Artikel</th>
                    <th class="text-center">Jenis</th>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Penulis</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php while($a = pg_fetch_assoc($rArtikel)) : ?>
                <tr>
                    <td class="text-center"><?= $a['id_artikel']; ?></td>
                    <td><?= htmlspecialchars($a['judul_artikel']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($a['nama_jenisartikel']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($a['tanggal_terbit_artikel']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($a['penulis_artikel']); ?></td>

                    <td class="text-center">
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
                <?php endwhile; ?>
            </tbody>

        </table>
    </div>

</div>

<script src="js/sidebar.js"></script>
</body>
</html>
