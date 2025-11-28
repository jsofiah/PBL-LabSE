<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once "../config.php";

$qJenis = "SELECT * FROM jenis_artikel ORDER BY id_jenisartikel ASC";
$rJenis = pg_query($conn, $qJenis);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Jenis Artikel</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styleSidebar.css">
<link rel="stylesheet" href="css/styleTabel.css">
</head>

<body>
<?php include 'sidebar.php'; ?>

<div class="content-area container-fluid px-3">

    <div class="mb-4">
        <h2 class="mb-2">Kelola Jenis Artikel</h2>
        <a href="tambah_jenisartikel.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Jenis Artikel
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-fixed">
            <thead class="table-primary">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Nama Jenis Artikel</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php while($row = pg_fetch_assoc($rJenis)) : ?>
                <tr>
                    <td class="text-center"><?= $row['id_jenisartikel']; ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['nama_jenisartikel']); ?></td>
                    <td class="text-center">
                        <a href="edit_jenisartikel.php?id=<?= $row['id_jenisartikel']; ?>" class="btn btn-warning btn-sm">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a onclick="return confirm('Yakin ingin menghapus?')"
                           href="hapus_jenisartikel.php?id=<?= $row['id_jenisartikel']; ?>"
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
