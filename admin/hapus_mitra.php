<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$id = $_GET['id'];

$qDelete = "SELECT sp_delete_mitra($1)";
$res = pg_query_params($conn, $qDelete, [$id]);

header("Location: kelola_mitra.php?msg=deleted");
exit;
?>
