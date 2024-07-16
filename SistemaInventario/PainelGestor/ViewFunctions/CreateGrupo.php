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
$grupo = $_POST['Grupo'] ?? '';

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Converter o valor do grupo para letras maiúsculas, lidando com caracteres acentuados
$grupo = mb_strtoupper($grupo, 'UTF-8');

// Sanitizar os dados de entrada para evitar injeção de SQL usando prepared statement
$sql_check = "SELECT GRUPO FROM GRUPO WHERE GRUPO = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $grupo);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Se o grupo já existe, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateGrupoExistente.php?erro=" . urlencode("Não foi possível realizar o cadastro. O grupo já está cadastrado"));
    exit(); // Termina a execução do script após redirecionamento
} else {
    // Construir a consulta SQL para inserção usando prepared statement
    $sql_insert = "INSERT INTO GRUPO (GRUPO) VALUES (?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("s", $grupo);

    // Executar a consulta SQL
    if ($stmt_insert->execute()) {
        // Redirecionar para a página de sucesso
        header("Location: ../ViewSucess/SucessCreateGrupo.php?sucesso=" . urlencode("O cadastro do grupo foi realizado com sucesso"));
        exit(); // Termina a execução do script após redirecionamento
    } else {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateGrupo.php?erro=" . urlencode("Não foi possível realizar o cadastro do grupo. Refaça a operação e tente novamente"));
        exit(); // Termina a execução do script após redirecionamento
    }
}

// Fechar os statements
$stmt_check->close();
$stmt_insert->close();

// Fechar a conexão
$conn->close();
?>
