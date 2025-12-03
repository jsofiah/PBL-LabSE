<?php
set_time_limit(0);              
ini_set('memory_limit', '1G');  

$host = 'localhost'; $port = '5432';
$dbname = 'db_LabSE'; $user = 'postgres'; $password = 'admin';     
$pg_dump = "C:\\Program Files\\PostgreSQL\\15\\bin\\pg_dump.exe";

$root_project = dirname(__DIR__);             
$folder_img   = $root_project . '/img';       
$folder_tujuan = $root_project . '/backup_data'; 

if (!is_dir($folder_tujuan)) { mkdir($folder_tujuan, 0777, true); }

$nama_zip = 'Backup_LabSE_' . date('Y-m-d') . '.zip';
$file_zip = $folder_tujuan . '/' . $nama_zip;   
$file_sql = $folder_tujuan . '/temp.sql';       

// 1. DUMP PHP menyuruh CMD menjalankan pg_dump
putenv("PGPASSWORD=$password");
$cmd = "\"$pg_dump\" --host=$host --port=$port --username=$user --format=plain --clean --file=\"$file_sql\" $dbname";
exec($cmd, $output, $status);
putenv("PGPASSWORD="); 

if ($status !== 0) { 
    header("Location: dashboard.php?status=gagal&pesan=Password salah atau pg_dump error");
    exit;
}

//  2. ZIP 
$zip = new ZipArchive();
if ($zip->open($file_zip, ZipArchive::CREATE) === TRUE) {
    //Memasukkan file temp.sql 
    $zip->addFile($file_sql, 'database.sql');
    
    if (is_dir($folder_img)) {
        //menelurusuri folder img 
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder_img),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $pathAsli = $file->getRealPath();
                $pathDiZip = substr($pathAsli, strlen($root_project) + 1);
                $zip->addFile($pathAsli, $pathDiZip);
            }
        }
    }
    
    $zip->close();
    unlink($file_sql); 
    header("Location: dashboard.php?status=sukses&file=" . $nama_zip);
    exit;

} else {
    header("Location: dashboard.php?status=gagal&pesan=Gagal membuat ZIP");
    exit;
}
?>