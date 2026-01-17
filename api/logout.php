<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '0');

// Logout simples - limpar sessão
session_start();
session_destroy();

echo json_encode([
    'status' => 'success',
    'message' => 'Logout realizado com sucesso'
]);
?>
