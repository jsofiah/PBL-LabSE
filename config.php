<?php
    $host = 'localhost';
    $port = '5432';
    $dbname = 'db_labSE';
    $user = 'diisi';
    $pass = 'diisi';

    $conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$pass");
    if(!$conn){
        die("Koneksi gagal: " . pg_last_error());
    }
?>
