<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require '../config.php';

$id = $_GET['id'];

$qDelete = "DELETE FROM fasilitas WHERE id_fasilitas = $1";
$result = pg_query_params($conn, $qDelete, [$id]);

header("Location: kelola_fasilitas.php");
exit;
