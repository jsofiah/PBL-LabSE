<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "../config.php";

$id = $_GET['id'] ?? 0;

$q = "SELECT * FROM jenis_artikel WHERE id_jenisartikel = $id";
$r = pg_query($conn, $q);
$data = pg_fetch_assoc($r);

if (!$data) {
    echo "<script>
            alert('Data tidak ditemukan!');
            window.location.href='kelola_jenisartikel.php';
        </script>";
    exit;
}

$deleteQuery = "CALL sp_delete_jenisartikel($id)";
pg_query($conn, $deleteQuery);

echo "<script>
        alert('Jenis artikel berhasil dihapus!');
        window.location.href = 'kelola_jenisartikel.php';
      </script>";
exit;
?>
