<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$id = $_GET['id'];

$q = "CALL sp_delete_peran_lab($1)";
pg_query_params($conn, $q, array($id));

echo "
    <script>
        alert('Peran lab berhasil dihapus!');
        window.location.href = 'kelola_peranLab.php';
    </script>
";
exit;
?>
