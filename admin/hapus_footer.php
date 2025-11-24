<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id_footer = $_GET['id'] ?? 0;

    $q = "SELECT url_logo_footer FROM vw_footer WHERE id_footer = $id_footer";
    $r = pg_query($conn, $q);
    $data = pg_fetch_assoc($r);

    if (!$data) {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href='kelola_footer.php';</script>";
        exit;
    }

    if (!empty($data['url_logo_footer'])) {
        $filePath = "../" . $data['url_logo_footer'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $deleteQuery = "CALL sp_delete_footer($id_footer)";
    pg_query($conn, $deleteQuery);

    echo "
    <script>
        alert('Footer berhasil dihapus!');
        window.location.href = 'kelola_footer.php';
    </script>
    ";
    exit;
?>