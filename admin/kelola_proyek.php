<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

$limit = 20;

$page_proyek = isset($_GET['page_proyek']) && is_numeric($_GET['page_proyek']) ? (int)$_GET['page_proyek'] : 1;
if ($page_proyek < 1) $page_proyek = 1;

$qTotalProyek = "SELECT COUNT(*) FROM proyek";
$rTotalProyek = pg_query($conn, $qTotalProyek);
$total_records_proyek = $rTotalProyek ? pg_fetch_result($rTotalProyek, 0, 0) : 0;
$total_pages_proyek = ceil($total_records_proyek / $limit);

if ($total_records_proyek === 0) {
    $page_proyek = 0;
    $offset_proyek = 0;
} else {
    if ($page_proyek > $total_pages_proyek) $page_proyek = $total_pages_proyek;
    $offset_proyek = ($page_proyek - 1) * $limit;
}

$qProyek = "SELECT * FROM vw_proyek ORDER BY id_proyek DESC LIMIT $limit OFFSET $offset_proyek";
$rProyek = pg_query($conn, $qProyek);

$page_mhs = isset($_GET['page_mhs']) && is_numeric($_GET['page_mhs']) ? (int)$_GET['page_mhs'] : 1;
if ($page_mhs < 1) $page_mhs = 1;

$qTotalMhs = "SELECT COUNT(*) FROM proyek_mhs"; 
$rTotalMhs = pg_query($conn, $qTotalMhs);
$total_records_mhs = $rTotalMhs ? pg_fetch_result($rTotalMhs, 0, 0) : 0;
$total_pages_mhs = ceil($total_records_mhs / $limit);

if ($total_records_mhs === 0) {
    $page_mhs = 0;
    $offset_mhs = 0;
} else {
    if ($page_mhs > $total_pages_mhs) $page_mhs = $total_pages_mhs;
    $offset_mhs = ($page_mhs - 1) * $limit;
}

$qPM = "SELECT * FROM vw_proyek_mhs ORDER BY id_proyek ASC LIMIT $limit OFFSET $offset_mhs";
$rPM = pg_query($conn, $qPM);


$page_dosen = isset($_GET['page_dosen']) && is_numeric($_GET['page_dosen']) ? (int)$_GET['page_dosen'] : 1;
if ($page_dosen < 1) $page_dosen = 1;

$qTotalDosen = "SELECT COUNT(*) FROM proyek_dosen"; 
$rTotalDosen = pg_query($conn, $qTotalDosen);
$total_records_dosen = $rTotalDosen ? pg_fetch_result($rTotalDosen, 0, 0) : 0;
$total_pages_dosen = ceil($total_records_dosen / $limit);

if ($total_records_dosen === 0) {
    $page_dosen = 0;
    $offset_dosen = 0;
} else {
    if ($page_dosen > $total_pages_dosen) $page_dosen = $total_pages_dosen;
    $offset_dosen = ($page_dosen - 1) * $limit;
}

$qPD = "SELECT * FROM vw_proyek_dosen ORDER BY id_proyek ASC LIMIT $limit OFFSET $offset_dosen";
$rPD = pg_query($conn, $qPD);

