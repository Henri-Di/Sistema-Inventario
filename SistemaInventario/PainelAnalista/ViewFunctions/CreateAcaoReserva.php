<?php
// Iniciar sessão se necessário
session_start();

// Verificar se os dados do usuário estão disponíveis na sessão
if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['usuarioNome']) || !isset($_SESSION['usuarioCodigoP'])) {
    header("Location: ../ViewFail/FailCreateUsuarioNaoAutenticado.php?erro=O usuário não está autenticado. Realize o login novamente");
    exit();
}

// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Verificar se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obter e validar os dados do formulário
    $idReserva = $_POST['idReserva'] ?? '';
    $acaoReserva = $_POST['AcaoReserva'] ?? '';

    // Validar se os campos obrigatórios foram preenchidos
    if (empty($idReserva) || empty($acaoReserva)) {
        header("Location: ../ViewFail/FailCreateUpdateDadosInvalidosReserva.php?erro=Os dados fornecidos são inválidos. Tente novamente");
        exit();
    }

    // Obter os dados do usuário da sessão
    $idUsuario = $_SESSION['usuarioId'];
    $nomeUsuario = mb_strtoupper($_SESSION['usuarioNome'], 'UTF-8');
    $codigoPUsuario = mb_strtoupper($_SESSION['usuarioCodigoP'], 'UTF-8');

    // Verificar a conexão com o banco de dados
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Iniciar transação para garantir consistência
    $conn->begin_transaction();

    try {
        // Obter os detalhes da reserva
        $sqlObterReserva = "SELECT IDPRODUTO, QUANTIDADE, SITUACAO, IDUSUARIO FROM RESERVA WHERE ID = ?";
        $stmtObterReserva = $conn->prepare($sqlObterReserva);
        $stmtObterReserva->bind_param("i", $idReserva);
        $stmtObterReserva->execute();
        $stmtObterReserva->bind_result($idProduto, $quantidadeReservada, $situacaoAtual, $idUsuarioCriador);
        $stmtObterReserva->fetch();
        $stmtObterReserva->close();

        // Verificar se a situação atual é 'PENDENTE'
        if ($situacaoAtual !== 'PENDENTE') {
            header("Location: ../ViewFail/FailCreateUpdateSituacaoInvalidaReserva.php?erro=A reserva não pode ser alterada pois já está concluída ou cancelada");
            exit();
        }

        // Verificar se o usuário atual é o criador da reserva
        if ($idUsuario !== $idUsuarioCriador) {
            header("Location: ../ViewFail/FailCreateUpdatePermissaoInvalidaReserva.php?erro=Você não tem permissão para modificar o status desta reserva. Somente o usuário que criou a reserva pode modificar seu status");
            exit();
        }

        // Verificar se há outras reservas pendentes para o mesmo produto
        $sqlVerificarReservasPendentes = "SELECT COUNT(*) FROM RESERVA WHERE IDPRODUTO = ? AND SITUACAO = 'PENDENTE'";
        $stmtVerificarReservasPendentes = $conn->prepare($sqlVerificarReservasPendentes);
        $stmtVerificarReservasPendentes->bind_param("i", $idProduto);
        $stmtVerificarReservasPendentes->execute();
        $stmtVerificarReservasPendentes->bind_result($quantidadeReservasPendentes);
        $stmtVerificarReservasPendentes->fetch();
        $stmtVerificarReservasPendentes->close();

        // Atualizar a tabela RESERVA
        $sqlUpdateReserva = "UPDATE RESERVA SET SITUACAO = ?, IDUSUARIO = ?, NOME = ?, CODIGOP = ? WHERE ID = ?";
        $stmtUpdateReserva = $conn->prepare($sqlUpdateReserva);
        $stmtUpdateReserva->bind_param("sissi", $acaoReserva, $idUsuario, $nomeUsuario, $codigoPUsuario, $idReserva);

        if (!$stmtUpdateReserva->execute()) {
            header("Location: ../ViewFail/FailCreateUpdateReserva.php?erro=Não foi possível atualizar a reserva. Refaça a operação e tente novamente");
            exit();
        }

        // Se a reserva for cancelada e não houver mais reservas pendentes, devolver a quantidade ao estoque
        if ($acaoReserva === 'CANCELADA' && $quantidadeReservasPendentes === 0) {
            $sqlUpdateEstoque = "UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE + ?, RESERVADO_RESERVA = 0 WHERE IDPRODUTO = ?";
            $stmtUpdateEstoque = $conn->prepare($sqlUpdateEstoque);
            $stmtUpdateEstoque->bind_param("ii", $quantidadeReservada, $idProduto);

            if (!$stmtUpdateEstoque->execute()) {
                header("Location: ../ViewFail/FailCreateUpdateEstoqueReserva.php?erro=Não foi possível atualizar o estoque do produto da reserva. Refaça a operação e tente novamente");
                exit();
            }
        } elseif ($acaoReserva === 'CONCLUIDA') {
            // Se a reserva for concluída, apenas zerar o reservado_reserva se não houver mais reservas pendentes
            if ($quantidadeReservasPendentes === 0) {
                $sqlUpdateEstoque = "UPDATE ESTOQUE SET RESERVADO_RESERVA = 0 WHERE IDPRODUTO = ?";
                $stmtUpdateEstoque = $conn->prepare($sqlUpdateEstoque);
                $stmtUpdateEstoque->bind_param("i", $idProduto);

                if (!$stmtUpdateEstoque->execute()) {
                    header("Location: ../ViewFail/FailCreateUpdateEstoqueReserva.php?erro=Não foi possível atualizar o estoque do produto da reserva. Refaça a operação e tente novamente");
                    exit();
                }
            }
        }

        // Commit da transação se todas as operações foram bem-sucedidas
        $conn->commit();

        header("Location: ../ViewSucess/SucessCreateUpdateAcaoReserva.php?sucesso=A reserva do produto foi concluída com sucesso");
        exit();

    } catch (Exception $e) {
        // Em caso de erro, fazer rollback da transação
        $conn->rollback();

        // Registrar o erro
        error_log("Erro: " . $e->getMessage());

        // Redirecionar para a página de falha com uma mensagem de erro
        header("Location: ../ViewFail/FailCreateUpdateReserva.php?erro=Não foi possível atualizar a reserva. Refaça a operação e tente novamente");
        exit();

    } finally {
        // Fechar os statements e a conexão
        if (isset($stmtUpdateReserva)) {
            $stmtUpdateReserva->close();
        }
        if (isset($stmtUpdateEstoque)) {
            $stmtUpdateEstoque->close();
        }
        $conn->close();
    }
}
?>
