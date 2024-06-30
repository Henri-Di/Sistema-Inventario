<?php
// Conexão e consulta ao banco de dados
 require_once('../../ViewConnection/ConnectionInventario.php');

// Obter os dados do formulário
$modelo = $_POST['Modelo'] ?? '';

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Sanitizar os dados de entrada para evitar injeção de SQL
$modelo = $conn->real_escape_string($modelo);

// Verificar se o modelo já existe na tabela
$sql_check = "SELECT MODELO FROM MODELO WHERE MODELO = '$modelo'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    // Se o modelo já existe, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateModeloExistente.php?erro=Não foi possível realizar o cadastro. Modelo já cadastrado");
    exit(); // Termina a execução do script após redirecionamento
} else {
    // Construir a consulta SQL para inserção
    $sql = "INSERT INTO MODELO (MODELO) VALUES ('$modelo')";

    // Executar a consulta SQL e verificar o resultado
    if (mysqli_query($conn, $sql)) {
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
