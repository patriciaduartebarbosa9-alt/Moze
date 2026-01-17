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

// Receber parâmetros
$especialidade = isset($_GET['especialidade']) ? trim($_GET['especialidade']) : '';
$localidade = isset($_GET['localidade']) ? trim($_GET['localidade']) : '';
$preco_max = isset($_GET['preco_max']) ? (float)$_GET['preco_max'] : null;
$ordem = isset($_GET['ordem']) ? trim($_GET['ordem']) : 'avaliacoes'; // avaliacoes, preco, nome

// Validar ordem
$ordens_validas = ['avaliacoes', 'preco', 'nome'];
if (!in_array($ordem, $ordens_validas)) {
    $ordem = 'avaliacoes';
}

// Construir SQL
$sql = "
    SELECT 
        f.id,
        u.id as utilizador_id,
        u.nome_completo,
        u.foto_perfil,
        u.bio,
        f.especialidades,
        f.preco_hora,
        f.avaliacoes_media,
        f.numero_avaliacoes,
        f.bio_profissional,
        f.portfolio_url,
        f.disponivel,
        f.certificado_verificado
    FROM fotografos f
    JOIN utilizadores u ON f.utilizador_id = u.id
    WHERE f.disponivel = 1 AND f.certificado_verificado = 1
";

$params = [];
$types = '';

// Filtrar por especialidade
if (!empty($especialidade)) {
    $sql .= " AND f.especialidades LIKE ?";
    $especialidade_param = '%' . $especialidade . '%';
    $params[] = $especialidade_param;
    $types .= 's';
}

// Filtrar por preço máximo
if ($preco_max !== null) {
    $sql .= " AND f.preco_hora <= ?";
    $params[] = $preco_max;
    $types .= 'd';
}

// Ordenar resultados
switch ($ordem) {
    case 'avaliacoes':
        $sql .= " ORDER BY f.avaliacoes_media DESC, f.numero_avaliacoes DESC";
        break;
    case 'preco':
        $sql .= " ORDER BY f.preco_hora ASC";
        break;
    case 'nome':
        $sql .= " ORDER BY u.nome_completo ASC";
        break;
}

// Limitar resultados
$sql .= " LIMIT 50";

// Preparar e executar query
$stmt = $conn->prepare($sql);

if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    response('success', 'Nenhum fotógrafo disponível com esses critérios', []);
    $stmt->close();
    mysqli_close($conn);
    exit;
}

$fotografos = [];

while ($row = $result->fetch_assoc()) {
    // Buscar disponibilidades (próximos 7 dias)
    $stmt_disp = $conn->prepare("
        SELECT DISTINCT DATE(data) as data_disponivel
        FROM disponibilidades
        WHERE fotografo_id = ? 
        AND data >= CURDATE() 
        AND data <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        AND reservado = 0
        ORDER BY data
        LIMIT 5
    ");
    $stmt_disp->bind_param("i", $row['id']);
    $stmt_disp->execute();
    $disp_result = $stmt_disp->get_result();
    
    $disponibilidades = [];
    while ($disp = $disp_result->fetch_assoc()) {
        $disponibilidades[] = $disp['data_disponivel'];
    }
    $stmt_disp->close();
    
    // Processar especialidades
    $especialidades = array_map('trim', explode(',', $row['especialidades']));
    
    // Preparar resposta
    $fotografos[] = [
        'id' => (int)$row['id'],
        'utilizador_id' => (int)$row['utilizador_id'],
        'nome' => $row['nome_completo'],
        'foto_perfil' => $row['foto_perfil'],
        'bio' => $row['bio'],
        'especialidades' => $especialidades,
        'preco_hora' => (float)$row['preco_hora'],
        'avaliacoes' => [
            'media' => round((float)$row['avaliacoes_media'], 1),
            'total' => (int)$row['numero_avaliacoes']
        ],
        'bio_profissional' => $row['bio_profissional'],
        'portfolio_url' => $row['portfolio_url'],
        'disponivel' => (bool)$row['disponivel'],
        'certificado_verificado' => (bool)$row['certificado_verificado'],
        'proximas_datas' => $disponibilidades
    ];
}

$stmt->close();

// Retornar sucesso
response('success', 'Fotógrafos carregados com sucesso', [
    'total' => count($fotografos),
    'fotografos' => $fotografos,
    'filtros' => [
        'especialidade' => $especialidade ? $especialidade : 'nenhum',
        'preco_maximo' => $preco_max ? $preco_max : 'sem limite',
        'ordenado_por' => $ordem
    ]
]);

mysqli_close($conn);
?>
