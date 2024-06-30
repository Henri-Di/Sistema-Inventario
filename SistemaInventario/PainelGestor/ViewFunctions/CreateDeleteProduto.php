<?php
// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Obter o ID do parâmetro GET e sanitizar
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Verificar se o ID não está vazio
if (!empty($id)) {
    // Iniciar transação para garantir consistência
    $conn->begin_transaction();

    try {
        // Construir as consultas SQL para exclusão nas tabelas relacionadas
        $sqlDeleteEstoque = "DELETE FROM ESTOQUE WHERE IDPRODUTO = '$id'";
        $sqlDeleteAcrescimo = "DELETE FROM ACRESCIMO WHERE IDPRODUTO = '$id'";
        $sqlDeleteSubtracao = "DELETE FROM SUBTRACAO WHERE IDPRODUTO = '$id'";
        $sqlDeleteSobrepor = "DELETE FROM SOBREPOR WHERE IDPRODUTO = '$id'";
        $sqlDeleteReserva = "DELETE FROM RESERVA WHERE IDPRODUTO = '$id'";
        $sqlDeleteDevolver = "DELETE FROM DEVOLVER WHERE IDPRODUTO = '$id'";
        $sqlDeleteInutilizar = "DELETE FROM INUTILIZAR WHERE IDPRODUTO = '$id'";
        $sqlDeleteTransferir = "DELETE FROM TRANSFERENCIA WHERE IDPRODUTO = '$id'";
        $sqlDeleteNotaFiscal = "DELETE FROM NOTAFISCAL WHERE IDPRODUTO = '$id'";

        // Executar as consultas SQL para exclusão nas tabelas relacionadas
        if (!mysqli_query($conn, $sqlDeleteEstoque)) {
            throw new Exception("Não foi possível remover os dados na tabela ESTOQUE");
        }

        if (!mysqli_query($conn, $sqlDeleteAcrescimo)) {
            throw new Exception("Não foi possível remover os dados na tabela ACRESCIMO");
        }

        if (!mysqli_query($conn, $sqlDeleteSubtracao)) {
            throw new Exception("Não foi possível remover os dados na tabela SUBTRACAO");
        }

        if (!mysqli_query($conn, $sqlDeleteSobrepor)) {
            throw new Exception("Não foi possível remover os dados na tabela SOBREPOR");
        }

        if (!mysqli_query($conn, $sqlDeleteReserva)) {
            throw new Exception("Não foi possível remover os dados na tabela RESERVA");
        }

        if (!mysqli_query($conn, $sqlDeleteDevolver)) {
            throw new Exception("Não foi possível remover os dados na tabela DEVOLVER");
        }

        if (!mysqli_query($conn, $sqlDeleteInutilizar)) {
            throw new Exception("Não foi possível remover os dados na tabela INUTILIZAR");
        }

        if (!mysqli_query($conn, $sqlDeleteTransferir)) {
            throw new Exception("Não foi possível remover os dados na tabela TRANSFERENCIA");
        }

        if (!mysqli_query($conn, $sqlDeleteNotaFiscal)) {
            throw new Exception("Não foi possível remover os dados na tabela NOTAFISCAL");
        }

        // Construir a consulta SQL para exclusão do produto
        $sqlDeleteProduto = "DELETE FROM PRODUTO WHERE IDPRODUTO = '$id'";

        // Executar a consulta SQL para exclusão do produto
        if (!mysqli_query($conn, $sqlDeleteProduto) || mysqli_affected_rows($conn) <= 0) {
            throw new Exception("Não foi possível remover o produto");
        }

        // Commit da transação se todas as operações foram bem-sucedidas
        $conn->commit();

        // Redirecionar para a página de sucesso com o ID do produto excluído
        header("Location: ../ViewSuccess/SuccessCreateDeleteProduto.php?id=$id");
        exit(); // Termina a execução do script após redirecionamento
    } catch (Exception $e) {
        // Em caso de erro, fazer rollback da transação
        $conn->rollback();

        // Redirecionar para a página de falha com mensagem de erro
        header("Location: ../ViewFail/FailCreateDeleteProduto.php?erro=" . urlencode($e->getMessage()));
        exit(); // Termina a execução do script após redirecionamento
    } finally {
        // Fechar a conexão
        $conn->close();
    }
} else {
    // Redirecionar para a página de falha se o ID estiver vazio
    header("Location: ../ViewFail/FailCreateDeleteProduto.php?erro=ID do produto não especificado");
    exit(); // Termina a execução do script após redirecionamento
}
?>
