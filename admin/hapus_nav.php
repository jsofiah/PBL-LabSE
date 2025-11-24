<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id_nav = $_GET['id'] ?? 0;

    $qDelete = "CALL sp_delete_nav($id_nav)";
    pg_query($conn, $qDelete);

    echo "
    <script>
        alert('Nav berhasil dihapus!');
        window.location.href = 'kelola_nav.php';
    </script>";
    exit;
?>