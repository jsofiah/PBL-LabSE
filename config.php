<?php
    $host = 'localhost';
    $port = '5432';
    $dbname = 'db_LabSE';
    $user = 'postgres';
    $pass = 'Jamdinding20';

    $conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$pass");
    if(!$conn){
        die("Koneksi gagal: " . pg_last_error());
    }
?>
