<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require '../config.php';

$id = $_GET['id'];

$q = pg_query($conn, "SELECT url_gambar_galeri FROM galeri WHERE id_galeri=$id");
$data = pg_fetch_assoc($q);

$path = "../" . $data['url_gambar_galeri'];
if (file_exists($path)) {
    unlink($path);
}

pg_query($conn, "DELETE FROM galeri WHERE id_galeri=$id");

header("Location: kelola_galeri.php");
exit;
