<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$id = intval($_GET['id']);

$qData = "SELECT url_gambar_mitra FROM mitra WHERE id_mitra = $1";
$rData = pg_query_params($conn, $qData, [$id]);
$data = pg_fetch_assoc($rData);

if ($data && !empty($data['url_gambar_mitra'])) {

    $filePath = "../" . $data['url_gambar_mitra'];

    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

$qDelete = "CALL sp_delete_mitra($1)";
$res = pg_query_params($conn, $qDelete, [$id]);

header("Location: kelola_mitra.php?msg=deleted");
exit;
?>
