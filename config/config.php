<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '0');

$host = "sql109.infinityfree.com";
$user = "if0_40439565";
$pass = "Mozept123";
$db   = "if0_40439565_moze";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Erro na ligação: ' . mysqli_connect_error()]);
    exit;
}

mysqli_set_charset($conn, "utf8mb4");
?>