<?php
// Iniciar sessão se necessário
session_start();

// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Verificar se os dados do usuário estão disponíveis na sessão
if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['usuarioNome']) || !isset($_SESSION['usuarioCodigoP'])) {
    header("Location: ../ViewFail/FailCreateUsuarioNaoAutenticado.php?erro=O usuário não está autenticado. Realize o login novamente");
    exit(); // Termina a execução do script após redirecionamento
}

// Obter os dados do usuário da sessão
$idUsuario = $_SESSION['usuarioId'];
$nomeUsuario = $_SESSION['usuarioNome'];
$codigoPUsuario = $_SESSION['usuarioCodigoP'];

// Verificar se o ID da transferência foi recebido via POST
if (!isset($_POST['idTransferencia'])) {
    header("Location: ../ViewFail/FailCreateLocalizaTransferencia.php?erro=A transferência de produtos indicada não foi encontrada");
    exit(); // Termina a execução do script após redirecionamento
}

// Obter o ID da transferência a partir dos dados recebidos
$idTransferencia = $_POST['idTransferencia'];

try {
    // Iniciar transação para garantir consistência
    $conn->begin_transaction();

    // Verificar se o usuário é o criador da transferência
    $sqlVerificaCriador = "SELECT IDUSUARIO, SITUACAO FROM TRANSFERENCIA WHERE ID = ?";
    $stmtVerificaCriador = $conn->prepare($sqlVerificaCriador);
    $stmtVerificaCriador->bind_param("i", $idTransferencia);
    $stmtVerificaCriador->execute();
    $stmtVerificaCriador->store_result();

    // Verificar se a transferência existe
    if ($stmtVerificaCriador->num_rows == 0) {
        header("Location: ../ViewFail/FailCreateLocalizaTransferencia.php?erro=A transferência de produtos indicada não foi encontrada");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Bind results
    $stmtVerificaCriador->bind_result($idUsuarioOrigem, $situacaoTransferencia);
    $stmtVerificaCriador->fetch();
    $stmtVerificaCriador->close();

    // Verificar se o usuário atual é o criador da transferência
    if ($idUsuario === $idUsuarioOrigem) {
        header("Location: ../ViewFail/FailCreateUsuarioAceitaTransferencia.php?erro=Você não pode aceitar ou recusar a transferência que você criou");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Verificar se a transferência já foi aceita ou recusada anteriormente
    if ($situacaoTransferencia !== 'Pendente') {
        header("Location: ../ViewFail/FailCreateTransferenciaProcessada.php?erro=Essa transferência já foi processada");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Verificar se a ação (aceitar ou recusar) foi enviada via POST
    if (!isset($_POST['Acao']) || ($_POST['Acao'] !== 'Aceitar' && $_POST['Acao'] !== 'Recusar')) {
        header("Location: ../ViewFail/FailAcaoInvalida.php?erro=Ação inválida especificada");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Obter a ação (aceitar ou recusar)
    $acao = $_POST['Acao'];

    // Obter dados da transferência para realizar operações
    $sqlSelectTransferencia = "SELECT QUANTIDADE, IDPRODUTO_ORIGEM, IDPRODUTO_DESTINO FROM TRANSFERENCIA WHERE ID = ?";
    $stmtSelect = $conn->prepare($sqlSelectTransferencia);
    $stmtSelect->bind_param("i", $idTransferencia);
    $stmtSelect->execute();
    $stmtSelect->store_result();

    // Verificar se a transferência existe
    if ($stmtSelect->num_rows == 0) {
        header("Location: ../ViewFail/FailCreateLocalizaTransferencia.php?erro=A transferência de produtos indicada não foi encontrada");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Bind results
    $stmtSelect->bind_result($quantidadeTransferida, $idProdutoOrigem, $idProdutoDestino);
    $stmtSelect->fetch();
    $stmtSelect->close();

    // Realizar ação com base na escolha do usuário
    if ($acao === 'Aceitar') {
        // Atualizar a situação da transferência para 'Recebido'
        $sqlUpdateAceitar = "UPDATE TRANSFERENCIA SET SITUACAO = 'Recebido' WHERE ID = ?";
        $stmtUpdate = $conn->prepare($sqlUpdateAceitar);
        $stmtUpdate->bind_param("i", $idTransferencia);
        $stmtUpdate->execute();

        // Verificar se a atualização foi bem-sucedida
        if ($stmtUpdate->affected_rows == 0) {
            header("Location: ../ViewFail/FailCreateSituacaoTransferenciaRecebida.php?erro=Erro ao atualizar a situação da transferência para Recebido");
            exit(); // Termina a execução do script após redirecionamento
        }
        $stmtUpdate->close();

        // Atualizar o estoque do produto destino com a quantidade transferida
        $sqlUpdateEstoqueDestino = "UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE + ? WHERE IDPRODUTO = ?";
        $stmtUpdateDestino = $conn->prepare($sqlUpdateEstoqueDestino);
        $stmtUpdateDestino->bind_param("ii", $quantidadeTransferida, $idProdutoDestino);
        $stmtUpdateDestino->execute();

        // Verificar se a atualização foi bem-sucedida
        if ($stmtUpdateDestino->affected_rows == 0) {
            header("Location: ../ViewFail/FailCreateAtualizaEstoqueDestinoTransferencia.php?erro=Não foi possível atualizar o estoque do produto de destino da transferência");
            exit(); // Termina a execução do script após redirecionamento
        }
        $stmtUpdateDestino->close();

        // Atualizar o estoque do produto de origem decrementando a quantidade transferida e a quantidade reservada
        $sqlUpdateEstoqueOrigem = "UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE - ?, RESERVADO = RESERVADO - ? WHERE IDPRODUTO = ?";
        $stmtUpdateOrigem = $conn->prepare($sqlUpdateEstoqueOrigem);
        $stmtUpdateOrigem->bind_param("iii", $quantidadeTransferida, $quantidadeTransferida, $idProdutoOrigem);
        $stmtUpdateOrigem->execute();

        // Verificar se a atualização foi bem-sucedida
        if ($stmtUpdateOrigem->affected_rows == 0) {
            header("Location: ../ViewFail/FailCreateAtualizaEstoqueOrigemTransferencia.php?erro=Erro ao atualizar a situação da transferência para Recebido");
            exit(); // Termina a execução do script após redirecionamento
        }
        $stmtUpdateOrigem->close();
    } elseif ($acao === 'Recusar') {
        // Atualizar a situação da transferência para 'Recusado'
        $sqlUpdateRecusar = "UPDATE TRANSFERENCIA SET SITUACAO = 'Recusado' WHERE ID = ?";
        $stmtUpdate = $conn->prepare($sqlUpdateRecusar);
        $stmtUpdate->bind_param("i", $idTransferencia);
        $stmtUpdate->execute();

        // Verificar se a atualização foi bem-sucedida
        if ($stmtUpdate->affected_rows == 0) {
            header("Location: ../ViewFail/FailCreateSituacaoTransferenciaRecusada.php?erro=Erro ao atualizar a situação da transferência para Recusado");
            exit(); // Termina a execução do script após redirecionamento
        }
        $stmtUpdate->close();

        // Remover a quantidade reservada do estoque do produto origem sem alterar a quantidade total
        $sqlUpdateEstoqueOrigem = "UPDATE ESTOQUE SET RESERVADO = RESERVADO - ? WHERE IDPRODUTO = ?";
        $stmtUpdateOrigem = $conn->prepare($sqlUpdateEstoqueOrigem);
        $stmtUpdateOrigem->bind_param("ii", $quantidadeTransferida, $idProdutoOrigem);
        $stmtUpdateOrigem->execute();

        // Verificar se a atualização foi bem-sucedida
        if ($stmtUpdateOrigem->affected_rows == 0) {
            header("Location: ../ViewFail/FailCreateAtualizaEstoqueOrigemTransferencia.php?erro=Erro ao remover a reserva do estoque do produto origem");
            exit(); // Termina a execução do script após redirecionamento
        }
        $stmtUpdateOrigem->close();
    }

    // Commit para finalizar a transação
    $conn->commit();
    header("Location: ../ViewSuccess/SuccessAceitaTransferencia.php?sucesso=A transferência foi processada com sucesso");
    exit(); // Termina a execução do script após redirecionamento
} catch (Exception $e) {
    // Rollback em caso de erro
    $conn->rollback();
    header("Location: ../ViewFail/FailProcessaTransferencia.php?erro=Erro ao processar a transferência: " . $e->getMessage());
    exit(); // Termina a execução do script após redirecionamento
} finally {
    // Fechar conexão ao banco de dados
    $conn->close();
}
?>
