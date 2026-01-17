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
if (!isset($_SESSION['user_id'])) {
    response('error', 'Não autenticado');
}

$user_id = $_SESSION['user_id'];

// Receber dados
$data = json_decode(file_get_contents('php://input'), true);

// Dados gerais (para todos)
$nome = isset($data['nome']) ? trim($data['nome']) : null;
$telefone = isset($data['telefone']) ? trim($data['telefone']) : null;
$bio = isset($data['bio']) ? trim($data['bio']) : null;

// Atualizar utilizador
if ($nome || $telefone || $bio) {
    $updates = [];
    $params = [];
    $types = '';
    
    if ($nome) {
        $updates[] = "nome_completo = ?";
        $params[] = $nome;
        $types .= 's';
    }
    
    if ($telefone) {
        $updates[] = "telefone = ?";
        $params[] = $telefone;
        $types .= 's';
    }
    
    if ($bio) {
        $updates[] = "bio = ?";
        $params[] = $bio;
        $types .= 's';
    }
    
    if (!empty($updates)) {
        $sql = "UPDATE utilizadores SET " . implode(', ', $updates) . " WHERE id = ?";
        $params[] = $user_id;
        $types .= 'i';
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            response('error', 'Erro ao atualizar perfil: ' . $stmt->error);
        }
        $stmt->close();
    }
}

// Buscar tipo de utilizador
$stmt = $conn->prepare("SELECT tipo FROM utilizadores WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$tipo = $user['tipo'];
$stmt->close();

// Dados específicos para fotógrafo
if ($tipo === 'fotografo') {
    $especialidades = isset($data['especialidades']) ? trim($data['especialidades']) : null;
    $preco_hora = isset($data['preco_hora']) ? (float)$data['preco_hora'] : null;
    $bio_profissional = isset($data['bio_profissional']) ? trim($data['bio_profissional']) : null;
    $portfolio_url = isset($data['portfolio_url']) ? trim($data['portfolio_url']) : null;
    $disponivel = isset($data['disponivel']) ? (bool)$data['disponivel'] : null;
    
    $updates = [];
    $params = [];
    $types = '';
    
    if ($especialidades !== null) {
        $updates[] = "especialidades = ?";
        $params[] = $especialidades;
        $types .= 's';
    }
    
    if ($preco_hora !== null) {
        $updates[] = "preco_hora = ?";
        $params[] = $preco_hora;
        $types .= 'd';
    }
    
    if ($bio_profissional !== null) {
        $updates[] = "bio_profissional = ?";
        $params[] = $bio_profissional;
        $types .= 's';
    }
    
    if ($portfolio_url !== null) {
        $updates[] = "portfolio_url = ?";
        $params[] = $portfolio_url;
        $types .= 's';
    }
    
    if ($disponivel !== null) {
        $updates[] = "disponivel = ?";
        $params[] = $disponivel ? 1 : 0;
        $types .= 'i';
    }
    
    if (!empty($updates)) {
        $sql = "UPDATE fotografos SET " . implode(', ', $updates) . " WHERE utilizador_id = ?";
        $params[] = $user_id;
        $types .= 'i';
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            response('error', 'Erro ao atualizar perfil de fotógrafo: ' . $stmt->error);
        }
        $stmt->close();
    }
}

// Dados específicos para cliente
if ($tipo === 'cliente') {
    $localizacao = isset($data['localizacao']) ? trim($data['localizacao']) : null;
    
    if ($localizacao !== null) {
        $stmt = $conn->prepare("UPDATE clientes SET localizacao = ? WHERE utilizador_id = ?");
        $stmt->bind_param("si", $localizacao, $user_id);
        
        if (!$stmt->execute()) {
            response('error', 'Erro ao atualizar perfil de cliente: ' . $stmt->error);
        }
        $stmt->close();
    }
}

// Upload de foto de perfil
if (isset($_FILES['foto_perfil'])) {
    $file = $_FILES['foto_perfil'];
    
    // Validações
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        response('error', 'Erro ao fazer upload da foto');
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        response('error', 'Tipo de ficheiro não permitido. Use JPG, PNG ou GIF');
    }
    
    if ($file['size'] > $max_size) {
        response('error', 'Ficheiro muito grande. Máximo 2MB');
    }
    
    // Criar pasta de uploads se não existir
    $upload_dir = '../uploads/perfil/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Gerar nome único para o ficheiro
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $unique_filename = 'perfil_' . $user_id . '_' . time() . '.' . $file_extension;
    $file_path = $upload_dir . $unique_filename;
    
    // Mover ficheiro
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        response('error', 'Erro ao salvar ficheiro');
    }
    
    // Atualizar banco de dados
    $stmt = $conn->prepare("UPDATE utilizadores SET foto_perfil = ? WHERE id = ?");
    $stmt->bind_param("si", $unique_filename, $user_id);
    
    if (!$stmt->execute()) {
        unlink($file_path); // Apagar ficheiro se falhar
        response('error', 'Erro ao salvar foto de perfil: ' . $stmt->error);
    }
    $stmt->close();
}

// Retornar sucesso
response('success', 'Perfil atualizado com sucesso!', [
    'id' => $user_id,
    'tipo' => $tipo
]);

mysqli_close($conn);
?>
