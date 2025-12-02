<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require '../config.php';

$id = intval($_GET['id']);

$q = "SELECT url_gambar_proyek1, url_gambar_proyek2, url_gambar_proyek3
      FROM proyek WHERE id_proyek = $1";
$r = pg_query_params($conn, $q, [$id]);
$data = pg_fetch_assoc($r);

foreach ($data as $img) {
    if (!empty($img)) {
        $path = "../" . $img;
        if (file_exists($path)) unlink($path);
    }
}

$qDelete = "CALL sp_delete_proyek($1)";
pg_query_params($conn, $qDelete, [$id]);

header("Location: kelola_proyek.php?msg=deleted");
exit;
?>
