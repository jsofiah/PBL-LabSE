<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id = $_GET['id'] ?? 0;

    $qCheck = "SELECT * FROM sertifikasi WHERE id_sertifikasi = $id";
    $rCheck = pg_query($conn, $qCheck);
    $data = pg_fetch_assoc($rCheck);

    if (!$data) {
        echo "<script>
            alert('Sertifikasi tidak ditemukan!');
            window.location.href='kelola_sertifikasi.php';
        </script>";
        exit;
    }

    $deleteQuery = "CALL sp_delete_sertifikasi($id)";
    pg_query($conn, $deleteQuery);

    echo "<script>
            alert('Sertifikasi berhasil dihapus!');
            window.location.href = 'kelola_sertifikasi.php';
        </script>";
    exit;
?>