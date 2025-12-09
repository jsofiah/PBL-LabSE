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

    $where_conditions = [];
    $params = [];
    $param_count = 1;

    if ($search !== '') {
        $where_conditions[] = "(LOWER(v.nama_mhs) LIKE $" . $param_count . " OR LOWER(m.nim_mhs) LIKE $" . $param_count . ")";
        $params[] = "%" . strtolower($search) . "%";
        $param_count++;
    }

    if ($prodi !== '') {
        $where_conditions[] = "v.prodi_mhs = $" . $param_count;
        $params[] = $prodi;
        $param_count++;
    }

    if ($angkatan !== '') {
        $where_conditions[] = "v.angkatan_mhs = $" . $param_count;
        $params[] = $angkatan;
        $param_count++;
    }

    if ($status !== '') {
        $where_conditions[] = "v.status = $" . $param_count;
        $params[] = $status;
        $param_count++;
    }

    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

    $qTotal = "SELECT COUNT(*) FROM mhs_segeeks m 
                LEFT JOIN vw_mhs_full v ON m.id_mhs = v.id_mhs
                $where_clause";

    $rTotal = pg_query_params($conn, $qTotal, $params);

    if (!$rTotal) {
        die("Query hitung total data gagal: " . pg_last_error($conn));
    }

    $total_records = pg_fetch_result($rTotal, 0, 0);
    $total_pages = ceil($total_records / $limit);

    if ($total_records === 0) {
        $offset = 0;
        $page = 1;
    } else {
        if ($page > $total_pages) {
            $page = $total_pages;
        }
        $offset = ($page - 1) * $limit;
    }

    if ($total_records > 0) {
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
                $where_clause
                ORDER BY m.nim_mhs ASC
                LIMIT $limit OFFSET $offset";

        $rMhs = pg_query_params($conn, $qMhs, $params);
        
        if (!$rMhs) {
            die("Query error: " . pg_last_error($conn));
        }
    } else {
        $rMhs = false;
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleSidebar.css">
    <link rel="stylesheet" href="css/styleTabel.css">
    <link rel="stylesheet" href="css/stylePaging.css">
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="content-area container-fluid px-3">
        <div class="mb-4">
            <h2 class="mb-2">Kelola Data Mahasiswa</h2>
            <a href="tambah_mhs.php" class="btn btn-success">
                <i class="fa fa-plus"></i> Tambah Mahasiswa
            </a>
        </div>

        <div class="search-filter-section mb-3">
            <form method="GET" action="" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-search"></i> Pencarian
                        </label>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" 
                                   name="search" 
                                   value="<?= htmlspecialchars($search) ?>" 
                                   class="form-control"
                                   placeholder="Cari nama atau NIM">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-graduation-cap"></i> Prodi
                        </label>
                        <select name="prodi" class="form-select filter-select">
                            <option value="">Semua Prodi</option>
                            <option value="TI" <?= $prodi=='TI'?'selected':'' ?>>TI</option>
                            <option value="SIB" <?= $prodi=='SIB'?'selected':'' ?>>SIB</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calendar"></i> Angkatan
                        </label>
                        <select name="angkatan" class="form-select filter-select">
                            <option value="">Semua Angkatan</option>
                            <?php for ($th=2020; $th<=2025; $th++): ?>
                            <option value="<?= $th ?>" <?= $angkatan==$th?'selected':'' ?>><?= $th ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-filter"></i> Status
                        </label>
                        <select name="status" class="form-select filter-select">
                            <option value="">Semua Status</option>
                            <option value="t" <?= $status=='t'?'selected':'' ?>>Aktif</option>
                            <option value="f" <?= $status=='f'?'selected':'' ?>>Non-Aktif</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Cari
                            </button>
                            <a href="?" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>

                <?php if (!empty($search) || !empty($prodi) || !empty($angkatan) || !empty($status)): ?>
                <div class="active-filters mt-2">
                    <span class="fw-semibold">Filter Aktif:</span>
                    <?php if (!empty($search)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-search"></i>
                            "<?= htmlspecialchars($search) ?>"
                            <span class="remove-filter" onclick="removeFilter('search')">×</span>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($prodi)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-graduation-cap"></i>
                            <?= htmlspecialchars($prodi) ?>
                            <span class="remove-filter" onclick="removeFilter('prodi')">×</span>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($angkatan)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-calendar"></i>
                            <?= htmlspecialchars($angkatan) ?>
                            <span class="remove-filter" onclick="removeFilter('angkatan')">×</span>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($status)): ?>
                        <span class="filter-badge">
                            <i class="fas fa-filter"></i>
                            <?= $status == 't' ? 'Aktif' : 'Non-Aktif' ?>
                            <span class="remove-filter" onclick="removeFilter('status')">×</span>
                        </span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table modern-table">
                    <colgroup>
                        <col style="width:50px;">
                        <col style="width:120px;">
                        <col style="width:200px;">
                        <col style="width:120px;">
                        <col style="width:100px;">
                        <col style="width:250px;">
                        <col style="width:200px;">
                    </colgroup>

                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">NIM</th>
                            <th class="text-center">Nama Mahasiswa</th>
                            <th class="text-center">Prodi</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Keahlian</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($rMhs && pg_num_rows($rMhs) > 0): 
                            $no = $offset + 1;
                            while($mhs = pg_fetch_assoc($rMhs)):
                        ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td class="text-center"><?= htmlspecialchars($mhs['nim_mhs']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($mhs['nama_mhs']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($mhs['prodi_mhs']); ?></td>
                                <td class="text-center">
                                    <?= $mhs['status'] == 't' ? 'Aktif' : '<span class="text-danger">Non-Aktif</span>' ?>
                                </td>
                                <td class="text-center">
                                    <?= !empty($mhs['daftar_keahlian']) ? htmlspecialchars($mhs['daftar_keahlian']) : '-' ?>
                                </td>
                                <td>
                                    <div class="action-buttons text-center">
                                        <a href="?aksi=edit&id=<?= $mhs['id_mhs']; ?>" 
                                           class="btn btn-action btn-warning btn-sm">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <a href="?aksi=hapus&id=<?= $mhs['id_mhs']; ?>" 
                                           class="btn btn-action btn-danger btn-sm"
                                           onclick="return confirm('Yakin ingin menghapus mahasiswa ini?')">
                                            <i class="fa fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h5>Tidak Ada Data</h5>
                                        <p class="text-muted">
                                            <?php if (!empty($search) || !empty($prodi) || !empty($angkatan) || !empty($status)): ?>
                                                Tidak ada data mahasiswa yang sesuai dengan filter yang dipilih.
                                            <?php else: ?>
                                                Tidak ada data mahasiswa yang ditemukan.
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

        <?php if ($total_records > 0): ?>
            <?php 
            $page = $page;
            $total_pages = $total_pages;
            $offset = $offset;
            $total_records = $total_records;
            $param_name = 'page';
            $other_param = http_build_query(['search' => $search, 'prodi' => $prodi, 'angkatan' => $angkatan, 'status' => $status]);
            $label = 'Mahasiswa';
            ?>
            <?php include 'paging.php'; ?>
        <?php endif; ?>
    </div>

    <script src="js/sidebar.js"></script>
    <script>
        function removeFilter(filterName) {
            const form = document.getElementById('filterForm');
            const input = form.querySelector(`[name="${filterName}"]`);
            if (input) {
                if (input.tagName === 'SELECT') {
                    input.value = '';
                } else {
                    input.value = '';
                }
                form.submit();
            }
        }
    </script>
</body>
</html>