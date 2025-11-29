<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    $qViewPendaftar = "SELECT * FROM vw_pendaftaran_segeeks ORDER BY id_pendaftar";
    $rViewPendaftar = pg_query($conn, $qViewPendaftar);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal LAB SE</title>
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
            <h2 class="mb-2">Kelola Pendaftaran SE Geeks</h2>
            <a href="../form_daftar.php" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Pendaftar</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <colgroup>
                    <col style="width:40px;">
                    <col style="width:200px;">
                    <col style="width:150px;">
                    <col style="width:230px;">
                    <col style="width:150px;">
                    <col style="width:200px;">
                    <col style="width:200px;">
                </colgroup>
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Nama Pendaftar</th>
                        <th class="text-center">NIM Pendaftar</th>
                        <th class="text-center">Program Studi</th>
                        <th class="text-center">Angkatan</th>
                        <th class="text-center">Status Pendaftaran</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($pendaftar = pg_fetch_assoc($rViewPendaftar)) : ?>
                        <tr>
                            <td class="text-center"><?= $pendaftar['id_pendaftar']; ?></td>
                            <td class="text-center"><?= htmlspecialchars($pendaftar['nama_pendaftar']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($pendaftar['nim_pendaftar']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($pendaftar['prodi_pendaftar']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($pendaftar['angkatan_pendaftar']); ?></td>
                            <td class="text-center">
                                <?php 
                                    if ($pendaftar['status_pendaftaran']) {
                                        echo htmlspecialchars($pendaftar['status_pendaftaran']);
                                    } else {
                                        echo "Belum dikonfirmasi";
                                    }
                                ?>
                            </td>
                            <td class="text-center">
                                <a href="terima_pendaftar.php?id=<?= $pendaftar['id_pendaftar'] ?>" 
                                class="btn btn-success btn-sm"
                                onclick="return confirm('Terima pendaftar ini?')">
                                <i class="fa fa-check"></i> Terima
                                </a>

                                <a href="tolak_pendaftar.php?id=<?= $pendaftar['id_pendaftar'] ?>" 
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Tolak pendaftar ini?')">
                                <i class="fa fa-times"></i> Tolak
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>