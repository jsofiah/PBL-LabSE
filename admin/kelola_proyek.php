<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

/* Ambil semua proyek */
$qProyek = "SELECT * FROM proyek ORDER BY id_proyek ASC";
$rProyek = pg_query($conn, $qProyek);

/* Ambil relasi proyek–mahasiswa */
$qPM = "
    SELECT pm.id_proyek,
           p.judul_proyek,
           pm.id_mhs,
           m.nama_mhs
    FROM proyek_mhs pm
    JOIN proyek p ON pm.id_proyek = p.id_proyek
    JOIN mhs_segeeks m ON pm.id_mhs = m.id_mhs
    ORDER BY pm.id_proyek ASC
";
$rPM = pg_query($conn, $qPM);

/* Ambil relasi proyek–dosen */
$qPD = "
    SELECT pd.id_proyek,
           p.judul_proyek,
           pd.id_dosen,
           d.nama_dosen
    FROM proyek_dosen pd
    JOIN proyek p ON pd.id_proyek = p.id_proyek
    JOIN dosen d ON pd.id_dosen = d.id_dosen
    ORDER BY pd.id_proyek ASC
";
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

    <!-- ================= PROYEK ================= -->
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
                    <th class="text-center">ID</th>
                    <th class="text-center">Gambar</th>
                    <th class="text-center">Judul</th>
                    <th class="text-center">Deskripsi</th>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Penulis</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

        <tbody>
        <?php while($pm = pg_fetch_assoc($rProyek)): ?>
            <tr>
                <!-- ID -->
                <td class="text-center"><?= $pm['id_proyek'] ?></td>

                <!-- Gambar -->
                <td class="text-center" >
                    <img src="../<?= htmlspecialchars($pm['url_gambar_proyek1']) ?>" width="80">
                </td>

                <!-- Judul -->
                <td class="text-center" ><?= htmlspecialchars($pm['judul_proyek']) ?></td>

                <!-- Deskripsi -->
                <td class="text-center" ><?= htmlspecialchars(substr($pm['isi_proyek'],0,100)) ?>...</td>

                <!-- Tanggal -->
                <td class="text-center" ><?= $pm['tanggal_terbit_proyek'] ?></td>

                <!-- Penulis -->
                <td class="text-center" ><?= $pm['penulis_proyek'] ?></td>

                <!-- Aksi -->
                <td class="text-center" >
                    <a href="edit_proyek.php?id=<?= $pm['id_proyek'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="hapus_proyek.php?id=<?= $pm['id_proyek'] ?>" 
                       onclick="return confirm('Yakin ingin menghapus?')"
                       class="btn btn-danger btn-sm">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>

    <hr class="my-5">

    <!-- ================= PROYEK - MAHASISWA ================= -->
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
                    <th class="text-center">ID</th>
                    <th class="text-center">ID Mahasiswa</th>
                    <th class="text-center">Judul Proyek</th>
                    <th class="text-center">Nama Mahasiswa</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
                </tr>
            </thead>
        <tbody>
        <?php while($pm = pg_fetch_assoc($rPM)): ?>
            <tr>
                <!-- ID Proyek Mahasiswa -->
                <td class="text-center" ><?= $pm['id_proyek'] ?></td>

                <!-- ID Mahasiswa -->
                <td class="text-center" ><?= $pm['id_mhs'] ?></td>

                <!-- Judul -->
                <td class="text-center" ><?= $pm['judul_proyek'] ?></td>

                <!-- Nama Mahasiswa -->
                <td class="text-center" ><?= $pm['nama_mhs'] ?></td>

                <!-- Aksi -->
                <td class="text-center" >
                    <a href="edit_proyek_mhs.php?id=<?= $pm['id_proyek'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="hapus_proyek_mhs.php?id=<?= $pm['id_proyek'] ?>" 
                       onclick="return confirm('Yakin ingin menghapus?')"
                       class="btn btn-danger btn-sm">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>

    <hr class="my-5">

    <!-- ================= PROYEK - DOSEN ================= -->
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
                    <th class="text-center">ID</th>
                    <th class="text-center">ID Dosen</th>
                    <th class="text-center">Judul Proyek</th>
                    <th class="text-center">Nama Dosen</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
                </tr>
            </thead>
        <tbody>

        <?php while($pd = pg_fetch_assoc($rPD)): ?>
            <tr>
                <!-- ID Proyek Dosen -->
                <td class="text-center" ><?= $pd['id_proyek'] ?></td>

                <!-- ID Dosen -->
                <td class="text-center" ><?= $pd['id_dosen'] ?></td>

                <!-- Judul Proyek -->
                <td class="text-center" ><?= $pd['judul_proyek'] ?></td>

                <!-- Nama Dosen -->
                <td class="text-center" ><?= $pd['nama_dosen'] ?></td>

                <!-- Aksi -->
                <td class="text-center" >
                    <a href="edit_proyek_dosen.php?id=<?= $pd['id_proyek'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="hapus_proyek_dosen.php?id=<?= $pd['id_proyek'] ?>" 
                       onclick="return confirm('Yakin ingin menghapus?')"
                       class="btn btn-danger btn-sm">Hapus</a>
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
