<?php
// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Obter os dados do formulário
$fornecedor = $_POST['Fornecedor'] ?? '';

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Sanitizar os dados de entrada para evitar injeção de SQL
$fornecedor = $conn->real_escape_string($fornecedor);

// Verificar se o fornecedor já existe na tabela
$sql_check = "SELECT FORNECEDOR FROM FORNECEDOR WHERE FORNECEDOR = '$fornecedor'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    // Se o fornecedor já existe, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateFornecedorExistente.php?erro=Não foi possivel realizar o cadastro. O fornecedor já está cadastrado no sistema");
    exit(); // Termina a execução do script após redirecionamento
} else {
    // Construir a consulta SQL para inserção
    $sql = "INSERT INTO FORNECEDOR (FORNECEDOR) VALUES ('$fornecedor')";

    // Executar a consulta SQL e verificar o resultado
    if (mysqli_query($conn, $sql)) {
        // Redirecionar para a página de sucesso
        header("Location: ../ViewSucess/SucessCreateFornecedor.php?sucesso=O cadastro do fornecedor foi realizado com sucesso");
        exit(); // Termina a execução do script após redirecionamento
    } else {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateFornecedor.php?erro=Não foi possivel realizar o cadastro do fornecedor. Tente novamente");
        exit(); // Termina a execução do script após redirecionamento
    }
}

// Fechar a conexão
$conn->close();
?>
