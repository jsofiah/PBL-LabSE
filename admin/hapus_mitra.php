<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$id = $_GET['id'];

$qDelete = "CALL sp_delete_mitra($id)";
$res = pg_query($conn, $qDelete);

header("Location: kelola_mitra.php?msg=deleted");
exit;
?>
