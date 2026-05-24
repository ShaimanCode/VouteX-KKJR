<?php
$host = 'localhost';
$dbname = 'votex_kkjr';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Sambungan gagal: ' . $conn->connect_error);
}

$conn->set_charset('utf8');
session_start();
?>
