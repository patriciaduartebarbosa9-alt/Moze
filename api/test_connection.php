<?php
// Ativa visualização de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Iniciando teste de conexão...<br><br>";

// Verifica se mysqli está disponível
if (!extension_loaded('mysqli')) {
    die("❌ Extensão MySQLi não está ativada no servidor!");
}

echo "✅ Extensão MySQLi disponível<br>";

$host = "sql109.infinityfree.com";
$user = "if0_40439565";
$pass = "Mozept123";
$db   = "if0_40439565_moze";

echo "Tentando conectar a: " . $host . "<br>";

// Tenta conectar
$conn = @mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    echo "❌ Erro na ligação: " . mysqli_connect_error() . "<br>";
    echo "Verifique:<br>";
    echo "- Host: " . $host . "<br>";
    echo "- User: " . $user . "<br>";
    echo "- Database: " . $db . "<br>";
    die();
}

echo "✅ Ligação bem-sucedida!<br><br>";

// Mostra base de dados ativa
$result = mysqli_query($conn, "SELECT DATABASE()");
$row = mysqli_fetch_row($result);
echo "Base de dados ativa: <strong>" . $row[0] . "</strong><br><br>";

// Listar tabelas
echo "<strong>Tabelas existentes:</strong><br>";
$result = mysqli_query($conn, "SHOW TABLES");

if (mysqli_num_rows($result) == 0) {
    echo "⚠️ Nenhuma tabela encontrada. A base de dados está vazia.<br>";
    echo "Você precisa executar o ficheiro <strong>database_structure.sql</strong> no phpMyAdmin.";
} else {
    while ($row = mysqli_fetch_row($result)) {
        echo "- " . $row[0] . "<br>";
    }
}

mysqli_close($conn);
?>

