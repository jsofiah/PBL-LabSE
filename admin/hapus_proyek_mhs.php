<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require "../config.php";

$id_proyek = $_GET['id_proyek'];
$id_mhs    = $_GET['id_mhs'];

$query = "CALL sp_delete_proyek_mhs($id_proyek, $id_mhs)";
pg_query($conn, $query);

echo "<script>alert('Proyek Mahasiswa berhasil dihapus!'); window.location='kelola_proyek.php';</script>";
exit;
?>
