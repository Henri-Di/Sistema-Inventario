<?php
// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Obter o ID do parâmetro POST e sanitizar
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

// Verificar se o ID não está vazio e se os dados foram enviados via POST
if (!empty($id) && isset($_POST['Material'])) {
    // Sanitizar o valor do material
    $material = $conn->real_escape_string($_POST['Material']);

    // Construir a consulta SQL para atualização
    $sql = "UPDATE MATERIAL SET MATERIAL='$material' WHERE IDMATERIAL='$id'";

    // Executar a consulta SQL
    $update = mysqli_query($conn, $sql);

    // Verificar se a atualização foi bem-sucedida
    if ($update) {
        // Redirecionar para a página de sucesso se a atualização foi bem-sucedida
        header("Location: ../ViewSucess/SucessCreateModificaMaterial.php?sucesso=A alteração foi realizada com sucesso no cadastro do material");
        exit(); // Termina a execução do script após o redirecionamento
    } else {
        // Redirecionar para a página de falha se houver erro na atualização
        error_log("Erro na atualização do material: " . mysqli_error($conn));
        header("Location: ../ViewFail/FailCreateModificaMaterial.php?erro=Não foi possivel realizar a alteração no cadastro do material");
        exit(); // Termina a execução do script após o redirecionamento
    }
} else {
    // Redirecionar para a página de falha se o ID estiver vazio ou se o campo Material não foi enviado via POST
    error_log("ID vazio ou campo Material não enviado");
    header("Location: ../ViewFail/FailCreateModificaMaterial.php?erro=Não foi possivel realizar a alteração no cadastro do material");
    exit(); // Termina a execução do script após o redirecionamento
}

// Fechar a conexão
$conn->close();
?>
