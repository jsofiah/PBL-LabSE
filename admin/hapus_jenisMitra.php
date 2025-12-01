<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require "../config.php";

if (!isset($_GET['id'])) {
    header("Location: kelola_jenisMitra.php");
    exit;
}

$id = intval($_GET['id']); // aman

$qDelete = "CALL sp_delete_jenismitra($id)";
$res = pg_query($conn, $qDelete);

if ($res) {
    echo "<script>
            alert('Jenis mitra berhasil dihapus!');
            window.location='kelola_jenisMitra.php';
          </script>";
    exit;
} else {
    echo "<script>
            alert('Gagal menghapus jenis mitra!');
            window.location='kelola_jenisMitra.php';
          </script>";
    exit;
}
?>
