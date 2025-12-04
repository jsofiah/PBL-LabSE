<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    $limit = 20;

    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $qTotal = "SELECT COUNT(*) FROM vw_admin_user"; 
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

    $qViewAdmin = "
        SELECT * FROM vw_admin_user 
        ORDER BY id ASC 
        LIMIT $limit OFFSET $offset
    ";
    $rViewAdmin = pg_query($conn, $qViewAdmin);

    if (!$rViewAdmin) {
        die("Query error: " . pg_last_error($conn));
    }
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
                    <?php 
                    if (pg_num_rows($rViewAdmin) > 0) :
                        $no = $offset + 1; 
                        while ($row = pg_fetch_assoc($rViewAdmin)) : 
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>

                            <td class="text-center">
                                <?php if (!empty($row['foto_admin'])): ?>
                                    <img src="<?= htmlspecialchars($row['foto_admin']); ?>" style="width:80px; height: 80px; object-fit: cover; border-radius:5px;">
                                <?php else: ?>
                                    <img src="https://placehold.co/80x80/cccccc/000000?text=No+Photo" style="width:80px; height: 80px; object-fit: cover; border-radius:5px;">
                                <?php endif; ?>
                            </td>

                            <td class="text-center"><?= htmlspecialchars($row['username']); ?></td>

                            <td class="text-center text-nowrap">
                                <a href="edit_admin.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>

                                <?php if ($row['username'] !== $_SESSION['username']): ?>
                                    <a href="hapus_admin.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus Admin ini?')">
                                    <i class="fa fa-trash"></i> Hapus
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled><i class="fa fa-ban"></i> Hapus</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data Admin yang ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1):  ?>
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?= $page - 1; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo; Sebelumnya</span>
                            </a>
                        </li>

                        <?php 
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        for ($i = $start_page; $i <= $end_page; $i++): 
                        ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?= $page + 1; ?>" aria-label="Next">
                                <span aria-hidden="true">Berikutnya &raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <p class="text-center text-muted mt-2">
                Menampilkan data <?= $offset + 1; ?> - <?= min($offset + $limit, $total_records); ?> dari total **<?= $total_records; ?>** data. 
            </p>
        <?php endif; ?>

    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="js/sidebar.js"></script>
</body>
</html>