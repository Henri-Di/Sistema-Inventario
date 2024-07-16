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

// Obter o ID do parâmetro GET e sanitizar
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Verificar se o ID não está vazio e se os dados foram enviados via POST
if (!empty($id) && isset($_POST['Conector'])) {
    // Sanitizar o valor do conector
    $conector = $conn->real_escape_string($_POST['Conector']);

    // Construir a consulta SQL preparada para atualização
    $sql = "UPDATE CONECTOR SET CONECTOR = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    // Verificar se a preparação da consulta foi bem-sucedida
    if ($stmt) {
        // Bind dos parâmetros e execução da consulta preparada
        $stmt->bind_param("si", $conector, $id);
        $stmt->execute();

        // Verificar se a atualização foi bem-sucedida
        if ($stmt->affected_rows > 0) {
            // Redirecionar para a página de sucesso se a atualização foi bem-sucedida
            header("Location: ../ViewSucess/SucessCreateModificaConector.php?sucesso=" . urlencode("A alteração no cadastro do conector foi realizada com sucesso"));
            exit(); // Termina a execução do script após o redirecionamento
        } else {
            // Redirecionar para a página de falha se nenhum registro foi atualizado
            header("Location: ../ViewFail/FailCreateModificaConector.php?erro=" . urlencode("Não foi possível realizar a alteração no cadastro do conector. Refaça a operação e tente novamente"));
            exit(); // Termina a execução do script após o redirecionamento
        }
    } else {
        // Redirecionar para a página de falha se houver erro na preparação da consulta
        header("Location: ../ViewFail/FailCreateModificaConector.php?erro=" . urlencode("Não foi possível realizar a alteração no cadastro do conector. Refaça a operação e tente novamente"));
        exit(); // Termina a execução do script após o redirecionamento
    }
} else {
    // Redirecionar para a página de falha se o ID estiver vazio ou se o campo Conector não foi enviado via POST
    header("Location: ../ViewFail/FailCreateModificaConector.php?erro=" . urlencode("Não foi possível realizar a alteração no cadastro do conector. Refaça a operação e tente novamente"));
    exit(); // Termina a execução do script após o redirecionamento
}

// Fechar a conexão
$conn->close();
?>
