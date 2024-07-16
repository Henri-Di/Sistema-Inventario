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
$metragem = $_POST['Metragem'] ?? '';

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Sanitizar os dados de entrada para evitar injeção de SQL usando prepared statement
$stmt_check = $conn->prepare("SELECT * FROM METRAGEM WHERE UPPER(METRAGEM) = ?");
$stmt_check->bind_param("s", $metragem);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Se já existe uma metragem igual, redirecionar para a página de falha de duplicidade
    header("Location: ../ViewFail/FailCreateMetragemExistente.php?erro=" . urlencode("Não foi possível realizar o cadastro da metragem. A metragem já está cadastrada no sistema"));
    exit(); // Termina a execução do script após redirecionamento
} else {
    // Se não existe, construir a consulta SQL para inserção usando prepared statement
    $stmt_insert = $conn->prepare("INSERT INTO METRAGEM (METRAGEM) VALUES (?)");
    $stmt_insert->bind_param("s", $metragem);

    // Executar a consulta SQL e verificar o resultado
    if ($stmt_insert->execute()) {
        // Redirecionar para a página de sucesso
        header("Location: ../ViewSucess/SucessCreateMetragem.php?sucesso=". urlencode("O cadastro da metragem foi realizado com sucesso"));
        exit(); // Termina a execução do script após redirecionamento
    } else {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateMetragem.php?erro=" . urlencode("Não foi possível realizar o cadastro da metragem. Tente novamente"));
        exit(); // Termina a execução do script após redirecionamento
    }
}

// Fechar os statements
$stmt_check->close();
$stmt_insert->close();

// Fechar a conexão
$conn->close();
?>
