<?php
// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Obter o ID do parâmetro GET e sanitizar
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Verificar se o ID não está vazio
if (!empty($id)) {
    // Verificar a conexão com o banco de dados
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Construir a consulta SQL para exclusão
    $sql = "DELETE FROM MATERIAL WHERE id = '$id'";

    // Executar a consulta SQL e verificar o resultado
    if (mysqli_query($conn, $sql) && mysqli_affected_rows($conn) > 0) {
        // Redirecionar para a página de sucesso
        header("Location: ../ViewSucess/SucessCreateDeleteMaterial.php?O material foi removido com sucesso");
        exit(); // Termina a execução do script após redirecionamento
    } else {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateDeleteMaterial.php?erro=Não foi possivel remover o material. Tente novamente");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Fechar a conexão
    $conn->close();
} else {
    // Redirecionar para a página de falha se o ID estiver vazio
    header("Location: ../ViewFail/FailCreateDeleteMaterial.php?erro=Não foi possivel remover o material. Tente novamente");
    exit(); // Termina a execução do script após redirecionamento
}
?>
