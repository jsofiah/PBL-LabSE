<?php
    $host     = 'localhost';
    $port     = '5432';
    $dbname   = 'db_LabSE';
    $user     = 'postgres';
    $password = 'admin';
    $pg_dump  = "C:\\Program Files\\PostgreSQL\\15\\bin\\pg_dump.exe";

    $root_project  = dirname(__DIR__);
    $folder_backup = $root_project . '/backup_data';

    if (!is_dir($folder_backup)) {
        mkdir($folder_backup, 0777, true);
    }

    $nama_file = 'Backup_LabSE_' . date('Y-m-d') . '.sql';
    $file_sql  = $folder_backup . '/' . $nama_file;

    putenv("PGPASSWORD=$password");
    $cmd = "\"$pg_dump\" --host=$host --port=$port --username=$user --format=plain --clean --file=\"$file_sql\" $dbname";
    exec($cmd, $output, $status);
    putenv("PGPASSWORD=");

    if ($status !== 0 || !file_exists($file_sql)) {
        header("Location: dashboard.php?status=gagal&pesan=Backup gagal");
        exit;
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $nama_file . '"');
    header('Content-Length: ' . filesize($file_sql));

    readfile($file_sql);
    unlink($file_sql);
    exit;

?>
