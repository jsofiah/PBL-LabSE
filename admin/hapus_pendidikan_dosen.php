<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id_pendidikan = $_GET['id'] ?? 0;

    if ($id_pendidikan == 0) {
        echo "<script>
                alert('ID pendidikan tidak valid!');
                window.location.href='kelola_pendidikan.php';
            </script>";
        exit;
    }

    $q = "SELECT * FROM vw_riwayat_pendidikan WHERE id_pendidikan = $id_pendidikan";
    $r = pg_query($conn, $q);
    $data = pg_fetch_assoc($r);

    if (!$data) {
        echo "<script>
                alert('Data riwayat pendidikan tidak ditemukan!');
                window.location.href='kelola_pendidikan.php';
            </script>";
        exit;
    }

    $deleteQuery = "CALL sp_delete_riwayat_pendidikan($id_pendidikan)";
    pg_query($conn, $deleteQuery);

    echo "<script>
            alert('Riwayat pendidikan berhasil dihapus!');
            window.location.href = 'kelola_pendidikan.php';
        </script>";
    exit;
?>
