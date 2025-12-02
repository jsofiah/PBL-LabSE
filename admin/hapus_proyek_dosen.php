<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

$id_proyek = intval($_GET['id_proyek']);
$id_dosen  = intval($_GET['id_dosen']);

$query = "CALL sp_delete_proyek_dosen($1, $2)";
$params = array($id_proyek, $id_dosen);

pg_query_params($conn, $query, $params);

echo "<script>
        alert('Relasi proyek–dosen berhasil dihapus!');
        window.location='kelola_proyek.php';
      </script>";
exit;
?>
