<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "../config.php";

$id = $_GET['id'] ?? 0;

$q = "SELECT * FROM jenis_publikasi WHERE id_jenispublikasi = $id";
$r = pg_query($conn, $q);
$data = pg_fetch_assoc($r);

if (!$data) {
    echo "<script>
            alert('Data tidak ditemukan!');
            window.location.href='kelola_jenispublikasi.php';
          </script>";
    exit;
}

$deleteQuery = "CALL sp_delete_jenispublikasi($id)";
pg_query($conn, $deleteQuery);

echo "<script>
        alert('Jenis publikasi berhasil dihapus!');
        window.location.href = 'kelola_jenispublikasi.php';
      </script>";
exit;
?>
