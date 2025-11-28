<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    $qViewKeahlian = "SELECT * FROM vw_mhs_keahlian ORDER BY id_mhs";
    $rViewKeahlian = pg_query($conn, $qViewKeahlian);
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
            <h2 class="mb-2">Kelola Keahlian Mahasiswa</h2>
            <a href="tambah_keahlianMhs.php" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Keahlian Mahasiswa</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <colgroup>
                    <col style="width:40px;">
                    <col style="width:200px;">
                    <col style="width:230px;">
                    <col style="width:200px;">
                </colgroup>
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Nama Mahasiswa</th>
                        <th class="text-center">Nama Keahlian</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($keahlian = pg_fetch_assoc($rViewKeahlian)) : ?>
                        <tr>
                            <td class="text-center"><?= $keahlian['id_mhs']; ?></td>
                            <td class="text-center"><?= htmlspecialchars($keahlian['nama_mhs']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($keahlian['nama_keahlian']); ?></td>
                            <td class="text-center">
                                <a href="edit_keahlianMhs.php?id_mhs=<?= $keahlian['id_mhs'] ?>&id_keahlian=<?= $keahlian['id_keahlian'] ?>" 
                                class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="hapus_keahlianMhs.php?id_mhs=<?= $keahlian['id_mhs']; ?>&id_keahlian=<?= $keahlian['id_keahlian']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin ingin menghapus Keahlian ini?')">
                                    <i class="fa fa-trash"></i> Hapus
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