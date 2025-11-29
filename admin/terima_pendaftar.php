<?php
require '../config.php';
require '../vendor/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/src/SMTP.php';
require '../vendor/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$id = $_GET['id'];

$qGet = "SELECT * FROM pendaftaran_segeeks WHERE id_pendaftar = $1";
$rGet = pg_query_params($conn, $qGet, [$id]);
$data = pg_fetch_assoc($rGet);

if ($data) {

    $qCall = "CALL sp_terima_pendaftar($1)";
    pg_query_params($conn, $qCall, [$id]);

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sibalinga6@gmail.com';
        $mail->Password   = 'lzoo bgvy zqsh scld';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('sibalinga6@gmail.com', 'Admin SE Geeks');
        $mail->addAddress($data['email_pendaftar'], $data['nama_pendaftar']);

        $mail->isHTML(true);
        $mail->Subject = 'Pendaftaran SE Geeks - DITERIMA';
        $mail->Body = "
            Halo <b>{$data['nama_pendaftar']}</b>,<br><br>
            Selamat! Pendaftaran kamu untuk SE Geeks telah <b>DITERIMA</b>.<br><br>
            Silakan datang untuk briefing sesuai jadwal yang akan diumumkan.<br><br>
            Salam,<br>
            <b>Admin SE Geeks</b>
        ";

        $mail->send();

    } catch (Exception $e) {
    }
}

header("Location: kelola_pendaftaran.php?success=1");
exit;
?>