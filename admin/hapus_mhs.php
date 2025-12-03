<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once '../config.php';

if (isset($_GET['id'])) {
    $id_mhs = intval($_GET['id']);
    
    $query = "CALL sp_delete_mhs($1)";
    $result = pg_query_params($conn, $query, array($id_mhs));
    
    if ($result) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='kelola_mhs.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus.'); window.location='kelola_mhs.php';</script>";
    }
} else {
    header("Location: kelola_mhs.php");
}
?>