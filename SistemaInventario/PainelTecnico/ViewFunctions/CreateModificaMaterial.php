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

// Obter o ID do parâmetro POST e sanitizar
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

// Verificar se o ID não está vazio e se os dados foram enviados via POST
if (!empty($id) && isset($_POST['Material'])) {
    // Sanitizar o valor do material
    $material = $conn->real_escape_string($_POST['Material']);

    // Construir a consulta SQL para atualização (usando prepared statement para segurança adicional)
    $sql = "UPDATE MATERIAL SET MATERIAL=? WHERE IDMATERIAL=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $material, $id);

    // Executar a consulta preparada
    if ($stmt->execute()) {
        // Redirecionar para a página de sucesso se a atualização foi bem-sucedida
        header("Location: ../ViewSucess/SucessCreateModificaMaterial.php?sucesso=" . urlencode("A alteração foi realizada com sucesso no cadastro do material"));
        exit(); // Termina a execução do script após o redirecionamento
    } else {
        // Redirecionar para a página de falha se houver erro na atualização
        error_log("Erro na atualização do material: " . $stmt->error);
        header("Location: ../ViewFail/FailCreateModificaMaterial.php?erro=" . urlencode("Não foi possível realizar a alteração no cadastro do material. Refaça a operação e tente novamente"));
        exit(); // Termina a execução do script após o redirecionamento
    }

    // Fechar o statement
    $stmt->close();
} else {
    // Redirecionar para a página de falha se o ID estiver vazio ou se o campo Material não foi enviado via POST
    error_log("ID vazio ou campo Material não enviado");
    header("Location: ../ViewFail/FailCreateModificaMaterial.php?erro=" . urlencode("Não foi possível realizar a alteração no cadastro do material. Refaça a operação e tente novamente"));
    exit(); // Termina a execução do script após o redirecionamento
}

// Fechar a conexão
$conn->close();
?>
