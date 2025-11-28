<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "../config.php";

$id = $_GET['id'] ?? 0;
$id = intval($id);

$cek = pg_query($conn, "SELECT id_publikasi FROM publikasi WHERE id_publikasi = $id");
$data = pg_fetch_assoc($cek);

if (!$data) {
    echo "<script>
            alert('Data publikasi tidak ditemukan!');
            window.location.href='kelola_publikasi.php';
          </script>";
    exit;
}

pg_query($conn, "CALL sp_delete_publikasi($id)");

echo "<script>
        alert('Publikasi berhasil dihapus!');
        window.location.href='kelola_publikasi.php';
      </script>";
exit;
?>
