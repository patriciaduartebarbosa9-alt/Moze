<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '0');
require_once 'config.php';

// Função para retornar respostas JSON
function response($status, $message, $data = null) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response('error', 'Método não permitido');
}

// Verificar autenticação
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'fotografo') {
    response('error', 'Apenas fotógrafos podem fazer upload de certificados');
}

$fotografo_id = $_SESSION['user_id'];

// Verificar se ficheiro foi enviado
if (!isset($_FILES['certificado'])) {
    response('error', 'Nenhum ficheiro foi enviado');
}

$file = $_FILES['certificado'];

// Validações
$allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
$max_size = 5 * 1024 * 1024; // 5MB

if ($file['error'] !== UPLOAD_ERR_OK) {
    response('error', 'Erro ao fazer upload do ficheiro');
}

if (!in_array($file['type'], $allowed_types)) {
    response('error', 'Tipo de ficheiro não permitido. Use PDF, JPG ou PNG');
}

if ($file['size'] > $max_size) {
    response('error', 'Ficheiro muito grande. Máximo 5MB');
}

// Criar pasta de uploads se não existir
$upload_dir = '../uploads/certificados/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Gerar nome único para o ficheiro
$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$unique_filename = 'cert_' . $fotografo_id . '_' . time() . '.' . $file_extension;
$file_path = $upload_dir . $unique_filename;

// Mover ficheiro
if (!move_uploaded_file($file['tmp_name'], $file_path)) {
    response('error', 'Erro ao salvar ficheiro');
}

// Atualizar banco de dados
$stmt = $conn->prepare("UPDATE fotografos SET certificado = ?, certificado_verificado = FALSE WHERE utilizador_id = ?");
$stmt->bind_param("si", $unique_filename, $fotografo_id);

if (!$stmt->execute()) {
    unlink($file_path); // Apagar ficheiro se falhar a atualização
    response('error', 'Erro ao salvar informações: ' . $stmt->error);
}

$stmt->close();

// Retornar sucesso
response('success', 'Certificado enviado com sucesso! Aguardando verificação da administração.', [
    'certificado' => $unique_filename,
    'status' => 'pendente_verificacao',
    'mensagem' => 'Seu certificado será verificado em breve'
]);

mysqli_close($conn);
?>
