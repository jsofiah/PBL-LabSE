<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "../config.php";

$id = $_GET['id'] ?? 0;

$q = "SELECT * FROM vw_konten_lab WHERE id_konten = $id";
$r = pg_query($conn, $q);
$data = pg_fetch_assoc($r);

if (!$data) {
    echo "<script>
            alert('Data konten tidak ditemukan!');
            window.location.href='kelola_kontenLab.php';
          </script>";
    exit;
}

$deleteQuery = "CALL sp_delete_konten_lab($id)";
pg_query($conn, $deleteQuery);

echo "<script>
        alert('Konten berhasil dihapus!');
        window.location.href = 'kelola_kontenLab.php';
      </script>";
exit;
?>
