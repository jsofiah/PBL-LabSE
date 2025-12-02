<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require '../config.php';

$id = $_GET['id'];

$q = pg_query_params($conn, "SELECT url_gambar_galeri FROM vw_galeri WHERE id_galeri=$1", array($id));
$data = pg_fetch_assoc($q);

$path = "../" . $data['url_gambar_galeri'];
if (file_exists($path)) {
    unlink($path);
}

$query = "CALL sp_delete_galeri($1)";
pg_query_params($conn, $query, array($id));

echo "<script>alert('Gambar berhasil dihapus!');
      window.location='kelola_galeri.php';</script>";
exit;
?>
