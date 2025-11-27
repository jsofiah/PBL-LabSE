<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id_penelitian = $_GET['id'] ?? 0;

    $q = "SELECT * FROM vw_penelitian_dosen WHERE id_penelitian = $id_penelitian";
    $r = pg_query($conn, $q);
    $data = pg_fetch_assoc($r);

    if (!$data) {
        echo "<script>
                alert('Data penelitian tidak ditemukan!');
                window.location.href='kelola_penelitian.php';
            </script>";
        exit;
    }

    $deleteQuery = "CALL sp_delete_penelitian($id_penelitian)";
    pg_query($conn, $deleteQuery);

    echo "<script>
            alert('Data penelitian berhasil dihapus!');
            window.location.href = 'kelola_penelitian.php';
        </script>";
    exit;
?>
