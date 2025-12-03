<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

$id = $_GET['id'] ?? 0;
$id = intval($id);

if ($id) {
    $q = "SELECT url_logo FROM logo_cta WHERE id_logo_cta = $1";
    $r = pg_query_params($conn, $q, [$id]);
    $data = pg_fetch_assoc($r);

    if ($data) {
        $filePath = "../" . $data['url_logo'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $query = "CALL sp_delete_logocta($1)";
    pg_query_params($conn, $query, [$id]);
}

echo "<script>alert('Logo CTA berhasil dihapus!'); window.location='kelola_logoCTA.php';</script>";
exit;
?>