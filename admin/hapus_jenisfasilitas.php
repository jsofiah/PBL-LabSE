<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$id = $_GET['id'];

$qDelete = "DELETE FROM jenis_fasilitas WHERE id_jenisfasilitas = $1";
$result = pg_query_params($conn, $qDelete, [$id]);

header("Location: kelola_jenisFasilitas.php");
exit;
?>
