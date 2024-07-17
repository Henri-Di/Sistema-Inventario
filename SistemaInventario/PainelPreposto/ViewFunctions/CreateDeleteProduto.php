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

// Função para executar query com prepared statement
function executeQuery($conn, $sql, $params = null) {
    $stmt = $conn->prepare($sql);

    if ($params !== null) {
        $stmt->bind_param(...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

// Obter o ID do parâmetro GET e sanitizar
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Verificar se o ID não está vazio
if (!empty($id)) {
    // Iniciar transação para garantir consistência
    $conn->begin_transaction();

    try {
        // Construir as consultas SQL para exclusão nas tabelas relacionadas usando prepared statements
        $sqlDeleteEstoque = "DELETE FROM ESTOQUE WHERE IDPRODUTO = ?";
        executeQuery($conn, $sqlDeleteEstoque, ['i', $id]);

        $sqlDeleteAcrescimo = "DELETE FROM ACRESCIMO WHERE IDPRODUTO = ?";
        executeQuery($conn, $sqlDeleteAcrescimo, ['i', $id]);

        $sqlDeleteSubtracao = "DELETE FROM SUBTRACAO WHERE IDPRODUTO = ?";
        executeQuery($conn, $sqlDeleteSubtracao, ['i', $id]);

        $sqlDeleteSobrepor = "DELETE FROM SOBREPOR WHERE IDPRODUTO = ?";
        executeQuery($conn, $sqlDeleteSobrepor, ['i', $id]);

        $sqlDeleteReserva = "DELETE FROM RESERVA WHERE IDPRODUTO = ?";
        executeQuery($conn, $sqlDeleteReserva, ['i', $id]);

        $sqlDeleteDevolver = "DELETE FROM DEVOLVER WHERE IDPRODUTO = ?";
        executeQuery($conn, $sqlDeleteDevolver, ['i', $id]);

        $sqlDeleteInutilizar = "DELETE FROM INUTILIZAR WHERE IDPRODUTO = ?";
        executeQuery($conn, $sqlDeleteInutilizar, ['i', $id]);

        $sqlDeleteTransferir = "DELETE FROM TRANSFERENCIA WHERE IDPRODUTO = ?";
        executeQuery($conn, $sqlDeleteTransferir, ['i', $id]);

        $sqlDeleteNotaFiscal = "DELETE FROM NOTAFISCAL WHERE IDPRODUTO = ?";
        executeQuery($conn, $sqlDeleteNotaFiscal, ['i', $id]);

        // Construir a consulta SQL para exclusão do produto
        $sqlDeleteProduto = "DELETE FROM PRODUTO WHERE IDPRODUTO = ?";
        executeQuery($conn, $sqlDeleteProduto, ['i', $id]);

        // Commit da transação se todas as operações foram bem-sucedidas
        $conn->commit();

        // Redirecionar para a página de sucesso com o ID do produto excluído
        header("Location: ../ViewSuccess/SuccessCreateDeleteProduto.php?sucesso=" . urlencode("O produto foi removido com sucesso"));
        exit(); // Termina a execução do script após redirecionamento
    } catch (mysqli_sql_exception $e) {
        // Em caso de erro específico do MySQL, fazer rollback da transação
        $conn->rollback();

        // Redirecionar para a página de falha com mensagem de erro específica
        header("Location: ../ViewFail/FailCreateDeleteProduto.php?erro=" . urlencode("Erro no banco de dados: " . $e->getMessage()));
        exit(); // Termina a execução do script após redirecionamento
    } catch (Exception $e) {
        // Em caso de erro genérico, fazer rollback da transação
        $conn->rollback();

        // Redirecionar para a página de falha com mensagem de erro genérica
        header("Location: ../ViewFail/FailCreateDeleteProduto.php?erro=" . urlencode("Não foi possível remover o produto. Tente novamente"));
        exit(); // Termina a execução do script após redirecionamento
    } finally {
        // Fechar a conexão
        $conn->close();
    }
} else {
    // Redirecionar para a página de falha se o ID estiver vazio
    header("Location: ../ViewFail/FailCreateDeleteProduto.php?erro=" . urlencode("Não foi possível remover o produto. Tente novamente"));
    exit(); // Termina a execução do script após redirecionamento
}
?>
