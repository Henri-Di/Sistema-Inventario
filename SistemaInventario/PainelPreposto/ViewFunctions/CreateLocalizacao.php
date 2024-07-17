<?php
// Iniciar sessão se necessário
session_start();
session_regenerate_id(true);

// Adicionar cabeçalhos de segurança
header("Content-Security-Policy: default-src 'self'");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Obter os dados do formulário
$localizacao = $_POST['Localizacao'] ?? '';

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Converter o valor da localização para letras maiúsculas, lidando com caracteres acentuados
$localizacao = mb_strtoupper($localizacao, 'UTF-8');

// Sanitizar os dados de entrada para evitar injeção de SQL usando prepared statement
$sql_check = "SELECT LOCALIZACAO FROM LOCALIZACAO WHERE LOCALIZACAO = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $localizacao);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Se a localização já existe, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateLocalizacaoExistente.php?erro=" . urlencode("Não foi possível realizar o cadastro. A localização já está cadastrada no sistema"));
    exit(); // Termina a execução do script após redirecionamento
} else {
    // Construir a consulta SQL para inserção usando prepared statement
    $sql_insert = "INSERT INTO LOCALIZACAO (LOCALIZACAO) VALUES (?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("s", $localizacao);

    // Executar a consulta SQL
    if ($stmt_insert->execute()) {
        // Redirecionar para a página de sucesso
        header("Location: ../ViewSucess/SucessCreateLocalizacao.php?sucesso=" . urlencode("O cadastro da localização foi realizado com sucesso"));
        exit(); // Termina a execução do script após redirecionamento
    } else {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateLocalizacao.php?erro=" . urlencode("Não foi possível realizar o cadastro da localização. Refaça a operação e tente novamente"));
        exit(); // Termina a execução do script após redirecionamento
    }
}

// Fechar os statements
$stmt_check->close();
$stmt_insert->close();

// Fechar a conexão
$conn->close();
?>
