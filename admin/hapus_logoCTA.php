<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

$id = $_GET['id'] ?? 0;

if ($id) {
    $query = "SELECT sp_delete_logo_cta($id)";
    pg_query($conn, $query);
}

echo "<script>alert('Logo CTA berhasil dihapus!'); window.location='kelola_logoCTA.php';</script>";
exit;
?>
