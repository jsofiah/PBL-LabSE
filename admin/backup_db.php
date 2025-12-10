<?php
    $host = 'localhost';
    $port = '5432';
    $dbname = 'db_LabSE';
    $user = 'postgres';
    $password = 'admin';
    $pg_dump = "C:\\Program Files\\PostgreSQL\\15\\bin\\pg_dump.exe";


    date_default_timezone_set('Asia/Jakarta'); 

    $backup_name = 'Backup_LabSE_' . date('Y-m-d_H-i') . '.sql';
    $current_time = date("d M Y, H:i") . " WIB";
    $file_riwayat = 'riwayat_backup.txt';

    $history = file_exists($file_riwayat) ? file($file_riwayat, FILE_IGNORE_NEW_LINES) : [];
    array_unshift($history, $current_time);
    $history_top_5 = array_slice($history, 0, 5);
    file_put_contents($file_riwayat, implode("\n", $history_top_5));

    set_time_limit(0);
    if (ob_get_level()) ob_end_clean();

    header('Content-Description: File Transfer');
    header('Content-Type: application/force-download');
    header('Content-Type: application/octet-stream');
    header('Content-Type: application/download');
    header("Content-Disposition: attachment; filename=\"" . $backup_name . "\"");
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');

    putenv("PGPASSWORD=$password");
    $cmd = "\"$pg_dump\" --host=$host --port=$port --username=$user --format=plain --clean --if-exists $dbname";
    passthru($cmd);
    putenv("PGPASSWORD=");

    exit;
?>