<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "../config.php";

$id = $_GET['id'] ?? 0;

$q = "SELECT url_gambar_artikel FROM artikel WHERE id_artikel = $id";
$r = pg_query($conn, $q);
$data = pg_fetch_assoc($r);

if (!$data) {
    echo "<script>
            alert('Artikel tidak ditemukan!');
            window.location.href='kelola_artikel.php';
         </script>";
    exit;
}

if (!empty($data['url_gambar_artikel'])) {
    $path = "../" . $data['url_gambar_artikel'];

    if (file_exists($path)) {
        unlink($path);
    }
}
pg_query($conn, "CALL sp_delete_artikel($id)");

echo "<script>
        alert('Artikel berhasil dihapus beserta gambarnya!');
        window.location.href='kelola_artikel.php';
     </script>";
exit;
?>
