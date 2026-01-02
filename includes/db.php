<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'zabocki_lavovi';

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset('utf8mb4');

$mysqli = $conn;