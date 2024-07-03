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

// Verificar se a ação (aceitar ou recusar) foi enviada via POST
if (!isset($_POST['Acao']) || ($_POST['Acao'] !== 'Aceitar' && $_POST['Acao'] !== 'Recusar')) {
    header("Location: ../ViewFail/FailAcaoInvalida.php?erro=Ação inválida especificada");
    exit(); // Termina a execução do script após redirecionamento
}

// Obter a ação (aceitar ou recusar)
$acao = $_POST['Acao'];

try {
    // Iniciar transação para garantir consistência
    $conn->begin_transaction();

    // Obter dados da transferência para verificar e realizar operações
    $sqlSelectTransferencia = "SELECT QUANTIDADE, IDPRODUTO_ORIGEM, IDPRODUTO_DESTINO, SITUACAO, IDUSUARIO FROM TRANSFERENCIA WHERE ID = ?";
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
    $stmtSelect->bind_result($quantidadeTransferida, $idProdutoOrigem, $idProdutoDestino, $situacaoTransferencia, $idUsuarioTransferencia);
    $stmtSelect->fetch();

    // Verificar se a transferência já foi aceita ou recusada anteriormente
    if ($situacaoTransferencia !== 'Pendente') {
        header("Location: ../ViewFail/FailCreateTransferenciaProcessada.php?erro=Essa transferência já foi processada. Tente novamente com uma transferência que esteja com o status pendente");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Verificar se o usuário que está tentando aceitar ou recusar é o mesmo que criou a transferência
    if ($idUsuario === $idUsuarioTransferencia) {
        header("Location: ../ViewFail/FailCreateUsuarioAceitaTransferencia.php?erro=Você não pode aceitar ou recusar uma transferência criada por você mesmo");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Realizar ação com base na escolha do usuário
    if ($acao === 'Aceitar') {
        // Atualizar a situação da transferência para 'Recebido'
        $sqlUpdateAceitar = "UPDATE TRANSFERENCIA SET SITUACAO = 'Recebido' WHERE ID = ?";
        $stmtUpdate = $conn->prepare($sqlUpdateAceitar);
        $stmtUpdate->bind_param("i", $idTransferencia);
        $stmtUpdate->execute();

        // Verificar se a atualização foi bem-sucedida
        if ($stmtUpdate->affected_rows == 0) {
            header("Location: ../ViewFail/FailCreateSituacaoTransferenciaRecebida.php?erro=Não foi possível atualizar a situação da transferência para Recebido");
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
            header("Location: ../ViewFail/FailCreateAtualizaEstoqueOrigemTransferencia.php?erro=Não foi possível atualizar o estoque do produto de origem da transferência. Tente novamente");
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
            header("Location: ../ViewFail/FailCreateSituacaoTransferenciaRecusada.php?erro=Não foi possível atualizar a situação da transferência para Recusado. Refaça a operação e tente novamente");
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
            header("Location: ../ViewFail/FailCreateQuantidadeEstoqueReservado.php?erro=Não foi possível atualizar a quantidade reservada do produto de origem. Refaça a operação e tente novamente");
            exit(); // Termina a execução do script após redirecionamento
        }
        $stmtUpdateOrigem->close();

        // Verificar se há outras transferências pendentes para o produto de origem
        $sqlCountPendentes = "SELECT COUNT(*) FROM TRANSFERENCIA WHERE IDPRODUTO_ORIGEM = ? AND SITUACAO = 'Pendente'";
        $stmtCountPendentes = $conn->prepare($sqlCountPendentes);
        $stmtCountPendentes->bind_param("i", $idProdutoOrigem);
        $stmtCountPendentes->execute();
        $stmtCountPendentes->bind_result($countPendentes);
        $stmtCountPendentes->fetch();
        $stmtCountPendentes->close();

    // Se não houver outras transferências pendentes, definir o reservado como 0
        if ($countPendentes == 0) {
            $sqlResetReservado = "UPDATE ESTOQUE SET RESERVADO = 0 WHERE IDPRODUTO = ?";
            $stmtResetReservado = $conn->prepare($sqlResetReservado);
            $stmtResetReservado->bind_param("i", $idProdutoOrigem);
            $stmtResetReservado->execute();
            $stmtResetReservado->close();
        }
    }

    // Inserir registro no log de transferência
    $acaoLog = $acao === 'Aceitar' ? 'Recebido' : 'Recusado';
    $sqlInsertLog = "INSERT INTO TRANSFERENCIA_LOG (IDTRANSFERENCIA, IDUSUARIO, NOME, CODIGOP, ACAO, IDPRODUTO_ORIGEM, IDPRODUTO_DESTINO) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtInsertLog = $conn->prepare($sqlInsertLog);
    $stmtInsertLog->bind_param("issssii", $idTransferencia, $idUsuario, $nomeUsuario, $codigoPUsuario, $acaoLog, $idProdutoOrigem, $idProdutoDestino);
    $stmtInsertLog->execute();
    $stmtInsertLog->close();

    // Commit da transação se todas as operações foram bem-sucedidas
    $conn->commit();

    // Redirecionar para a página de sucesso
    header("Location: ../ViewSucess/SucessCreateAcaoTransferencia.php?sucesso=A sua confirmação sobre a transferência foi realizada com sucesso");
    exit(); // Termina a execução do script após redirecionamento
} catch (Exception $e) {
    // Em caso de erro, fazer rollback da transação
    $conn->rollback();

    // Exibir mensagem de erro
    echo "Erro: " . $e->getMessage();

    // Redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateAcaoTransferencia.php?erro=Não foi possível processar a transferência de produtos. Refaça a operação e tente novamente");
    exit(); // Termina a execução do script após redirecionamento
} finally {
    // Fechar o statement de seleção
    if (isset($stmtSelect)) {
        $stmtSelect->close();
    }

    // Fechar a conexão
    $conn->close();
}
?>