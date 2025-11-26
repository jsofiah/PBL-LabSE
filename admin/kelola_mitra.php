<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

// Ambil semua data mitra
$qViewMitra = "SELECT * FROM mitra ORDER BY id_mitra";
$rViewMitra = pg_query($conn, $qViewMitra);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Mitra</title>

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
                    <col style="width:250px;">
                    <col style="width:360px;">
                    <col style="width:190px;">
                </colgroup>

                <thead class="table-primary">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Gambar</th>
                        <th class="text-center">Nama Mitra</th>
                        <th class="text-center">Isi Mitra</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($m = pg_fetch_assoc($rViewMitra)) : ?>
                        <tr>
                            <!-- ID -->
                            <td class="text-center"><?= $m['id_mitra']; ?></td>

                            <!-- Gambar mitra -->
                            <td class="text-center">
                                <?php if (!empty($m['url_gambar_mitra'])) : ?>
                                    <img src="../<?= htmlspecialchars($m['url_gambar_mitra']); ?>"
                                         alt="Gambar Mitra"
                                         style="width:100px; height:auto; border-radius:5px;">
                                <?php else : ?>
                                    <span class="text-muted">Tidak ada gambar</span>
                                <?php endif; ?>
                            </td>

                            <!-- Nama -->
                            <td class="text-center"><?= htmlspecialchars($m['nama_mitra']); ?></td>

                            <!-- Deskripsi -->
                            <td class="text-center">
                                <?= htmlspecialchars(substr($m['isi_mitra'], 0, 60)); ?>...
                            </td>

                            <!-- Aksi -->
                            <td class="text-center">
                                <a href="edit_mitra.php?id=<?= $m['id_mitra']; ?>" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>

                                <a href="hapus_mitra.php?id=<?= $m['id_mitra']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus mitra ini?')">
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
