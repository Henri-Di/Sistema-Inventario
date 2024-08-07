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

// Verificar se os dados do usuário estão disponíveis na sessão
if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['usuarioNome']) || !isset($_SESSION['usuarioCodigoP'])) {
    header("Location: ../ViewFail/FailCreateUsuarioNaoAutenticado.php?erro=" . urlencode("O usuário não está autenticado. Realize o login novamente"));
    exit();
}

// Obter os dados do usuário da sessão
$idUsuario = $_SESSION['usuarioId'];
$nomeUsuario = $_SESSION['usuarioNome'];
$codigoPUsuario = $_SESSION['usuarioCodigoP'];

// Verificar se o ID da transferência foi recebido via POST
if (!isset($_POST['idTransferencia'])) {
    header("Location: ../ViewFail/FailCreateLocalizaTransferencia.php?erro=" .  urlencode("A transferência de produtos indicada não foi encontrada"));
    exit();
}

// Obter o ID da transferência a partir dos dados recebidos
$idTransferencia = $_POST['idTransferencia'];

// Verificar se a ação (aceitar ou recusar) foi enviada via POST
if (!isset($_POST['Acao']) || ($_POST['Acao'] !== 'Aceitar' && $_POST['Acao'] !== 'Recusar')) {
    header("Location: ../ViewFail/FailAcaoTransferencia.php?erro=" . urlencode("Não foi possível processar a transferência de produtos. Refaça a operação e tente novamente"));
    exit();
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
        header("Location: ../ViewFail/FailCreateLocalizaTransferencia.php?erro=" . urlencode("A transferência de produtos indicada não foi encontrada"));
        exit();
    }

    // Bind results
    $stmtSelect->bind_result($quantidadeTransferida, $idProdutoOrigem, $idProdutoDestino, $situacaoTransferencia, $idUsuarioTransferencia);
    $stmtSelect->fetch();

    // Verificar se a transferência já foi aceita ou recusada anteriormente
    if ($situacaoTransferencia !== 'PENDENTE') {
        header("Location: ../ViewFail/FailCreateTransferenciaProcessada.php?erro=" . urlencode("Essa transferência já foi processada. Tente novamente com uma transferência que esteja com o status pendente"));
        exit();
    }

    // Verificar se o usuário que está tentando aceitar ou recusar é o mesmo que criou a transferência
    if ($idUsuario === $idUsuarioTransferencia) {
        header("Location: ../ViewFail/FailCreateUsuarioAceitaTransferencia.php?erro=" . urlencode("Você não pode aceitar ou recusar uma transferência criada por você mesmo"));
        exit();
    }

    // Realizar ação com base na escolha do usuário
    if ($acao === 'Aceitar') {
        // Atualizar a situação da transferência para 'RECEBIDO'
        $sqlUpdateAceitar = "UPDATE TRANSFERENCIA SET SITUACAO = ? WHERE ID = ?";
        $stmtUpdate = $conn->prepare($sqlUpdateAceitar);
        $situacao = mb_strtoupper('Confirmada', 'UTF-8');
        $stmtUpdate->bind_param("si", $situacao, $idTransferencia);
        $stmtUpdate->execute();

        // Verificar se a atualização foi bem-sucedida
        if ($stmtUpdate->affected_rows == 0) {
            header("Location: ../ViewFail/FailCreateSituacaoTransferenciaRecebida.php?erro=" . urlencode("Não foi possível atualizar a situação da transferência para Recebido"));
            exit();
        }
        $stmtUpdate->close();

        // Atualizar o estoque do produto destino com a quantidade transferida
        $sqlUpdateEstoqueDestino = "UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE + ? WHERE IDPRODUTO = ?";
        $stmtUpdateDestino = $conn->prepare($sqlUpdateEstoqueDestino);
        $stmtUpdateDestino->bind_param("ii", $quantidadeTransferida, $idProdutoDestino);
        $stmtUpdateDestino->execute();

        // Verificar se a atualização foi bem-sucedida
        if ($stmtUpdateDestino->affected_rows == 0) {
            header("Location: ../ViewFail/FailCreateAtualizaEstoqueDestinoTransferencia.php?erro=" . urlencode("Não foi possível atualizar o estoque do produto de destino da transferência"));
            exit();
        }
        $stmtUpdateDestino->close();

        // Atualizar o estoque do produto de origem decrementando a quantidade transferida e a quantidade reservada
        $sqlUpdateEstoqueOrigem = "UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE - ?, RESERVADO_TRANSFERENCIA = RESERVADO_TRANSFERENCIA - ? WHERE IDPRODUTO = ?";
        $stmtUpdateOrigem = $conn->prepare($sqlUpdateEstoqueOrigem);
        $stmtUpdateOrigem->bind_param("iii", $quantidadeTransferida, $quantidadeTransferida, $idProdutoOrigem);
        $stmtUpdateOrigem->execute();

        // Verificar se a atualização foi bem-sucedida
        if ($stmtUpdateOrigem->affected_rows == 0) {
            header("Location: ../ViewFail/FailCreateAtualizaEstoqueOrigemTransferencia.php?erro=" . urlencode("Não foi possível atualizar o estoque do produto de origem da transferência. Tente novamente"));
            exit();
        }
        $stmtUpdateOrigem->close();
    } elseif ($acao === 'Recusar') {
        // Atualizar a situação da transferência para 'RECUSADO'
        $sqlUpdateRecusar = "UPDATE TRANSFERENCIA SET SITUACAO = ? WHERE ID = ?";
        $stmtUpdate = $conn->prepare($sqlUpdateRecusar);
        $situacao = mb_strtoupper('Recusada', 'UTF-8');
        $stmtUpdate->bind_param("si", $situacao, $idTransferencia);
        $stmtUpdate->execute();

        // Verificar se a atualização foi bem-sucedida
        if ($stmtUpdate->affected_rows == 0) {
            header("Location: ../ViewFail/FailCreateSituacaoTransferenciaRecusada.php?erro=" . urlencode("Não foi possível atualizar a situação da transferência para Recusado. Refaça a operação e tente novamente"));
            exit();
        }
        $stmtUpdate->close();

        // Remover a quantidade reservada do estoque do produto origem sem alterar a quantidade total
        $sqlUpdateEstoqueOrigem = "UPDATE ESTOQUE SET RESERVADO_TRANSFERENCIA = RESERVADO_TRANSFERENCIA - ? WHERE IDPRODUTO = ?";
        $stmtUpdateOrigem = $conn->prepare($sqlUpdateEstoqueOrigem);
        $stmtUpdateOrigem->bind_param("ii", $quantidadeTransferida, $idProdutoOrigem);
        $stmtUpdateOrigem->execute();

        // Verificar se a atualização foi bem-sucedida
        if ($stmtUpdateOrigem->affected_rows == 0) {
            header("Location: ../ViewFail/FailCreateQuantidadeEstoqueReservado.php?erro=" . urlencode("Não foi possível atualizar a quantidade reservada do produto de origem. Refaça a operação e tente novamente"));
            exit();
        }
        $stmtUpdateOrigem->close();

        // Verificar se há outras transferências pendentes para o produto de origem
        $sqlCountPendentes = "SELECT COUNT(*) FROM TRANSFERENCIA WHERE IDPRODUTO_ORIGEM = ? AND SITUACAO = 'PENDENTE'";
        $stmtCountPendentes = $conn->prepare($sqlCountPendentes);
        $stmtCountPendentes->bind_param("i", $idProdutoOrigem);
        $stmtCountPendentes->execute();
        $stmtCountPendentes->bind_result($countPendentes);
        $stmtCountPendentes->fetch();
        $stmtCountPendentes->close();

        // Se não houver outras transferências pendentes, definir o reservado como 0
        if ($countPendentes == 0) {
            $sqlResetReservado = "UPDATE ESTOQUE SET RESERVADO_TRANSFERENCIA = 0 WHERE IDPRODUTO = ?";
            $stmtResetReservado = $conn->prepare($sqlResetReservado);
            $stmtResetReservado->bind_param("i", $idProdutoOrigem);
            $stmtResetReservado->execute();
            $stmtResetReservado->close();
        }
    }

    // Inserir registro no log de transferência
    $acaoLog = mb_strtoupper($acao === 'Aceitar' ? 'Confirmada' : 'Recusada', 'UTF-8');
    $sqlInsertLog = "INSERT INTO TRANSFERENCIA_LOG (IDTRANSFERENCIA, IDUSUARIO, NOME, CODIGOP, ACAO, IDPRODUTO_ORIGEM, IDPRODUTO_DESTINO) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtInsertLog = $conn->prepare($sqlInsertLog);
    $stmtInsertLog->bind_param("issssii", $idTransferencia, $idUsuario, $nomeUsuario, $codigoPUsuario, $acaoLog, $idProdutoOrigem, $idProdutoDestino);
    $stmtInsertLog->execute();
    $stmtInsertLog->close();

    // Commit da transação se todas as operações foram bem-sucedidas
    $conn->commit();

    // Redirecionar para a página de sucesso
    header("Location: ../ViewSucess/SucessCreateAcaoTransferencia.php?sucesso=" . urlencode("A sua confirmação sobre a transferência foi realizada com sucesso"));
    exit();
} catch (Exception $e) {
    // Em caso de erro, fazer rollback da transação
    $conn->rollback();

    // Exibir mensagem de erro
    echo "Erro: " . $e->getMessage();

    // Redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateAcaoTransferencia.php?erro=" . urlencode("Não foi possível processar a transferência de produtos. Refaça a operação e tente novamente"));
    exit();
} finally {
    // Fechar o statement de seleção
    if (isset($stmtSelect)) {
        $stmtSelect->close();
    }

    // Fechar a conexão
    $conn->close();
}
?>
