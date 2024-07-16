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
$modelo = $_POST['Modelo'] ?? '';

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Converter o modelo para letras maiúsculas, lidando com caracteres acentuados
$modelo = mb_strtoupper($modelo, 'UTF-8');

// Sanitizar os dados de entrada para evitar injeção de SQL usando prepared statement
$stmt_check = $conn->prepare("SELECT MODELO FROM MODELO WHERE MODELO = ?");
$stmt_check->bind_param("s", $modelo);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Se o modelo já existe, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateModeloExistente.php?erro=" . urlencode("Não foi possível realizar o cadastro. O modelo já está cadastrado no sistema"));
    exit(); // Termina a execução do script após redirecionamento
} else {
    // Se não existe, construir a consulta SQL para inserção usando prepared statement
    $stmt_insert = $conn->prepare("INSERT INTO MODELO (MODELO) VALUES (?)");
    $stmt_insert->bind_param("s", $modelo);

    // Executar a consulta SQL e verificar o resultado
    if ($stmt_insert->execute()) {
        // Redirecionar para a página de sucesso
        header("Location: ../ViewSucess/SucessCreateModelo.php?sucesso=" . urlencode("O cadastro do modelo foi realizado com sucesso"));
        exit(); // Termina a execução do script após redirecionamento
    } else {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateModelo.php?erro=" . urlencode("Não foi possível realizar o cadastro do modelo. Tente novamente"));
        exit(); // Termina a execução do script após redirecionamento
    }
}

// Fechar os statements
$stmt_check->close();
$stmt_insert->close();

// Fechar a conexão
$conn->close();
?>
