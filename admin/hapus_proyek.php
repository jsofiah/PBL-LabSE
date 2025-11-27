<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

if (!isset($_GET['id'])) {
    header("Location: kelola_proyek.php");
    exit;
}

$id = $_GET['id'];

$qDelete = "CALL sp_delete_proyek($id)";
$res = pg_query($conn, $qDelete);

if ($res) {
    echo "<script>
            alert('Proyek berhasil dihapus!');
            window.location = 'kelola_proyek.php';
          </script>";
} else {
    echo "<script>
            alert('Gagal menghapus proyek!');
            window.location = 'kelola_proyek.php';
          </script>";
}
?>