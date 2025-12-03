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

$message = "Fasilitas berhasil dihapus!";
$redirect = "kelola_fasilitas.php";
$success = false;

if ($data) {
    if (!empty($data['url_gambar_fasilitas'])) {

        $filePath = "../" . $data['url_gambar_fasilitas'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $qDelete = "CALL sp_delete_fasilitas($1)";
    $result = pg_query_params($conn, $qDelete, [$id]);

    if ($result) {
        $success = true;
    } else {
        $message = "Gagal menghapus fasilitas: " . pg_last_error($conn);
    }
} else {
    $message = "Data fasilitas tidak ditemukan!";
}

echo "
<script>
    alert('$message');
    window.location.href = '$redirect';
</script>
";
exit;
?>