<?php
session_start();
require_once '../config.php';

$id = $_GET['id'];

pg_query_params($conn, "CALL sp_delete_status($1)", [$id]);

header("Location: kelola_statusPendaftaran.php");
exit;
