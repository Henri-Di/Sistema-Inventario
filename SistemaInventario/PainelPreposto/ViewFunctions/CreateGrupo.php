<?php
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

// Sanitizar os dados de entrada para evitar injeção de SQL
$grupo = $conn->real_escape_string($grupo);

// Verificar se o grupo já existe na tabela
$sql_check = "SELECT GRUPO FROM GRUPO WHERE GRUPO = '$grupo'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    // Se o grupo já existe, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateGrupoExistente.php?erro=Não foi possível realizar o cadastro. O grupo já está cadastrado");
    exit(); // Termina a execução do script após redirecionamento
} else {
    // Construir a consulta SQL para inserção
    $sql = "INSERT INTO GRUPO (GRUPO) VALUES ('$grupo')";

    // Executar a consulta SQL
    if (mysqli_query($conn, $sql)) {
        // Redirecionar para a página de sucesso
        header("Location: ../ViewSucess/SucessCreateGrupo.php?sucesso=O cadastro do grupo foi realizado com sucesso");
        exit(); // Termina a execução do script após redirecionamento
    } else {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateGrupo.php?erro=Não foi possível realizar o cadastro do grupo. Refaça a operação e tente novamente");
        exit(); // Termina a execução do script após redirecionamento
    }
}

// Fechar a conexão
$conn->close();
?>
