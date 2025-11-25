<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id_dosen = $_GET['id'] ?? 0;

    $q = "SELECT url_foto_dosen FROM vw_detail_dosen WHERE id_dosen = $id_dosen";
    $r = pg_query($conn, $q);
    $data = pg_fetch_assoc($r);

    if (!$data) {
        echo "<script>
                alert('Data dosen tidak ditemukan!');
                window.location.href='kelola_profil.php';
            </script>";
        exit;
    }

    if (!empty($data['url_foto_dosen'])) {
        $filePath = "../img/dosen/" . $data['url_foto_dosen'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $deleteQuery = "CALL sp_delete_dosen($id_dosen)";
    pg_query($conn, $deleteQuery);

    echo "<script>
            alert('Dosen berhasil dihapus!');
            window.location.href = 'kelola_profil.php';
        </script>";
    exit;
?>