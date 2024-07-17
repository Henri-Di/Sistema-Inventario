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
$material = $_POST['Material'] ?? '';

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Converter o valor do material para letras maiúsculas, lidando com caracteres acentuados
$material = mb_strtoupper($material, 'UTF-8');

// Sanitizar os dados de entrada para evitar injeção de SQL usando prepared statement
$sql_check = "SELECT MATERIAL FROM MATERIAL WHERE MATERIAL = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $material);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Se o material já existe, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateMaterialExistente.php?erro=" . urlencode("Não foi possível realizar o cadastro. O material já está cadastrado no sistema"));
    exit(); // Termina a execução do script após redirecionamento
} else {
    // Construir a consulta SQL para inserção usando prepared statement
    $sql_insert = "INSERT INTO MATERIAL (MATERIAL) VALUES (?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("s", $material);

    // Executar a consulta SQL
    if ($stmt_insert->execute()) {
        // Redirecionar para a página de sucesso
        header("Location: ../ViewSucess/SucessCreateMaterial.php?sucesso=" . urlencode("O cadastro do material foi realizado com sucesso"));
        exit(); // Termina a execução do script após redirecionamento
    } else {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateMaterial.php?erro=" . urlencode("Não foi possível realizar o cadastro do material"));
        exit(); // Termina a execução do script após redirecionamento
    }
}

// Fechar os statements
$stmt_check->close();
$stmt_insert->close();

// Fechar a conexão
$conn->close();
?>
