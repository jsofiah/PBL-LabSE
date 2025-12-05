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

    $qTotal = "SELECT COUNT(*) FROM vw_riwayat_pendidikan"; 
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

    $qViewPendidikanDosen = "
        SELECT 
            rp.*, 
            d.nama_dosen
        FROM vw_riwayat_pendidikan rp
        JOIN dosen d ON d.id_dosen = rp.id_dosen
        ORDER BY rp.id_pendidikan
        LIMIT $limit OFFSET $offset;
        ";

    $rViewPendidikanDosen = pg_query($conn, $qViewPendidikanDosen);
    
    if (!$rViewPendidikanDosen) {
        die("Query error: " . pg_last_error($conn));
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pendidikan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/stylePaging.css">
    <link rel="stylesheet" href="css/styleTabel.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content-area container-fluid px-3">
        <div class="mb-4">
            <h2 class="mb-2">Kelola Riwayat Pendidikan Dosen</h2>
            <a href="tambah_pendidikan_dosen.php" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Riwayat Pendidikan</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <colgroup>
                    <col style="width:40px;">
                    <col style="width:290px;">
                    <col style="width:290px;">
                    <col style="width:100px;">
                    <col style="width:150px;">
                    <col style="width:150px;">
                    <col style="width:150px;">
                    <col style="width:200px;">
                </colgroup>
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Nama Dosen</th>
                        <th class="text-center">Universitas</th>
                        <th class="text-center">Jenjang</th>
                        <th class="text-center">Bidang Studi</th>
                        <th class="text-center">Gelar</th>
                        <th class="text-center">Tahun Lulus</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php 
                    if (pg_num_rows($rViewPendidikanDosen) > 0) :
                        $no = $offset + 1; 
                        while($dosen = pg_fetch_assoc($rViewPendidikanDosen)) : 
                ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= htmlspecialchars($dosen['nama_dosen']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($dosen['universitas']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($dosen['jenjang']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($dosen['bidang_studi']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($dosen['gelar']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($dosen['tahun_lulus']); ?></td>

                        <td class="text-center">
                            <a href="edit_pendidikan_dosen.php?id=<?= $dosen['id_pendidikan']; ?>" 
                            class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            <a href="hapus_pendidikan_dosen.php?id=<?= $dosen['id_pendidikan']; ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin ingin menghapus Riwayat Pendidikan ini?')">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php 
                        endwhile; 
                    else: 
                ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data riwayat pendidikan dosen yang ditemukan.</td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>
        </div>
        <?php include 'paging.php'; ?>
    </div>

    <script src="js/sidebar.js"></script>
</body>
</html>