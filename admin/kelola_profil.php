<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    $qViewDosen = "SELECT * FROM vw_detail_dosen ORDER BY id_dosen";
    $rViewDosen = pg_query($conn, $qViewDosen);
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
                <?php $no = 1; ?>
                <?php while($dosen = pg_fetch_assoc($rViewDosen)) : ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>

                        <td class="text-center"><?= htmlspecialchars($dosen['nama_dosen']); ?></td>

                        <td class="text-center">
                            <img src="../<?= htmlspecialchars($dosen['url_foto_dosen']); ?>" 
                                alt="Foto <?= htmlspecialchars($dosen['nama_dosen']); ?>" 
                                style="width:80px; height:auto; border-radius:5px;">
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
                <?php endwhile; ?>
            </tbody>

            </table>
        </div>
    </div>

    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>