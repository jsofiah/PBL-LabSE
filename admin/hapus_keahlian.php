<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id_keahlian = $_GET['id'] ?? 0;

    $q = "SELECT nama_keahlian FROM vw_bidang_keahlian WHERE id_keahlian = $id_keahlian";
    $r = pg_query($conn, $q);
    $data = pg_fetch_assoc($r);

    if (!$data) {
        echo "<script>
                alert('Data keahlian tidak ditemukan!');
                window.location.href='kelola_keahlian.php';
            </script>";
        exit;
    }

    $deleteQuery = "CALL sp_delete_keahlian($id_keahlian)";
    pg_query($conn, $deleteQuery);

    echo "<script>
            alert('Keahlian berhasil dihapus!');
            window.location.href = 'kelola_keahlian.php';
        </script>";
    exit;
?>