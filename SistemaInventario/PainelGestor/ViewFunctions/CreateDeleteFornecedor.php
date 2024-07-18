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

// Verificar se o ID não está vazio
if (!empty($id)) {
    // Verificar a conexão com o banco de dados
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Iniciar transação para garantir atomicidade
    $conn->begin_transaction();

    try {
        // Construir a consulta SQL para exclusão usando prepared statements
        $sql = "DELETE FROM FORNECEDOR WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        // Executar a consulta SQL e verificar o resultado
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            // Commit da transação se a exclusão for bem-sucedida
            $conn->commit();
            // Redirecionar para a página de sucesso
            header("Location: ../ViewSucess/SucessCreateDeleteFornecedor.php?sucesso=" . urlencode("O fornecedor foi removido com sucesso"));
            exit();
        } else {
            // Rollback da transação em caso de falha
            $conn->rollback();
            // Redirecionar para a página de falha
            header("Location: ../ViewFail/FailCreateDeleteFornecedor.php?erro=" . urlencode("Não foi possível remover o fornecedor. Refaça a operação e tente novamente"));
            exit();
        }
    } catch (Exception $e) {
        // Rollback da transação em caso de exceção
        $conn->rollback();
        // Redirecionar para a página de falha com mensagem de erro genérica
        header("Location: ../ViewFail/FailCreateDeleteFornecedor.php?erro=" . urlencode("Não foi possível remover o fornecedor. Refaça a operação e tente novamente"));
    } finally {
        // Fechar o statement
        $stmt->close();
    }
} else {
    // Redirecionar para a página de falha se o ID estiver vazio
    header("Location: ../ViewFail/FailCreateDeleteFornecedor.php?erro=" . urlencode("Não foi possível remover o fornecedor. Refaça a operação e tente novamente"));
    exit();
}

// Fechar a conexão
$conn->close();
?>
