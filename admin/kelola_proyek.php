<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

$qProyek = "SELECT * FROM vw_proyek ORDER BY id_proyek ASC";
$rProyek = pg_query($conn, $qProyek);

$qPM = "SELECT * FROM vw_proyek_mhs ORDER BY id_proyek ASC";
$rPM = pg_query($conn, $qPM);

$qPD = "SELECT * FROM vw_proyek_dosen ORDER BY id_proyek ASC";
$rPD = pg_query($conn, $qPD);
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
            <?php $no = 1; ?>
            <?php while($pm = pg_fetch_assoc($rProyek)): ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>

                <td class="text-center">
                    <img src="../<?= htmlspecialchars($pm['url_gambar_proyek1']) ?>" width="80">
                </td>

                <td class="text-center"><?= htmlspecialchars($pm['judul_proyek']) ?></td>

                <td class="text-center"><?= htmlspecialchars(substr($pm['isi_proyek'],0,100)) ?>...</td>

                <td class="text-center"><?= $pm['tanggal_terbit_proyek'] ?></td>

                <td class="text-center"><?= $pm['penulis_proyek'] ?></td>

                <td class="text-center">
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
        </tbody>

    </table>
    </div>

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
                </tr>
            </thead>
        <tbody>
        <?php $no = 1; ?>
        <?php while($pm = pg_fetch_assoc($rPM)): ?>
        <tr>
            <td class="text-center"><?= $no++; ?></td>

            <td class="text-center"><?= $pm['id_mhs'] ?></td>

            <td class="text-center"><?= $pm['judul_proyek'] ?></td>

            <td class="text-center"><?= $pm['nama_mhs'] ?></td>

            <td class="text-center">
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
    </tbody>

    </table>
    </div>

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
                </tr>
            </thead>
        <tbody>
        <?php $no = 1; ?>
        <?php while($pd = pg_fetch_assoc($rPD)): ?>
        <tr>
            <td class="text-center"><?= $no++; ?></td>

            <td class="text-center"><?= $pd['id_dosen'] ?></td>

            <td class="text-center"><?= htmlspecialchars($pd['judul_proyek']) ?></td>

            <td class="text-center"><?= htmlspecialchars($pd['nama_dosen']) ?></td>

            <td class="text-center">
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
    </tbody>

    </table>
    </div>
</div>

    <script src="js/sidebar.js"></script>
</body>
</html>
