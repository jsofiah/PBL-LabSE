<?php
require '../config.php';

$id = $_GET['id'];

pg_query_params($conn, "CALL sp_delete_pendaftaran($1)", [$id]);

header("Location: kelola_pendaftaran.php?deleted=1");
exit;
?>
