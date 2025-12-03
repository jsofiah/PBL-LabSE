<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    $qViewAdmin = "SELECT * FROM vw_admin_user ORDER BY id ASC";
    $rViewAdmin = pg_query($conn, $qViewAdmin);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Admin - Portal LAB SE</title>
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
            <h2 class="mb-3">Kelola Admin User</h2>
            <a href="tambah_admin.php" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Admin</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <colgroup>
                    <col style="width:50px">
                    <col style="width:100px">
                    <col style="width:150px">
                    <col style="width:100px">
                </colgroup>
                <thead class="table-primary">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Foto</th>
                    <th class="text-center">Username</th>
                    <th class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php while ($row = pg_fetch_assoc($rViewAdmin)) : ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>

                            <td class="text-center">
                                <?php if (!empty($row['foto_admin'])): ?>
                                    <img src="<?= htmlspecialchars($row['foto_admin']); ?>" style="width:80px; border-radius:5px;">
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center"><?= htmlspecialchars($row['username']); ?></td>

                            <td class="text-center">
                                <a href="edit_admin.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>

                                <?php if ($row['username'] !== $_SESSION['username']): ?>
                                    <a href="hapus_admin.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus Admin ini?')">
                                    <i class="fa fa-trash"></i> Hapus
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled><i class="fa fa-trash"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="js/sidebar.js"></script>
</body>
</html>