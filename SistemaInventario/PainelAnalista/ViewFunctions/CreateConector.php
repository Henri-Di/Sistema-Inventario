<?php
// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Obter os dados do formulário
$conector = $_POST['Conector'] ?? '';

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Sanitizar os dados de entrada para evitar injeção de SQL
$conector = $conn->real_escape_string($conector);

// Converter o valor para letras maiúsculas
$conector = strtoupper($conector);

// Verificar se o conector já existe na tabela
$sql_check = "SELECT CONECTOR FROM CONECTOR WHERE CONECTOR = '$conector'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    // Se o conector já existe, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateConectorExistente.php?erro=Não foi possível realizar o cadastro do conector. Conector já cadastrado no sistema");
    exit(); // Termina a execução do script após redirecionamento
} else {
    // Construir a consulta SQL para inserção
    $sql = "INSERT INTO CONECTOR (CONECTOR) VALUES ('$conector')";

    // Executar a consulta SQL e verificar o resultado
    if (mysqli_query($conn, $sql)) {
        // Redirecionar para a página de sucesso
        header("Location: ../ViewSucess/SucessCreateConector.php?sucesso=O cadastro do conector foi realizado com sucesso");
        exit(); // Termina a execução do script após redirecionamento
    } else {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateConector.php?erro=Não foi possivel realizar o cadastro do conector. Tente novamente");
        exit(); // Termina a execução do script após redirecionamento
    }
}

// Fechar a conexão
$conn->close();
?>
