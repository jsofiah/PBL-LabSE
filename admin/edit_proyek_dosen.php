<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require "../config.php";

// ID lama (primary key komposit)
$id_proyek_old = intval($_GET['id_proyek']);
$id_dosen_old  = intval($_GET['id_dosen']);

// Ambil data untuk dropdown
$rProyek = pg_query($conn, "SELECT id_proyek, judul_proyek FROM proyek ORDER BY id_proyek");
$rDosen  = pg_query($conn, "SELECT id_dosen, nama_dosen FROM dosen ORDER BY id_dosen");

// Ambil data lama
$qGet = "
    SELECT * FROM proyek_dosen 
    WHERE id_proyek = $id_proyek_old AND id_dosen = $id_dosen_old
";
$rGet = pg_query($conn, $qGet);
$data = pg_fetch_assoc($rGet);

if (!$data) {
    echo "<script>alert('Data relasi tidak ditemukan!'); window.location='kelola_proyek_dosen.php';</script>";
    exit;
}

// Jika update disubmit
if (isset($_POST['submit'])) {

    $id_proyek = $_POST['id_proyek'];
    $id_dosen  = $_POST['id_dosen'];

    // cek relasi
    $cek = pg_query_params($conn,
        "SELECT 1 FROM proyek_dosen WHERE id_proyek = $1 AND id_dosen = $2",
        array($id_proyek, $id_dosen)
    );

    if (pg_num_rows($cek) > 0) {
        echo "<script>
                alert('Relasi ini sudah ada! Tidak boleh duplikat.');
                window.location='tambah_proyek_dosen.php';
              </script>";
        exit;
    }

    // insert jika blm ada 
    $query = "CALL sp_create_proyek_dosen($1, $2)";
    $params = array($id_proyek, $id_dosen);

    $res = pg_query_params($conn, $query, $params);

    if ($res) {
        echo "<script>
                alert('Relasi proyek–dosen berhasil ditambahkan!');
                window.location='kelola_proyek_dosen.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('Gagal menambahkan relasi!');</script>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Proyek–Dosen</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="css/styleForm.css" rel="stylesheet">

</head>

<body class="p-4">
<div class="content-area container">

    <h1 class="fw-bold text-center mb-4">Edit Relasi Proyek – Dosen</h1>

    <div class="card p-4 shadow-sm form-card">

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label text-white">Pilih Proyek</label>
                <select name="id_proyek" class="form-control" required>
                    <?php while ($p = pg_fetch_assoc($rProyek)) : ?>
                        <option value="<?= $p['id_proyek']; ?>"
                            <?= ($p['id_proyek'] == $data['id_proyek']) ? 'selected' : '' ?>>
                            <?= $p['id_proyek']; ?> - <?= htmlspecialchars($p['judul_proyek']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Pilih Dosen</label>
                <select name="id_dosen" class="form-control" required>
                    <?php while ($d = pg_fetch_assoc($rDosen)) : ?>
                        <option value="<?= $d['id_dosen']; ?>"
                            <?= ($d['id_dosen'] == $data['id_dosen']) ? 'selected' : '' ?>>
                            <?= $d['id_dosen']; ?> - <?= htmlspecialchars($d['nama_dosen']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button name="update" class="btn btn-warning">Update</button>
                <a href="kelola_proyek.php" class="btn btn-secondary">Kembali</a>
            </div>

        </form>
    </div>

</div>
</body>
</html>
