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
$idusuario = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

// Verificar se o ID não está vazio e se os dados foram enviados via POST
if (!empty($idusuario) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Construir a consulta SQL para remoção (usando prepared statement para segurança adicional)
    $sql = "DELETE FROM USUARIO WHERE IDUSUARIO=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idusuario);

    // Executar a consulta preparada
    if ($stmt->execute()) {
        // Redirecionar para a página de sucesso se a exclusão foi bem-sucedida
        header("Location: ../ViewSucess/SucessCreateDeleteUsuario.php?sucesso=" . urlencode("O usuário foi removido com sucesso"));
        exit(); // Termina a execução do script após o redirecionamento
    } else {
        // Redirecionar para a página de falha se houver erro na exclusão
        error_log("Erro na exclusão do usuário: " . $stmt->error);
        header("Location: ../ViewFail/FailCreateDeleteUsuario.php?erro=" . urlencode("Não foi possível realizar a remoção do usuário. Refaça a operação e tente novamente"));
        exit(); // Termina a execução do script após o redirecionamento
    }

    // Fechar o statement
    $stmt->close();
} else {
    // Redirecionar para a página de falha se o ID estiver vazio ou se os campos necessários não foram enviados via POST
    header("Location: ../ViewFail/FailDeleteUsuario.php?erro=" . urlencode("Não foi possível realizar a remoção do usuário. Refaça a operação e tente novamente"));
    exit(); // Termina a execução do script após o redirecionamento
}

// Fechar a conexão
$conn->close();
?>
