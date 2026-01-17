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

// Receber dados
$data = json_decode(file_get_contents('php://input'), true);

// Validações
$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? $data['password'] : '';

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    response('error', 'Email inválido');
}

if (empty($password)) {
    response('error', 'Password é obrigatória');
}

// Buscar utilizador
$stmt = $conn->prepare("SELECT id, email, password, nome_completo, tipo, foto_perfil FROM utilizadores WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    response('error', 'Email ou password incorretos');
}

$user = $result->fetch_assoc();
$stmt->close();

// Verificar password
if (!password_verify($password, $user['password'])) {
    response('error', 'Email ou password incorretos');
}

// Se for fotógrafo, verificar se tem certificado
$certificado_verificado = false;
$status_certificado = 'pendente';

if ($user['tipo'] === 'fotografo') {
    $stmt = $conn->prepare("SELECT id, certificado, certificado_verificado FROM fotografos WHERE utilizador_id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $foto_result = $stmt->get_result();
    
    if ($foto_result->num_rows > 0) {
        $foto = $foto_result->fetch_assoc();
        if (!empty($foto['certificado'])) {
            $certificado_verificado = $foto['certificado_verificado'];
            $status_certificado = $foto['certificado_verificado'] ? 'verificado' : 'pendente_verificacao';
        }
    }
    $stmt->close();
}

// Gerar token simples (pode usar JWT em produção)
$token = bin2hex(random_bytes(32));

// Salvar token em sessão ou cookie
session_start();
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_type'] = $user['tipo'];
$_SESSION['token'] = $token;

// Retornar dados do utilizador
$userData = [
    'id' => $user['id'],
    'email' => $user['email'],
    'nome' => $user['nome_completo'],
    'tipo' => $user['tipo'],
    'foto_perfil' => $user['foto_perfil'],
    'token' => $token
];

// Se for fotógrafo, adicionar status do certificado
if ($user['tipo'] === 'fotografo') {
    $userData['certificado'] = [
        'status' => $status_certificado,
        'verificado' => $certificado_verificado
    ];
    
    if ($status_certificado === 'pendente') {
        $userData['mensagem_certificado'] = 'Por favor, faça upload do seu certificado profissional para completar o perfil';
    }
}

response('success', 'Login realizado com sucesso!', $userData);

mysqli_close($conn);
?>
