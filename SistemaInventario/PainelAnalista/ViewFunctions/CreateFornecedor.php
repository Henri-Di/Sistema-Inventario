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
$fornecedor = $_POST['Fornecedor'] ?? '';

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Sanitizar os dados de entrada para evitar injeção de SQL usando prepared statement
$stmt_check = $conn->prepare("SELECT FORNECEDOR FROM FORNECEDOR WHERE FORNECEDOR = ?");
$stmt_check->bind_param("s", $fornecedor);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Se o fornecedor já existe, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateFornecedorExistente.php?erro=" . urlencode("Não foi possível realizar o cadastro. O fornecedor já está cadastrado no sistema"));
    exit(); // Termina a execução do script após redirecionamento
} else {
    // Construir a consulta SQL para inserção usando prepared statement
    $stmt_insert = $conn->prepare("INSERT INTO FORNECEDOR (FORNECEDOR) VALUES (?)");
    $stmt_insert->bind_param("s", $fornecedor);

    // Executar a consulta SQL e verificar o resultado
    if ($stmt_insert->execute()) {
        // Redirecionar para a página de sucesso
        header("Location: ../ViewSucess/SucessCreateFornecedor.php?sucesso=" . urlencode("O cadastro do fornecedor foi realizado com sucesso"));
        exit(); // Termina a execução do script após redirecionamento
    } else {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateFornecedor.php?erro=" . urlencode("Não foi possível realizar o cadastro do fornecedor. Tente novamente"));
        exit(); // Termina a execução do script após redirecionamento
    }
}

// Fechar os statements
$stmt_check->close();
$stmt_insert->close();

// Fechar a conexão
$conn->close();
?>
