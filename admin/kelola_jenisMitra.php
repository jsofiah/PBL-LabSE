<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require '../config.php';

$qJenis = "SELECT * FROM jenis_mitra ORDER BY id_jenismitra ASC";
$rJenis = pg_query($conn, $qJenis);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
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
                <col style="width:100px;">
                <col style="width:80px;">
            </colgroup>

        <thead class="table-primary">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Nama Jenis Mitra</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
                </tr>
            </thead>

            <tbody>
                <?php while ($j = pg_fetch_assoc($rJenis)) : ?>
                    <tr>
                        <!-- ID Jenis Mitra -->
                        <td class="text-center"><?= $j['id_jenismitra'] ?></td>

                        <!-- Jenis Mitra -->
                        <td class="text-center"><?= htmlspecialchars($j['nama_jenismitra']?? '') ?></td>

                        <!-- ID Proyek Dosen -->
                        <td class="text-center">
                            <a href="edit_jenisMitra.php?id=<?= $j['id_jenismitra'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i>Edit</a>
                            <a href="hapus_jenisMitra.php?id=<?= $j['id_jenismitra'] ?>"
                               onclick="return confirm('Yakin ingin menghapus jenis mitra ini?')"
                               class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>
                                Hapus
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
