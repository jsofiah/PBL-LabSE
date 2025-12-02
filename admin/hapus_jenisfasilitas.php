<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$id = $_GET['id'];
$message = "";
$success = false;

$qDelete = "CALL sp_delete_jenis_fasilitas($1)";
$result = pg_query_params($conn, $qDelete, [$id]);

if ($result) {
    $message = "Jenis Fasilitas berhasil dihapus!";
} else {
    $message = "Gagal menghapus Jenis Fasilitas! Error: " . pg_last_error($conn);
}

echo "
<script>
    alert('$message');
    window.location.href = 'kelola_jenisFasilitas.php';
</script>
";
exit;
?>