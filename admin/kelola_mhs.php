<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
    require_once '../config.php';

    // Query menggabungkan tabel master (untuk ambil NIM/Email) dengan View (untuk ambil list keahlian/proyek)
    $qMhs = "SELECT 
                m.id_mhs, 
                m.nim_mhs, 
                m.email_mhs, 
                v.nama_mhs, 
                v.prodi_mhs, 
                v.angkatan_mhs, 
                v.status, 
                v.daftar_keahlian, 
                v.daftar_proyek 
            FROM mhs_segeeks m
            LEFT JOIN vw_mhs_full v ON m.id_mhs = v.id_mhs
            ORDER BY m.nama_mhs ASC";
            
    $rMhs = pg_query($conn, $qMhs);
    $mahasiswa = [];
    while ($row = pg_fetch_assoc($rMhs)) {
        $mahasiswa[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Mahasiswa - SE Geeks</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="content-area container-fluid px-4">
        <div class="mb-4">
            <h2 class="mb-3">Kelola Data Mahasiswa</h2>
            <a href="tambah_mhs.php" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah
                Mahasiswa</a>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center" style="width: 5%;">No</th>
                                <th class="text-center" style="width: 15%;">NIM</th>
                                <th style="width: 25%;">Nama Mahasiswa</th>
                                <th style="width: 20%;">Prodi</th>
                                <th class="text-center" style="width: 10%;">Status</th>
                                <th style="width: 25%;">Keahlian</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (count($mahasiswa) > 0): ?>
                            <?php $no = 1; foreach($mahasiswa as $mhs) : ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>

                                <td class="text-center"><?= htmlspecialchars($mhs['nim_mhs']); ?></td>

                                <td class="text-truncate" style="max-width: 200px;"
                                    title="<?= htmlspecialchars($mhs['nama_mhs']); ?>">
                                    <?= htmlspecialchars($mhs['nama_mhs']); ?>
                                </td>

                                <td><?= htmlspecialchars($mhs['prodi_mhs']); ?></td>

                                <td class="text-center">
                                    <?php if($mhs['status'] == 't'): ?>
                                    Aktif
                                    <?php else: ?>
                                    <span class="text-danger">Non-Aktif</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-truncate" style="max-width: 200px;">
                                    <?php 
                            if (!empty($mhs['daftar_keahlian'])) {
                                echo htmlspecialchars($mhs['daftar_keahlian']);
                            } else {
                                echo '-';
                            }
                            ?>
                                </td>

                                <td class="text-center align-top text-nowrap">
                                    <div class="d-flex justify-content-center gap-1">

                                        <a href="edit_mhs.php?id=<?= $mhs['id_mhs']; ?>"
                                            class="btn btn-warning btn-sm"
                                            title="Edit">
                                            <i class="fa-solid fa-pen-to-square me-2"></i>Edit
                                        </a>

                                        <a href="hapus_mhs.php?id=<?= $mhs['id_mhs']; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')" title="Hapus">
                                            <i class="fa-solid fa-trash me-2"></i>Hapus
                                        </a>

                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data mahasiswa.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="js/sidebar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>