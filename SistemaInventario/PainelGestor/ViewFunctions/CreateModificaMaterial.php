<?php
// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Obter o ID do parâmetro GET e sanitizar
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Verificar se o ID não está vazio e se os dados foram enviados via POST
if (!empty($id) && isset($_POST['Material'])) {
    // Sanitizar o valor do material
    $material = $conn->real_escape_string($_POST['Material']);

    // Construir a consulta SQL para atualização
    $sql = "UPDATE MATERIAL SET MATERIAL='$material' WHERE id='$id'";

    // Executar a consulta SQL
    $update = mysqli_query($conn, $sql);

    // Verificar se a atualização foi bem-sucedida
    if ($update) {
        // Redirecionar para a página de sucesso se a atualização foi bem-sucedida
        header("Location: ../ViewSucess/SucessCreateModificaProduto.php");
        exit(); // Termina a execução do script após o redirecionamento
    } else {
        // Redirecionar para a página de falha se houver erro na atualização
        header("Location: ../ViewFail/FailCreateModificaProduto.php?erro=Não foi possivel realizar a alteração no cadastro");
        exit(); // Termina a execução do script após o redirecionamento
    }
} else {
    // Redirecionar para a página de falha se o ID estiver vazio ou se o campo Material não foi enviado via POST
    header("Location: ../ViewFail/FailCreateModificaProduto.php?erro=Não foi possivel realizar a alteração no cadastro");
    exit(); // Termina a execução do script após o redirecionamento
}

// Fechar a conexão
$conn->close();
?>
