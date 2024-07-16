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
$conector = $_POST['Conector'] ?? '';

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Converter o valor para letras maiúsculas, lidando com caracteres acentuados
$conector = mb_strtoupper($conector, 'UTF-8');

// Verificar se o conector está vazio
if (empty($conector)) {
    header("Location: ../ViewFail/FailCreateConectorVazio.php?erro=" . urlencode("O campo de conector não pode estar vazio."));
    exit();
}

// Verificar se o conector já existe na tabela usando prepared statements
$sql_check = "SELECT CONECTOR FROM CONECTOR WHERE CONECTOR = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $conector);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Se o conector já existe, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateConectorExistente.php?erro=" . urlencode("Não foi possível realizar o cadastro do conector. Conector já cadastrado no sistema"));
    exit();
} else {
    // Construir a consulta SQL para inserção usando prepared statements
    $sql_insert = "INSERT INTO CONECTOR (CONECTOR) VALUES (?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("s", $conector);

    // Executar a consulta SQL e verificar o resultado
    if ($stmt_insert->execute()) {
        // Redirecionar para a página de sucesso
        header("Location: ../ViewSucess/SucessCreateConector.php?sucesso=" . urlencode("O cadastro do conector foi realizado com sucesso"));
        exit();
    } else {
        // Registrar o erro no log
        error_log("Erro ao cadastrar conector: " . $stmt_insert->error);
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateConector.php?erro=" . urlencode("Não foi possível realizar o cadastro do conector. Tente novamente"));
        exit();
    }
}

// Fechar os statements e a conexão
$stmt_check->close();
if (isset($stmt_insert)) {
    $stmt_insert->close();
}
$conn->close();
?>
