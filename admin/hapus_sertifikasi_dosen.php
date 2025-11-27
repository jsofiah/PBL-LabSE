<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "../config.php";

$id_dosen = $_GET['id_dosen'] ?? 0;
$id_sertifikasi = $_GET['id_sertifikasi'] ?? 0;

if ($id_dosen == 0 || $id_sertifikasi == 0) {
    echo "<script>
            alert('Parameter tidak valid!');
            window.location='kelola_sertifikasi.php';
          </script>";
    exit;
}

// Hapus
$q = "CALL sp_delete_dosen_sertifikasi($1, $2)";
pg_query_params($conn, $q, [$id_dosen, $id_sertifikasi]);

echo "<script>
        alert('Sertifikasi dosen berhasil dihapus');
        window.location='kelola_sertifikasi.php';
      </script>";
exit;
?>
