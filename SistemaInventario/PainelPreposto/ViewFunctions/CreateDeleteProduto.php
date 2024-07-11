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
            header("Location: ../ViewFail/FailCreateRemoverDadosEstoque.php?erro=Não foi possivel realizar a remoção do produto. Tente novamente");
            exit(); // Termina a execução do script após redirecionamento
        } finally {
        }

        if (!mysqli_query($conn, $sqlDeleteAcrescimo)) {
            header("Location: ../ViewFail/FailCreateRemoverDadosAcrescimo.php?erro=Não foi possivel realizar a remoção do produto. Tente novamente");
            exit(); // Termina a execução do script após redirecionamento
        } finally {
        }

        if (!mysqli_query($conn, $sqlDeleteSubtracao)) {
            header("Location: ../ViewFail/FailCreateRemoverDadosSubtracao.php?erro=Não foi possivel realizar a remoção do produto. Tente novamente");
            exit(); // Termina a execução do script após redirecionamento
        } finally {
        }

        if (!mysqli_query($conn, $sqlDeleteSobrepor)) {
            header("Location: ../ViewFail/FailCreateRemoverDadosSobrepor.php?erro=Não foi possivel realizar a remoção do produto. Tente novamente");
            exit(); // Termina a execução do script após redirecionamento
        } finally {
        }

        if (!mysqli_query($conn, $sqlDeleteReserva)) {
            header("Location: ../ViewFail/FailCreateRemoverDadosReserva.php?erro=Não foi possivel realizar a remoção do produto. Tente novamente");
            exit(); // Termina a execução do script após redirecionamento
        } finally {
        }

        if (!mysqli_query($conn, $sqlDeleteDevolver)) {
            header("Location: ../ViewFail/FailCreateRemoverDadosDevolver.php?erro=Não foi possivel realizar a remoção do produto. Tente novamente");
            exit(); // Termina a execução do script após redirecionamento
        } finally {
        }

        if (!mysqli_query($conn, $sqlDeleteInutilizar)) {
            header("Location: ../ViewFail/FailCreateRemoverDadosInutilizar.php?erro=Não foi possivel realizar a remoção do produto. Tente novamente");
            exit(); // Termina a execução do script após redirecionamento
        } finally {
        }

        if (!mysqli_query($conn, $sqlDeleteTransferir)) {
            header("Location: ../ViewFail/FailCreateRemoverDadosTransferir.php?erro=Não foi possivel realizar a remoção do produto. Tente novamente");
            exit(); // Termina a execução do script após redirecionamento
        } finally {
        }

        if (!mysqli_query($conn, $sqlDeleteNotaFiscal)) {
            header("Location: ../ViewFail/FailCreateRemoverDadosNotaFiscal.php?erro=Não foi possivel realizar a remoção do produto. Tente novamente");
            exit(); // Termina a execução do script após redirecionamento
        } finally {
        }

        // Construir a consulta SQL para exclusão do produto
        $sqlDeleteProduto = "DELETE FROM PRODUTO WHERE IDPRODUTO = '$id'";

        // Executar a consulta SQL para exclusão do produto
        if (!mysqli_query($conn, $sqlDeleteProduto) || mysqli_affected_rows($conn) <= 0) {
            header("Location: ../ViewFail/FailCreateDeleteProduto.php?erro=Não foi possivel realizar a remoção do produto. Tente novamente");
            exit(); // Termina a execução do script após redirecionamento
        } finally {
        }

        // Commit da transação se todas as operações foram bem-sucedidas
        $conn->commit();

        // Redirecionar para a página de sucesso com o ID do produto excluído
        header("Location: ../ViewSuccess/SuccessCreateDeleteProduto.php?sucesso=O produto removido com sucesso");
        exit(); // Termina a execução do script após redirecionamento
    } catch (Exception $e) {
        // Em caso de erro, fazer rollback da transação
        $conn->rollback();

        // Redirecionar para a página de falha com mensagem de erro
        header("Location: ../ViewFail/FailCreateDeleteProduto.php?erro=Não foi possivel realizar a remoção do produto. Tente novamente");
        exit(); // Termina a execução do script após redirecionamento
    } finally {
        // Fechar a conexão
        $conn->close();
    }
} else {
    // Redirecionar para a página de falha se o ID estiver vazio
    header("Location: ../ViewFail/FailCreateDeleteProduto.php?erro=Não foi possivel realizar a remoção do produto. Tente novamente");
    exit(); // Termina a execução do script após redirecionamento
}
?>
