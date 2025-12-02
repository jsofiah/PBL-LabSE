<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require '../config.php';

$id = $_GET['id'] ?? 0;

$qData = "SELECT url_gambar_fasilitas FROM fasilitas WHERE id_fasilitas = $1";
$rData = pg_query_params($conn, $qData, [$id]);
$data = pg_fetch_assoc($rData);

if ($data) {

    if (!empty($data['url_gambar_fasilitas'])) {

        $filePath = "../" . $data['url_gambar_fasilitas'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $qDelete = "DELETE FROM fasilitas WHERE id_fasilitas = $1";
    pg_query_params($conn, $qDelete, [$id]);
}

header("Location: kelola_fasilitas.php");
exit;
?>