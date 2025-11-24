<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id_subnav = $_GET['id'] ?? 0;

    if (!$id_subnav) {
        echo "
            <script>
                alert('ID tidak valid!');
                window.location.href = 'kelola_nav.php';
            </script>
        ";
        exit;
    }

    $qDelete = "CALL sp_delete_subnav($id_subnav)";
    pg_query($conn, $qDelete);

    echo "
        <script>
            alert('Subnav berhasil dihapus!');
            window.location.href = 'kelola_nav.php';
        </script>
    ";
    exit;
?>
