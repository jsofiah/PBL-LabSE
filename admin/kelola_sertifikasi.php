<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    $limit = 20;

    $qTotalSertifikasi = "SELECT COUNT(*) FROM vw_sertifikasi";
    $rTotalSertifikasi = pg_query($conn, $qTotalSertifikasi);
    $total_records_sertifikasi = pg_fetch_result($rTotalSertifikasi, 0, 0);

    $total_pages_sertifikasi = ceil($total_records_sertifikasi / $limit);

    $page_sertifikasi = isset($_GET['page_sertifikasi']) && is_numeric($_GET['page_sertifikasi']) ? (int)$_GET['page_sertifikasi'] : 1;
    if ($page_sertifikasi < 1) $page_sertifikasi = 1;
    if ($page_sertifikasi > $total_pages_sertifikasi && $total_pages_sertifikasi > 0) $page_sertifikasi = $total_pages_sertifikasi;
    if ($total_records_sertifikasi == 0) $page_sertifikasi = 0;

    $offset_sertifikasi = ($page_sertifikasi > 0) ? ($page_sertifikasi - 1) * $limit : 0;

    $qViewSertifikasi = "SELECT * FROM vw_sertifikasi ORDER BY id_sertifikasi LIMIT $limit OFFSET $offset_sertifikasi";
    $rViewSertifikasi = pg_query($conn, $qViewSertifikasi);

    $qTotalDosen = "SELECT COUNT(*) FROM vw_sertifikasi_dosen";
    $rTotalDosen = pg_query($conn, $qTotalDosen);
    $total_records_dosen = pg_fetch_result($rTotalDosen, 0, 0);

    $total_pages_dosen = ceil($total_records_dosen / $limit);

    $page_dosen = isset($_GET['page_dosen']) && is_numeric($_GET['page_dosen']) ? (int)$_GET['page_dosen'] : 1;
    if ($page_dosen < 1) $page_dosen = 1;
    if ($page_dosen > $total_pages_dosen && $total_pages_dosen > 0) $page_dosen = $total_pages_dosen;
    if ($total_records_dosen == 0) $page_dosen = 0;
    
    $offset_dosen = ($page_dosen > 0) ? ($page_dosen - 1) * $limit : 0;

    $qViewSertifikasiDosen = "SELECT * FROM vw_sertifikasi_dosen ORDER BY id_sertifikasi LIMIT $limit OFFSET $offset_dosen";
    $rViewSertifikasiDosen = pg_query($conn, $qViewSertifikasiDosen);

    $current_page_sertifikasi = isset($_GET['page_sertifikasi']) ? (int)$_GET['page_sertifikasi'] : 1;
    $current_page_dosen = isset($_GET['page_dosen']) ? (int)$_GET['page_dosen'] : 1;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Sertifikasi</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
    <style>
        .action-btns {
            white-space: nowrap; 
            display: flex;
            gap: 5px;
            justify-content: center;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content-area container-fluid px-3">
        <div class="mb-4">
            <h2 class="mb-2">Kelola Sertifikasi</h2>
            <a href="tambah_sertifikasi.php" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Sertifikasi</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <colgroup>
                    <col style="width:50px;">
                    <col style="width:290px;">
                    <col style="width:290px;">
                    <col style="width:100px;">
                    <col style="width:200px;">
                </colgroup>
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Nama Sertifikasi</th>
                        <th class="text-center">Penyelenggara</th>
                        <th class="text-center">Tahun Sertifikasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (pg_num_rows($rViewSertifikasi) > 0) : ?>
                <?php $no = $offset_sertifikasi + 1; ?>
                <?php while($sertifikasi = pg_fetch_assoc($rViewSertifikasi)) : ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= htmlspecialchars($sertifikasi['nama_sertifikasi']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($sertifikasi['penyelenggara']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($sertifikasi['tahun_sertifikasi']); ?></td>
                        <td class="text-center action-btns">
                            <a href="edit_sertifikasi.php?id=<?= $sertifikasi['id_sertifikasi']; ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="hapus_sertifikasi.php?id=<?= $sertifikasi['id_sertifikasi']; ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin ingin menghapus Sertifikasi ini?')">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data Sertifikasi yang ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            </table>
        </div>
        <?php 
            $page = $page_sertifikasi;
            $total_pages = $total_pages_sertifikasi;
            $offset = $offset_sertifikasi;
            $total_records = $total_records_sertifikasi;
            $param_name = 'page_sertifikasi';
            $other_param = 'page_dosen=' . $page_dosen;
            $label = 'Sertifikasi';

            include 'paging.php'; 
        ?>

        <div class="mt-5 mb-4">
            <h2 class="mb-2">Kelola Sertifikasi Dosen</h2>
            <a href="tambah_sertifikasi_dosen.php" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Sertifikasi Dosen</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <colgroup>
                    <col style="width:50px;">
                    <col style="width:290px;">
                    <col style="width:290px;">
                    <col style="width:290px;">
                    <col style="width:100px;">
                    <col style="width:200px;">
                </colgroup>
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Nama Dosen</th>
                        <th class="text-center">Nama Sertifikasi</th>
                        <th class="text-center">Penyelenggara</th>
                        <th class="text-center">Tahun Sertifikasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (pg_num_rows($rViewSertifikasiDosen) > 0) : ?>
                <?php $no = $offset_dosen + 1; ?>
                <?php while($dosen = pg_fetch_assoc($rViewSertifikasiDosen)) : ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= htmlspecialchars($dosen['nama_dosen']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($dosen['nama_sertifikasi']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($dosen['penyelenggara']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($dosen['tahun_sertifikasi']); ?></td>

                        <td class="text-center action-btns">
                            <a href="edit_sertifikasi_dosen.php?id=<?= $dosen['id_sertifikasi']; ?>" 
                            class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            <a href="hapus_sertifikasi_dosen.php?id_dosen=<?= $dosen['id_dosen']; ?>&id_sertifikasi=<?= $dosen['id_sertifikasi']; ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin ingin menghapus Sertifikasi Dosen ini?')">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data Sertifikasi Dosen yang ditemukan.</td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>
        </div>
        <?php 
            $page = $page_dosen;
            $total_pages = $total_pages_dosen;
            $offset = $offset_dosen;
            $total_records = $total_records_dosen;
            $param_name = 'page_dosen';
            $other_param = 'page_sertifikasi=' . $page_sertifikasi;
            $label = 'Sertifikasi Dosen';

            include 'paging.php'; 
        ?>
    </div>
    <script src="js/sidebar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>