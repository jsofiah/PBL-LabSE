<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

if (!isset($_GET['id'])) {
    header("Location: kelola_roadmap.php");
    exit;
}

$id = $_GET['id'];

// Jalankan function delete
$qDelete = "CALL sp_delete_roadmap($id)";
pg_query($conn, $qDelete);

if ($result) {
    echo "<script>
            alert('Roadmap berhasil dihapus!');
            window.location = 'kelola_roadmap.php';
          </script>";
} else {
    echo "<script>
            alert('Gagal menghapus roadmap!');
            window.location = 'kelola_roadmap.php';
          </script>";
}
?>