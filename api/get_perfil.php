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

// Verificar autenticação
session_start();
if (!isset($_SESSION['user_id'])) {
    response('error', 'Não autenticado');
}

$user_id = $_SESSION['user_id'];

// Buscar dados do utilizador
$stmt = $conn->prepare("SELECT id, email, nome_completo, telefone, tipo, foto_perfil, bio, data_criacao FROM utilizadores WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    response('error', 'Utilizador não encontrado');
}

$user = $result->fetch_assoc();
$stmt->close();

$perfil = [
    'id' => $user['id'],
    'email' => $user['email'],
    'nome' => $user['nome_completo'],
    'telefone' => $user['telefone'],
    'tipo' => $user['tipo'],
    'foto_perfil' => $user['foto_perfil'],
    'bio' => $user['bio'],
    'data_criacao' => $user['data_criacao']
];

// Se for fotógrafo, buscar dados específicos
if ($user['tipo'] === 'fotografo') {
    $stmt = $conn->prepare("SELECT especialidades, preco_hora, avaliacoes_media, numero_avaliacoes, bio_profissional, portfolio_url, disponivel, certificado, certificado_verificado FROM fotografos WHERE utilizador_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $foto = $result->fetch_assoc();
        $perfil['fotografo'] = [
            'especialidades' => explode(',', $foto['especialidades']),
            'preco_hora' => (float)$foto['preco_hora'],
            'avaliacoes_media' => (float)$foto['avaliacoes_media'],
            'numero_avaliacoes' => (int)$foto['numero_avaliacoes'],
            'bio_profissional' => $foto['bio_profissional'],
            'portfolio_url' => $foto['portfolio_url'],
            'disponivel' => (bool)$foto['disponivel'],
            'certificado' => [
                'ficheiro' => $foto['certificado'],
                'verificado' => (bool)$foto['certificado_verificado']
            ]
        ];
    }
    $stmt->close();
}

// Se for cliente, buscar dados específicos
if ($user['tipo'] === 'cliente') {
    $stmt = $conn->prepare("SELECT localizacao, preferencias FROM clientes WHERE utilizador_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $cliente = $result->fetch_assoc();
        $perfil['cliente'] = [
            'localizacao' => $cliente['localizacao'],
            'preferencias' => $cliente['preferencias']
        ];
    }
    $stmt->close();
}

response('success', 'Perfil carregado com sucesso', $perfil);

mysqli_close($conn);
?>
