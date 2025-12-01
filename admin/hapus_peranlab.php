<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$id = $_GET['id'];

$q = "DELETE FROM peran_lab WHERE id_peran = $id";
pg_query($conn, $q);
echo "
    <script>
        alert('Peran lab berhasil dihapus!');
        window.location.href = 'kelola_peranLab.php';
    </script>
    ";

header("Location: kelola_peranLab.php");
exit;
