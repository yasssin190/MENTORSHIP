<?php

$dsn ="mysql:hostname=127.0.0.1;dbname=db_mentori";
$username = "root";
$password="";
try
{
    $connessione = new PDO($dsn,$username,$password);
}
catch(Exception $e)
{
    echo "errore: ".$e->getMessage();
}



?>