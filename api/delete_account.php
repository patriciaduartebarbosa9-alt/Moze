<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once 'config.php';

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
    exit;
}

// Obter dados
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? null;

if (!$userId) {
    echo json_encode(['status' => 'error', 'message' => 'ID de utilizador não fornecido']);
    exit;
}

try {
    // Iniciar transação
    $conn->begin_transaction();
    
    // Obter tipo de utilizador
    $result = $conn->query("SELECT tipo FROM utilizadores WHERE id = $userId");
    $user = $result->fetch_assoc();
    
    if (!$user) {
        throw new Exception('Utilizador não encontrado');
    }
    
    $tipo = $user['tipo'];
    
    // Apagar dos dados tipo-específicos
    if ($tipo === 'cliente') {
        $conn->query("DELETE FROM clientes WHERE utilizador_id = $userId");
    } elseif ($tipo === 'fotografo') {
        $conn->query("DELETE FROM fotografos WHERE utilizador_id = $userId");
    }
    
    // Apagar das outras tabelas
    $conn->query("DELETE FROM reservas WHERE cliente_id = $userId OR fotografo_id = $userId");
    $conn->query("DELETE FROM avaliacoes WHERE cliente_id = $userId OR fotografo_id = $userId");
    $conn->query("DELETE FROM mensagens WHERE remetente_id = $userId OR destinatario_id = $userId");
    $conn->query("DELETE FROM disponibilidades WHERE fotografo_id = $userId");
    
    // Apagar da tabela principal
    $conn->query("DELETE FROM utilizadores WHERE id = $userId");
    
    // Confirmar transação
    $conn->commit();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Conta eliminada com sucesso'
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro ao eliminar conta: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
