<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '0');

// Garantir que não há output antes do JSON
ob_start();

try {
    require_once 'config.php';
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro na conexão: ' . $e->getMessage(), 'data' => null]);
    exit;
}

// Função para retornar respostas JSON
function response($status, $message, $data = null) {
    ob_clean();
    http_response_code(($status === 'success') ? 200 : 400);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Verificar se é POST ou GET
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    response('error', 'Método não permitido');
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

if ($action === 'migrate_clients') {
    // Migrar clientes
    $sql = "INSERT INTO clientes (utilizador_id, localizacao)
            SELECT id, 'Não especificado' 
            FROM utilizadores 
            WHERE tipo = 'cliente' 
            AND id NOT IN (SELECT utilizador_id FROM clientes)";
    
    if ($conn->query($sql)) {
        $affected_rows = $conn->affected_rows;
        response('success', "Migrados $affected_rows clientes", ['rows' => $affected_rows]);
    } else {
        response('error', 'Erro ao migrar clientes: ' . $conn->error);
    }
}
elseif ($action === 'migrate_photographers') {
    // Migrar fotógrafos
    $sql = "INSERT INTO fotografos (utilizador_id, especialidades, preco_hora, disponivel, certificado_verificado)
            SELECT id, 'Não especificado', 0, 1, 0
            FROM utilizadores 
            WHERE tipo = 'fotografo' 
            AND id NOT IN (SELECT utilizador_id FROM fotografos)";
    
    if ($conn->query($sql)) {
        $affected_rows = $conn->affected_rows;
        response('success', "Migrados $affected_rows fotógrafos", ['rows' => $affected_rows]);
    } else {
        response('error', 'Erro ao migrar fotógrafos: ' . $conn->error);
    }
}
elseif ($action === 'get_status') {
    // Verificar quantos utilizadores faltam migrar
    $query_clients = "SELECT COUNT(*) as total_clients FROM utilizadores WHERE tipo = 'cliente'";
    $query_photographers = "SELECT COUNT(*) as total_photographers FROM utilizadores WHERE tipo = 'fotografo'";
    $query_clients_migrated = "SELECT COUNT(DISTINCT utilizador_id) as migrated_clients FROM clientes";
    $query_photographers_migrated = "SELECT COUNT(DISTINCT utilizador_id) as migrated_photographers FROM fotografos";
    
    $result_clients = $conn->query($query_clients);
    $result_photographers = $conn->query($query_photographers);
    $result_clients_migrated = $conn->query($query_clients_migrated);
    $result_photographers_migrated = $conn->query($query_photographers_migrated);
    
    $data_clients = $result_clients->fetch_assoc();
    $data_photographers = $result_photographers->fetch_assoc();
    $data_clients_migrated = $result_clients_migrated->fetch_assoc();
    $data_photographers_migrated = $result_photographers_migrated->fetch_assoc();
    
    $clients_to_migrate = $data_clients['total_clients'] - $data_clients_migrated['migrated_clients'];
    $photographers_to_migrate = $data_photographers['total_photographers'] - $data_photographers_migrated['migrated_photographers'];
    
    response('success', 'Status da migração', [
        'clientes' => [
            'total' => $data_clients['total_clients'],
            'migrados' => $data_clients_migrated['migrated_clients'],
            'pendentes' => $clients_to_migrate
        ],
        'fotografos' => [
            'total' => $data_photographers['total_photographers'],
            'migrados' => $data_photographers_migrated['migrated_photographers'],
            'pendentes' => $photographers_to_migrate
        ]
    ]);
}
elseif ($action === 'migrate_all') {
    // Migrar tudo de uma vez
    $results = [];
    
    // Migrar clientes
    $sql_clients = "INSERT INTO clientes (utilizador_id, localizacao)
                    SELECT id, 'Não especificado' 
                    FROM utilizadores 
                    WHERE tipo = 'cliente' 
                    AND id NOT IN (SELECT utilizador_id FROM clientes)";
    
    if ($conn->query($sql_clients)) {
        $results['clientes'] = $conn->affected_rows;
    } else {
        response('error', 'Erro ao migrar clientes: ' . $conn->error);
    }
    
    // Migrar fotógrafos
    $sql_photographers = "INSERT INTO fotografos (utilizador_id, especialidades, preco_hora, disponivel, certificado_verificado)
                        SELECT id, 'Não especificado', 0, 1, 0
                        FROM utilizadores 
                        WHERE tipo = 'fotografo' 
                        AND id NOT IN (SELECT utilizador_id FROM fotografos)";
    
    if ($conn->query($sql_photographers)) {
        $results['fotografos'] = $conn->affected_rows;
    } else {
        response('error', 'Erro ao migrar fotógrafos: ' . $conn->error);
    }
    
    response('success', 'Migração completa', $results);
}
else {
    response('error', 'Ação não especificada. Use: migrate_clients, migrate_photographers, migrate_all ou get_status');
}

if ($conn) {
    mysqli_close($conn);
}
ob_end_clean();
?>
