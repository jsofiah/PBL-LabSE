<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    require_once "../config.php";

    $id_mhs = $_GET['id_mhs'] ?? 0;
    $id_keahlian = $_GET['id_keahlian'] ?? 0;

    if ($id_mhs == 0 || $id_keahlian == 0) {
        echo "<script>
                alert('Parameter tidak valid!');
                window.location='kelola_dosenKeahlian.php';
            </script>";
        exit;
    }

    $q = "CALL sp_delete_mhs_keahlian($1, $2)";
    pg_query_params($conn, $q, [$id_mhs, $id_keahlian]);

    echo "<script>
            alert('Keahlian mahasiswa berhasil dihapus');
            window.location='kelola_mhsKeahlian.php';
        </script>";
    exit;
?>