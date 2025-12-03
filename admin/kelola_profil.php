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

    $qTotal = "SELECT COUNT(*) FROM dosen"; 
    $rTotal = pg_query($conn, $qTotal);
    $total_records = pg_fetch_result($rTotal, 0, 0);

    $total_pages = ceil($total_records / $limit);

    if ($total_pages === 0) {
        $page = 0;
        $offset = 0;
    } else {
        if ($page > $total_pages) {
            $page = $total_pages;
        }
        $offset = ($page - 1) * $limit;
    }

    $qViewDosen = "SELECT * FROM vw_detail_dosen 
                   ORDER BY id_dosen ASC 
                   LIMIT $limit OFFSET $offset";
    $rViewDosen = pg_query($conn, $qViewDosen);
    
    if (!$rViewDosen) {
        die("Query error: " . pg_last_error($conn));
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Profil Dosen</title>
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
            <h2 class="mb-2">Kelola Dosen</h2>
            <a href="tambah_dosen.php" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Dosen</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <colgroup>
                    <col style="width:40px;">
                    <col style="width:250px;">
                    <col style="width:170px;">
                    <col style="width:190px;">
                    <col style="width:250px;">
                    <col style="width:200px;">
                </colgroup>
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Nama Dosen</th>
                        <th class="text-center">Foto Dosen</th>
                        <th class="text-center">Jabatan</th>
                        <th class="text-center">Email Dosen</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php 
                    if (pg_num_rows($rViewDosen) > 0) :
                        $no = $offset + 1; 
                        while($dosen = pg_fetch_assoc($rViewDosen)) : 
                ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>

                        <td class="text-center"><?= htmlspecialchars($dosen['nama_dosen']); ?></td>

                        <td class="text-center">
                            <img src="../<?= htmlspecialchars($dosen['url_foto_dosen']); ?>" 
                                alt="Foto <?= htmlspecialchars($dosen['nama_dosen']); ?>" 
                                style="width:80px; height:auto; border-radius:5px; object-fit: cover;">
                        </td>

                        <td class="text-center"><?= htmlspecialchars($dosen['jabatan_lab']); ?></td>

                        <td class="text-center">
                            <a href="mailto:<?= htmlspecialchars($dosen['email_dosen']); ?>" class="text-primary">
                                <?= htmlspecialchars($dosen['email_dosen']); ?>
                            </a>
                        </td>

                        <td class="text-center">
                            <a href="edit_dosen.php?id=<?= $dosen['id_dosen']; ?>" 
                            class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            <a href="hapus_dosen.php?id=<?= $dosen['id_dosen']; ?>"
                            onclick="return confirm('Yakin ingin menghapus Dosen ini?')"
                            class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php 
                        endwhile; 
                    else: 
                ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data dosen yang ditemukan.</td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>
        </div>

        <?php if ($total_pages > 1): ?>
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
    <script src="js/sidebar.js"></script>
</body>
</html>