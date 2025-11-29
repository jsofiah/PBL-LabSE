<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

// Ambil data jenis fasilitas
$qJenis = "SELECT * FROM jenis_fasilitas ORDER BY id_jenisfasilitas";
$rJenis = pg_query($conn, $qJenis);

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

        <!-- HEADER -->
        <div class="mb-4">
            <h2 class="mb-2">Kelola Jenis Fasilitas</h2>
            <a href="tambah_jenisfasilitas.php" class="btn btn-success btn-sm">
                <i class="fa fa-plus"></i> Tambah Jenis Fasilitas
            </a>
        </div>

        <!-- TABEL -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <colgroup>
                    <col style="width:60px">
                    <col style="width:250px">
                    <col style="width:120px">
                </colgroup>

                <thead class="table-primary">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Nama Jenis Fasilitas</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($jenisFasilitas as $jf) : ?>
                        <tr>
                            <td class="text-center"><?= $jf['id_jenisfasilitas']; ?></td>
                            <td class="text-center"><?= htmlspecialchars($jf['nama_jenisfasilitas']); ?></td>

                            <td class="text-center">
                                <a href="edit_jenisfasilitas.php?id=<?= $jf['id_jenisfasilitas']; ?>" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>

                                <a href="hapus_jenisfasilitas.php?id=<?= $jf['id_jenisfasilitas']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus jenis fasilitas ini?')">
                                    <i class="fa fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>

    <script src="js/sidebar.js"></script>
</body>

</html>
