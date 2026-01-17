<?php
header('Content-Type: application/json');
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

// Receber dados
$data = json_decode(file_get_contents('php://input'), true);

// Validações
$nome = isset($data['nome']) ? trim($data['nome']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? $data['password'] : '';
$tipo = isset($data['tipo']) ? $data['tipo'] : ''; // 'cliente' ou 'fotografo'
$telefone = isset($data['telefone']) ? trim($data['telefone']) : '';

// Validar campos obrigatórios
if (empty($nome)) {
    response('error', 'Nome é obrigatório');
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    response('error', 'Email inválido');
}

if (empty($password) || strlen($password) < 6) {
    response('error', 'Password deve ter no mínimo 6 caracteres');
}

if ($tipo !== 'cliente' && $tipo !== 'fotografo') {
    response('error', 'Tipo de utilizador inválido (cliente ou fotografo)');
}

// Verificar se email já existe
$stmt = $conn->prepare("SELECT id FROM utilizadores WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    response('error', 'Email já registado');
}
$stmt->close();

// Hash da password
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// Inserir utilizador
$stmt = $conn->prepare("INSERT INTO utilizadores (email, password, nome_completo, telefone, tipo) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $email, $password_hash, $nome, $telefone, $tipo);

if (!$stmt->execute()) {
    response('error', 'Erro ao registar utilizador: ' . $stmt->error);
}

$utilizador_id = $stmt->insert_id;
$stmt->close();

// Se for fotógrafo, inserir na tabela fotografos
if ($tipo === 'fotografo') {
    $especialidades = isset($data['especialidades']) ? $data['especialidades'] : '';
    $preco_hora = isset($data['preco_hora']) ? (float)$data['preco_hora'] : 0;
    $bio = isset($data['bio']) ? trim($data['bio']) : '';
    
    $stmt = $conn->prepare("INSERT INTO fotografos (utilizador_id, especialidades, preco_hora, bio_profissional) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $utilizador_id, $especialidades, $preco_hora, $bio);
    
    if (!$stmt->execute()) {
        response('error', 'Erro ao registar como fotógrafo: ' . $stmt->error);
    }
    $stmt->close();
}

// Se for cliente, inserir na tabela clientes
if ($tipo === 'cliente') {
    $localizacao = isset($data['localizacao']) ? trim($data['localizacao']) : '';
    
    $stmt = $conn->prepare("INSERT INTO clientes (id, utilizador_id, localizacao) VALUES (NULL, ?, ?)");
    $stmt->bind_param("is", $utilizador_id, $localizacao);
    
    if (!$stmt->execute()) {
        response('error', 'Erro ao registar como cliente: ' . $stmt->error);
    }
    $stmt->close();
}

// Retornar sucesso
response('success', 'Registo realizado com sucesso!', [
    'id' => $utilizador_id,
    'email' => $email,
    'nome' => $nome,
    'tipo' => $tipo
]);

mysqli_close($conn);
?>
