<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    $qViewSertifikasi = "SELECT * FROM vw_sertifikasi ORDER BY id_sertifikasi";
    $rViewSertifikasi = pg_query($conn, $qViewSertifikasi);

    $qViewSertifikasiDosen = "SELECT * FROM vw_sertifikasi_dosen ORDER BY id_sertifikasi";
    $rViewSertifikasiDosen = pg_query($conn, $qViewSertifikasiDosen);
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
                    <col style="width:40px;">
                    <col style="width:290px;">
                    <col style="width:290px;">
                    <col style="width:100px;">
                    <col style="width:200px;">
                </colgroup>
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Nama Sertifikasi</th>
                        <th class="text-center">Penyelenggara</th>
                        <th class="text-center">Tahun Sertifikasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($sertifikasi = pg_fetch_assoc($rViewSertifikasi)) : ?>
                        <tr>
                            <td class="text-center"><?= $sertifikasi['id_sertifikasi']; ?></td>
                            <td class="text-center"><?= htmlspecialchars($sertifikasi['nama_sertifikasi']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($sertifikasi['penyelenggara']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($sertifikasi['tahun_sertifikasi']); ?></td>
                            <td class="text-center">
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
                </tbody>
            </table>
        </div>

        <div class="mt-5 mb-4">
            <h2 class="mb-2">Kelola Sertifikasi Dosen</h2>
            <a href="tambah_sertifikasi_dosen.php" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Sertifikasi Dosen</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-fixed">
                <colgroup>
                    <col style="width:100px;">
                    <col style="width:290px;">
                    <col style="width:290px;">
                    <col style="width:290px;">
                    <col style="width:100px;">
                    <col style="width:200px;">
                </colgroup>
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">ID Sertifikasi</th>
                        <th class="text-center">Nama Dosen</th>
                        <th class="text-center">Nama Sertifikasi</th>
                        <th class="text-center">Penyelenggara</th>
                        <th class="text-center">Tahun Sertifikasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($dosen = pg_fetch_assoc($rViewSertifikasiDosen)) : ?>
                        <tr>
                            <td class="text-center"><?= $dosen['id_sertifikasi']; ?></td>
                            <td class="text-center"><?= htmlspecialchars($dosen['nama_dosen']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($dosen['nama_sertifikasi']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($dosen['penyelenggara']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($dosen['tahun_sertifikasi']); ?></td>
                            <td class="text-center">
                                <a href="edit_sertifikasi_dosen.php?id=<?= $dosen['id_sertifikasi']; ?>" class="btn btn-warning btn-sm">
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
                </tbody>
            </table>
        </div>
    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>