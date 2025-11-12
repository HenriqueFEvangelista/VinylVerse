<?php
$host = 'localhost';
$user = 'root';
$pass = ''; 
$dbname = 'vinylverse'; 

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die('Erro na conexão: ' . $conn->connect_error);
}
?>
