<?php
// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Obter os dados do formulário
$metragem = $_POST['Metragem'] ?? '';

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Sanitizar os dados de entrada para evitar injeção de SQL
$metragem = $conn->real_escape_string($metragem);

// Construir a consulta SQL para verificar duplicidade
$sql_check = "SELECT * FROM METRAGEM WHERE METRAGEM = '$metragem'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    // Se já existe uma metragem igual, redirecionar para a página de falha de duplicidade
    header("Location: ../ViewFail/FailCreateMetragemExistente.php?erro=Não foi possível realizar o cadastro. Metragem já cadastrada");
    exit(); // Termina a execução do script após redirecionamento
} else {
    // Se não existe, construir a consulta SQL para inserção
    $sql = "INSERT INTO METRAGEM (METRAGEM) VALUES ('$metragem')";

    // Executar a consulta SQL e verificar o resultado
    if ($conn->query($sql) === TRUE) {
        // Redirecionar para a página de sucesso
        header("Location: ../ViewSucess/SucessCreate.php");
        exit(); // Termina a execução do script após redirecionamento
    } else {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreate.php?erro=Não foi possível realizar o cadastro");
        exit(); // Termina a execução do script após redirecionamento
    }
}

// Fechar a conexão
$conn->close();
?>
