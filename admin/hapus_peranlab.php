<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$id = $_GET['id'];

$q = "DELETE FROM peran_lab WHERE id_peran = $id";
pg_query($conn, $q);

header("Location: kelola_peranLab.php");
exit;
