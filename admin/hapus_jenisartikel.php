<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once "../config.php";

$id = $_GET['id'] ?? 0;

$sql = "CALL sp_delete_jenisartikel($id)";
pg_query($conn, $sql);

echo "<script>
        alert('Jenis Artikel berhasil dihapus!');
        window.location.href='kelola_jenisartikel.php';
      </script>";
exit;
?>
