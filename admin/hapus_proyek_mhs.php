<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

$id_proyek = intval($_GET['id_proyek']);
$id_mhs    = intval($_GET['id_mhs']);

$query = "CALL sp_delete_proyek_mhs($1, $2)";
$params = array($id_proyek, $id_mhs);

pg_query_params($conn, $query, $params);

echo "<script>
        alert('Relasi proyek–mahasiswa berhasil dihapus!');
        window.location='kelola_proyek.php';
      </script>";
exit;
?>
