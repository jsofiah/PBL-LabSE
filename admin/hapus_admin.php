<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $qCek = "SELECT foto_admin FROM admin_user WHERE id = $id";
    $rCek = pg_query($conn, $qCek);
    $data = pg_fetch_assoc($rCek);

    $query = "CALL sp_delete_admin($id)";
    $result = pg_query($conn, $query);

    if ($result) {

        if (!empty($data['foto_admin'])) {
            $filePath = $data['foto_admin'];

            if (file_exists($filePath) && is_file($filePath)) {
                unlink($filePath);
            }
        }

        $_SESSION['pesan'] = "Admin berhasil dihapus!";
    } else {
        $_SESSION['pesan'] = "Gagal menghapus admin.";
    }
}

header("Location: kelola_admin.php");
exit;
?>