if (!$rProyek || !$rPM || !$rPD) {
    die("Gagal mengambil data dari database.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Proyek</title>
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
        <h2 class="mb-2">Kelola Proyek</h2>
        <a href="tambah_proyek.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Proyek
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-fixed">
            <colgroup>
                <col style="width:40px;">
                <col style="width:150px;">
                <col style="width:200px;">
                <col style="width:300px;">
                <col style="width:140px;">
                <col style="width:180px;">
                <col style="width:180px;">
            </colgroup>

            <thead class="table-primary">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Gambar</th>
                    <th class="text-center">Judul</th>
                    <th class="text-center">Deskripsi</th>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Penulis</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php 
            if (pg_num_rows($rProyek) > 0):
                $no = $offset_proyek + 1;
                while($pm = pg_fetch_assoc($rProyek)): 
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>

                <td class="text-center">
                    <img src="../<?= htmlspecialchars($pm['url_gambar_proyek1']) ?>" width="80" alt="Gambar Proyek">
                </td>

                <td class="text-center"><?= htmlspecialchars($pm['judul_proyek']) ?></td>

                <td class="text-truncate" style="max-width: 300px;"><?= htmlspecialchars(substr($pm['isi_proyek'],0,100)) ?>...</td>

                <td class="text-center"><?= $pm['tanggal_terbit_proyek'] ?></td>

                <td class="text-center"><?= $pm['penulis_proyek'] ?></td>

                <td class="text-center text-nowrap">
                    <a href="edit_proyek.php?id=<?= $pm['id_proyek'] ?>" class="btn btn-warning btn-sm">
                        <i class="fa fa-edit"></i> Edit
                    </a>

                    <a href="hapus_proyek.php?id=<?= $pm['id_proyek'] ?>"
                    onclick="return confirm('Yakin ingin menghapus?')"
                    class="btn btn-danger btn-sm">
                    <i class="fa fa-trash"></i> Hapus
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="7" class="text-center">Tidak ada data Proyek Utama.</td>
            </tr>
            <?php endif; ?>
        </tbody>

        </table>
    </div> 
    <?php 
        $page = $page_proyek;
        $total_pages = $total_pages_proyek;
        $offset = $offset_proyek;
        $total_records = $total_records_proyek;
        $param_name = 'page_proyek';
        $other_param = 'page_mhs=' . $page_mhs . '&page_dosen=' . $page_dosen;
        $label = 'Proyek';

        include 'paging.php'; 
    ?>

    <hr class="my-5">

    <div class="mb-4">
        <h2>Kelola Proyek Mahasiswa</h2>
        <a href="tambah_proyek_mhs.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Proyek
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-fixed">
            <colgroup>
                <col style="width:40px;">
                <col style="width:100px;">
                <col style="width:200px;">
                <col style="width:300px;">
                <col style="width:140px;">
            </colgroup>

        <thead class="table-primary">
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">ID Mahasiswa</th>
                <th class="text-center">Judul Proyek</th>
                <th class="text-center">Nama Mahasiswa</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        if (pg_num_rows($rPM) > 0):
            $no = $offset_mhs + 1;
            while($pm = pg_fetch_assoc($rPM)): 
        ?>
        <tr>
            <td class="text-center"><?= $no++; ?></td>

            <td class="text-center"><?= $pm['id_mhs'] ?></td>

            <td class="text-center"><?= htmlspecialchars($pm['judul_proyek']) ?></td>

            <td class="text-center"><?= htmlspecialchars($pm['nama_mhs']) ?></td>

            <td class="text-center text-nowrap">
                <a href="edit_proyek_mhs.php?id_proyek=<?= $pm['id_proyek'] ?>&id_mhs=<?= $pm['id_mhs'] ?>"
                class="btn btn-warning btn-sm">
                    <i class="fa fa-edit"></i> Edit
                </a>

                <a href="hapus_proyek_mhs.php?id_proyek=<?= $pm['id_proyek'] ?>&id_mhs=<?= $pm['id_mhs'] ?>"
                onclick="return confirm('Yakin ingin menghapus?')"
                class="btn btn-danger btn-sm">
                    <i class="fa fa-trash"></i> Hapus
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php else: ?>
        <tr>
            <td colspan="5" class="text-center">Tidak ada data Proyek Mahasiswa.</td>
        </tr>
        <?php endif; ?>
        </tbody>

        </table>
    </div>
    <?php 
        $page = $page_mhs;
        $total_pages = $total_pages_mhs;
        $offset = $offset_mhs;
        $total_records = $total_records_mhs;
        $param_name = 'page_mhs';
        $other_param = 'page_proyek=' . $page_proyek . '&page_dosen=' . $page_dosen;
        $label = 'Proyek Mahasiswa';

        include 'paging.php'; 
    ?>

    <hr class="my-5">

    <div class="mb-4">
        <h2>Kelola Proyek Dosen</h2>
        <a href="tambah_proyek_dosen.php" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> Tambah Proyek
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-fixed">
            <colgroup>
                <col style="width:40px;">
                <col style="width:100px;">
                <col style="width:200px;">
                <col style="width:300px;">
                <col style="width:140px;">
            </colgroup>

        <thead class="table-primary">
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">ID Dosen</th>
                <th class="text-center">Judul Proyek</th>
                <th class="text-center">Nama Dosen</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        if (pg_num_rows($rPD) > 0):
            $no = $offset_dosen + 1;
            while($pd = pg_fetch_assoc($rPD)): 
        ?>
        <tr>
            <td class="text-center"><?= $no++; ?></td>

            <td class="text-center"><?= $pd['id_dosen'] ?></td>

            <td class="text-center"><?= htmlspecialchars($pd['judul_proyek']) ?></td>

            <td class="text-center"><?= htmlspecialchars($pd['nama_dosen']) ?></td>

            <td class="text-center text-nowrap">
                <a href="edit_proyek_dosen.php?id_proyek=<?= $pd['id_proyek'] ?>&id_dosen=<?= $pd['id_dosen'] ?>"
                class="btn btn-warning btn-sm">
                    <i class="fa fa-edit"></i> Edit
                </a>

                <a href="hapus_proyek_dosen.php?id_proyek=<?= $pd['id_proyek'] ?>&id_dosen=<?= $pd['id_dosen'] ?>"
                onclick="return confirm('Yakin ingin menghapus?')"
                class="btn btn-danger btn-sm">
                <i class="fa fa-trash"></i> Hapus
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php else: ?>
        <tr>
            <td colspan="5" class="text-center">Tidak ada data Proyek Dosen.</td>
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
        $other_param = 'page_proyek=' . $page_proyek . '&page_mhs=' . $page_mhs;
        $label = 'Proyek Dosen';

        include 'paging.php'; 
    ?>
</div>

    <script src="js/sidebar.js"></script>
</body>
</html>