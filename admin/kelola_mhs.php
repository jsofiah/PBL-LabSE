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

    $search   = isset($_GET['search']) ? trim($_GET['search']) : '';
    $prodi    = isset($_GET['prodi']) ? trim($_GET['prodi']) : '';
    $angkatan = isset($_GET['angkatan']) ? trim($_GET['angkatan']) : '';
    $status   = isset($_GET['status']) ? trim($_GET['status']) : '';

    $where = "WHERE 1=1";

    if ($search !== '') {
        $safeSearch = pg_escape_string($conn, strtolower($search));
        $where .= " AND (LOWER(v.nama_mhs) LIKE '%$safeSearch%' OR LOWER(m.nim_mhs) LIKE '%$safeSearch%')";
    }

    if ($prodi !== '') {
        $safeProdi = pg_escape_string($conn, $prodi);
        $where .= " AND v.prodi_mhs = '$safeProdi'";
    }

    if ($angkatan !== '') {
        $safeAngkatan = pg_escape_string($conn, $angkatan);
        $where .= " AND v.angkatan_mhs = '$safeAngkatan'";
    }

    if ($status !== '') {
        $safeStatus = pg_escape_string($conn, $status);
        $where .= " AND v.status = '$safeStatus'";
    }

    $qTotal = "SELECT COUNT(*) FROM mhs_segeeks m 
                LEFT JOIN vw_mhs_full v ON m.id_mhs = v.id_mhs
                $where";

    $rTotal = pg_query($conn, $qTotal);

    if (!$rTotal) {
        die("Query hitung total data gagal: " . pg_last_error($conn));
    }

    $total_records = pg_fetch_result($rTotal, 0, 0);

   $total_pages = ceil($total_records / $limit);

if ($total_records === 0) {
    $page = 1;
    $offset = 0;
} else {
    if ($page > $total_pages) {
        $page = $total_pages;
    }
    if ($page < 1) {
        $page = 1;
    }

    $offset = ($page - 1) * $limit;

    if ($offset < 0) {
        $offset = 0;
    }
}


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
            $where
            ORDER BY m.nim_mhs ASC
            LIMIT $limit OFFSET $offset";

    $rMhs = pg_query($conn, $qMhs);
    
    if (!$rMhs) {
        die("Query error: " . pg_last_error($conn));
    }
    
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
    <title>Kelola Mahasiswa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
    <link rel="stylesheet" href="css/stylePaging.css">
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="content-area container-fluid px-4">
        <div class="mb-4">
            <h2 class="mb-3">Kelola Data Mahasiswa</h2>
            <a href="tambah_mhs.php" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah
                Mahasiswa</a>
        </div>
        <form method="GET" class="row g-2 mb-3">

            <div class="col-md-4">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control"
                    placeholder="Cari nama atau NIM">
            </div>

            <div class="col-md-2">
                <select name="prodi" class="form-control">
                    <option value="">Semua Prodi</option>
                    <option value="TI" <?= $prodi=='TI'?'selected':'' ?>>TI</option>
                    <option value="SIB" <?= $prodi=='SIB'?'selected':'' ?>>SIB</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="angkatan" class="form-control">
                    <option value="">Semua Angkatan</option>
                    <?php for ($th=2020; $th<=2025; $th++): ?>
                    <option value="<?= $th ?>" <?= $angkatan==$th?'selected':'' ?>><?= $th ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="t" <?= $status=='t'?'selected':'' ?>>Aktif</option>
                    <option value="f" <?= $status=='f'?'selected':'' ?>>Non-Aktif</option>
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100"><i class="fa fa-search"></i> Filter</button>
            </div>

        </form>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                <div class="table-container">
                    <table class="table modern-table table-fixed">
                        <thead>
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
                            <?php if ($total_records > 0): 
                        $no = $offset + 1;
                        foreach ($mahasiswa as $mhs):
                    ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td class="text-center"><?= htmlspecialchars($mhs['nim_mhs']); ?></td>

                                <td class="text-truncate-custom" title="<?= htmlspecialchars($mhs['nama_mhs']); ?>">
                                    <?= htmlspecialchars($mhs['nama_mhs']); ?>
                                </td>

                                <td><?= htmlspecialchars($mhs['prodi_mhs']); ?></td>

                                <td class="text-center">
                                    <?= $mhs['status'] == 't' ? 'Aktif' : '<span class="text-danger">Non-Aktif</span>' ?>
                                </td>

                                <td class="text-truncate-custom">
                                    <?= !empty($mhs['daftar_keahlian']) ? htmlspecialchars($mhs['daftar_keahlian']) : '-' ?>
                                </td>

                                <td>
                                    <div style="display: flex; justify-content: center; gap: 6px; width: 100%;">
                                        <a href="?aksi=edit&id=<?= $row['id_status']; ?>"
                                            class="btn btn-sm btn-warning">Edit</a>
                                        <a href="?aksi=hapus&id=<?= $row['id_status']; ?>"
                                            class="btn btn-sm btn-danger">Hapus</a>
                                    </div>
                                </td>

                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h5>Tidak Ada Data</h5>

                                        <p class="text-muted">
                                            <?php if (!empty($search) || !empty($filter_status) || !empty($filter_prodi)): ?>
                                            Tidak ada data mahasiswa yang sesuai filter.
                                            <?php else: ?>
                                            Belum ada data mahasiswa tersimpan.
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php include 'paging.php'; ?>
    </div>
    <script src="js/sidebar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>