<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require "../config.php";

$id_proyek = $_GET['id_proyek'];
$id_dosen  = $_GET['id_dosen'];

pg_query($conn, "CALL sp_delete_proyek_dosen($id_proyek, $id_dosen)");

echo "<script>alert('Relasi berhasil dihapus!'); window.location='kelola_proyek_dosen.php';</script>";
exit;
?>
