<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Setup MOZE - Criar Estrutura</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .step { margin: 20px 0; padding: 10px; border-left: 4px solid #007bff; }
        .status { padding: 5px; margin: 5px 0; }
    </style>
</head>
<body>
    <h1>🔧 Setup MOZE - Criar Estrutura da Base de Dados</h1>";

// ============================================
// Passo 1: Criar campos de certificado
// ============================================
echo "<div class='step'><h2>Passo 1: Adicionar campos de certificado</h2>";

$sql_queries = [
    "ALTER TABLE fotografos ADD COLUMN certificado VARCHAR(255) AFTER disponivel",
    "ALTER TABLE fotografos ADD COLUMN certificado_verificado BOOLEAN DEFAULT FALSE AFTER certificado"
];

foreach ($sql_queries as $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "<div class='status'><span class='success'>✅ Query executada com sucesso</span></div>";
    } else {
        // Verificar se o campo já existe (erro esperado)
        if (strpos(mysqli_error($conn), 'Duplicate column name') !== false) {
            echo "<div class='status'><span style='color: orange;'>⚠️ Campo já existe (ignorado)</span></div>";
        } else {
            echo "<div class='status'><span class='error'>❌ Erro: " . mysqli_error($conn) . "</span></div>";
        }
    }
}

echo "</div>";

// ============================================
// Passo 2: Criar pastas de uploads
// ============================================
echo "<div class='step'><h2>Passo 2: Criar pastas para uploads</h2>";

$pasta_uploads = '../uploads/';
$pasta_certificados = '../uploads/certificados/';

// Criar pasta uploads
if (!is_dir($pasta_uploads)) {
    if (mkdir($pasta_uploads, 0755, true)) {
        echo "<div class='status'><span class='success'>✅ Pasta /uploads/ criada</span></div>";
    } else {
        echo "<div class='status'><span class='error'>❌ Erro ao criar /uploads/</span></div>";
    }
} else {
    echo "<div class='status'><span style='color: orange;'>⚠️ Pasta /uploads/ já existe</span></div>";
}

// Criar pasta certificados
if (!is_dir($pasta_certificados)) {
    if (mkdir($pasta_certificados, 0755, true)) {
        echo "<div class='status'><span class='success'>✅ Pasta /uploads/certificados/ criada</span></div>";
    } else {
        echo "<div class='status'><span class='error'>❌ Erro ao criar /uploads/certificados/</span></div>";
    }
} else {
    echo "<div class='status'><span style='color: orange;'>⚠️ Pasta /uploads/certificados/ já existe</span></div>";
}

echo "</div>";

// ============================================
// Passo 3: Verificar tabelas
// ============================================
echo "<div class='step'><h2>Passo 3: Verificar tabelas criadas</h2>";

$result = mysqli_query($conn, "SHOW TABLES");
$tabelas = [];
while ($row = mysqli_fetch_row($result)) {
    $tabelas[] = $row[0];
}

$tabelas_esperadas = [
    'utilizadores',
    'clientes',
    'fotografos',
    'disponibilidades',
    'reservas',
    'avaliacoes',
    'mensagens'
];

foreach ($tabelas_esperadas as $tabela) {
    if (in_array($tabela, $tabelas)) {
        echo "<div class='status'><span class='success'>✅ Tabela '$tabela' existe</span></div>";
    } else {
        echo "<div class='status'><span class='error'>❌ Tabela '$tabela' não encontrada</span></div>";
    }
}

echo "</div>";

// ============================================
// Passo 4: Verificar campos na tabela fotografos
// ============================================
echo "<div class='step'><h2>Passo 4: Verificar campos na tabela fotografos</h2>";

$result = mysqli_query($conn, "DESCRIBE fotografos");
$campos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $campos[] = $row['Field'];
}

$campos_esperados = [
    'id',
    'utilizador_id',
    'especialidades',
    'preco_hora',
    'avaliacoes_media',
    'numero_avaliacoes',
    'bio_profissional',
    'portfolio_url',
    'disponivel',
    'certificado',
    'certificado_verificado'
];

foreach ($campos_esperados as $campo) {
    if (in_array($campo, $campos)) {
        echo "<div class='status'><span class='success'>✅ Campo '$campo' existe</span></div>";
    } else {
        echo "<div class='status'><span class='error'>❌ Campo '$campo' não encontrado</span></div>";
    }
}

echo "</div>";

// ============================================
// Conclusão
// ============================================
echo "<div class='step' style='border-left: 4px solid green;'>
    <h2>🎉 Setup Concluído!</h2>
    <p>Sua base de dados MOZE está pronta para usar!</p>
    <p><strong>Próximos passos:</strong></p>
    <ul>
        <li>Registar utilizadores via <code>/api/register.php</code></li>
        <li>Fazer login via <code>/api/login.php</code></li>
        <li>Upload de certificados via <code>/api/upload_certificado.php</code></li>
    </ul>
    <p><a href='test_connection.php'>← Voltar ao teste de conexão</a></p>
</div>";

echo "</body>
</html>";

mysqli_close($conn);
?>
