<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "../config.php";

$id = $_GET['id'] ?? 0;
$id = intval($id);

$qCheck = "SELECT * FROM footer_social WHERE id_social = $id";
$rCheck = pg_query($conn, $qCheck);
$data = pg_fetch_assoc($rCheck);

if (!$data) {
    echo "<script>
        alert('Social media tidak ditemukan!');
        window.location.href='kelola_footer.php';
    </script>";
    exit;
}

$deleteQuery = "DELETE FROM footer_social WHERE id_social = $id";
pg_query($conn, $deleteQuery);

echo "<script>
        alert('Social media berhasil dihapus!');
        window.location.href = 'kelola_footer.php';
    </script>";
exit;
?>
