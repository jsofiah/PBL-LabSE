<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

// Ambil data peran lab
$query = "SELECT * FROM peran_lab ORDER BY id_peran ASC";
$result = pg_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Peran Lab</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="content-area container">
        <div class="mb-4">
            <h2 class="mb-2">Kelola Peran Lab</h2>
            <a href="tambah_peranLab.php" class="btn btn-success btn-sm">
                <i class="fa fa-plus"></i> Tambah Peran
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <colgroup>
                    <col style="width: 50px">
                    <col style="width: 180px">
                    <col style="width: 300px">
                    <col style="width: 120px">
                    <col style="width: 150px">
                </colgroup>

                <thead class="table-primary">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Nama Peran</th>
                        <th class="text-center">Deskripsi</th>
                        <th class="text-center">Icon (Text)</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = pg_fetch_assoc($result)) : ?>
                        <tr>
                            <td class="text-center"><?= $row['id_peran'] ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['nama_peran']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['deskripsi_peran']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['icon']) ?></td>

                            <td class="text-center">
                                <a href="edit_peranLab.php?id=<?= $row['id_peran'] ?>" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>

                                <a href="hapus_peranLab.php?id=<?= $row['id_peran'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus peran ini?')">
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
