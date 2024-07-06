<?php
// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Obter os dados do formulário
$localizacao = $_POST['Localizacao'] ?? '';

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Converter o valor da localização para letras maiúsculas, lidando com caracteres acentuados
$localizacao = mb_strtoupper($localizacao, 'UTF-8');

// Sanitizar os dados de entrada para evitar injeção de SQL
$localizacao = $conn->real_escape_string($localizacao);

// Verificar se a localização já existe na tabela
$sql_check = "SELECT LOCALIZACAO FROM LOCALIZACAO WHERE LOCALIZACAO = '$localizacao'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    // Se a localização já existe, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateLocalizacaoExistente.php?erro=Não foi possível realizar o cadastro. Localização já está cadastrada");
    exit(); // Termina a execução do script após redirecionamento
} else {
    // Construir a consulta SQL para inserção
    $sql = "INSERT INTO LOCALIZACAO (LOCALIZACAO) VALUES ('$localizacao')";

    // Executar a consulta SQL
    if (mysqli_query($conn, $sql)) {
        // Redirecionar para a página de sucesso
        header("Location: ../ViewSucess/SucessCreateLocalizacao.php?sucesso=O cadastro da localização foi realizado com sucesso");
        exit(); // Termina a execução do script após redirecionamento
    } else {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateLocalizacao.php?erro=Não foi possível realizar o cadastro da localização. Refaça a operação e tente novamente");
        exit(); // Termina a execução do script após redirecionamento
    }
}

// Fechar a conexão
$conn->close();
?>
