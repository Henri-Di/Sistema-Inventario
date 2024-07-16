<?php
session_start();
session_regenerate_id(true);

if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['usuarioNome']) || !isset($_SESSION['usuarioCodigoP'])) {
    header("Location: ../ViewFail/FailCreateUsuarioNaoAutenticado.php?erro=" . urlencode("O usuário não está autenticado. Realize o login novamente"));
    exit();
}

header("Content-Security-Policy: default-src 'self'");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

require_once('../../ViewConnection/ConnectionInventario.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idReserva = filter_input(INPUT_POST, 'idReserva', FILTER_SANITIZE_NUMBER_INT);
    $acaoReserva = filter_input(INPUT_POST, 'AcaoReserva', FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($idReserva) || empty($acaoReserva)) {
        header("Location: ../ViewFail/FailCreateUpdateDadosInvalidosReserva.php?erro=" . urlencode("Os dados fornecidos são inválidos. Tente novamente"));
        exit();
    }

    $idUsuario = $_SESSION['usuarioId'];
    $nomeUsuario = htmlspecialchars(mb_strtoupper($_SESSION['usuarioNome'], 'UTF-8'), ENT_QUOTES, 'UTF-8');
    $codigoPUsuario = htmlspecialchars(mb_strtoupper($_SESSION['usuarioCodigoP'], 'UTF-8'), ENT_QUOTES, 'UTF-8');

    if ($conn->connect_error) {
        die("Falha na conexão: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8'));
    }

    $conn->begin_transaction();

    try {
        $sqlObterReserva = "SELECT IDPRODUTO, QUANTIDADE, SITUACAO, IDUSUARIO FROM RESERVA WHERE ID = ?";
        $stmtObterReserva = $conn->prepare($sqlObterReserva);
        $stmtObterReserva->bind_param("i", $idReserva);
        $stmtObterReserva->execute();
        $stmtObterReserva->bind_result($idProduto, $quantidadeReservada, $situacaoAtual, $idUsuarioCriador);
        $stmtObterReserva->fetch();
        $stmtObterReserva->close();

        if ($situacaoAtual !== 'PENDENTE') {
            header("Location: ../ViewFail/FailCreateUpdateSituacaoInvalidaReserva.php?erro=" . urlencode("A reserva não pode ser alterada pois já está concluída ou cancelada"));
            exit();
        }

        if ($idUsuario !== $idUsuarioCriador) {
            header("Location: ../ViewFail/FailCreateUpdatePermissaoInvalidaReserva.php?erro=" . urlencode("Você não tem permissão para modificar o status desta reserva. Somente o usuário que criou a reserva pode modificar seu status"));
            exit();
        }

        $sqlVerificarReservasPendentes = "SELECT COUNT(*) FROM RESERVA WHERE IDPRODUTO = ? AND SITUACAO = 'PENDENTE'";
        $stmtVerificarReservasPendentes = $conn->prepare($sqlVerificarReservasPendentes);
        $stmtVerificarReservasPendentes->bind_param("i", $idProduto);
        $stmtVerificarReservasPendentes->execute();
        $stmtVerificarReservasPendentes->bind_result($quantidadeReservasPendentes);
        $stmtVerificarReservasPendentes->fetch();
        $stmtVerificarReservasPendentes->close();

        $sqlUpdateReserva = "UPDATE RESERVA SET SITUACAO = ?, IDUSUARIO = ?, NOME = ?, CODIGOP = ? WHERE ID = ?";
        $stmtUpdateReserva = $conn->prepare($sqlUpdateReserva);
        $stmtUpdateReserva->bind_param("sissi", $acaoReserva, $idUsuario, $nomeUsuario, $codigoPUsuario, $idReserva);

        if (!$stmtUpdateReserva->execute()) {
            header("Location: ../ViewFail/FailCreateUpdateReserva.php?erro=" . urlencode("Não foi possível atualizar a reserva. Refaça a operação e tente novamente"));
            exit();
        }

        if ($acaoReserva === 'CANCELADA' && $quantidadeReservasPendentes === 0) {
            $sqlUpdateEstoque = "UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE + ?, RESERVADO_RESERVA = 0 WHERE IDPRODUTO = ?";
            $stmtUpdateEstoque = $conn->prepare($sqlUpdateEstoque);
            $stmtUpdateEstoque->bind_param("ii", $quantidadeReservada, $idProduto);

            if (!$stmtUpdateEstoque->execute()) {
                header("Location: ../ViewFail/FailCreateUpdateEstoqueReserva.php?erro=" . urlencode("Não foi possível atualizar o estoque do produto da reserva. Refaça a operação e tente novamente"));
                exit();
            }
        } elseif ($acaoReserva === 'CONCLUIDA') {
            if ($quantidadeReservasPendentes === 0) {
                $sqlUpdateEstoque = "UPDATE ESTOQUE SET RESERVADO_RESERVA = 0 WHERE IDPRODUTO = ?";
                $stmtUpdateEstoque = $conn->prepare($sqlUpdateEstoque);
                $stmtUpdateEstoque->bind_param("i", $idProduto);

                if (!$stmtUpdateEstoque->execute()) {
                    header("Location: ../ViewFail/FailCreateUpdateEstoqueReserva.php?erro=" . urlencode("Não foi possível atualizar o estoque do produto da reserva. Refaça a operação e tente novamente"));
                    exit();
                }
            }
        }

        $conn->commit();

        header("Location: ../ViewSucess/SucessCreateUpdateAcaoReserva.php?sucesso=" . urlencode("A reserva do produto foi concluída com sucesso"));
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Erro: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));

        header("Location: ../ViewFail/FailCreateUpdateReserva.php?erro=" . urlencode("Não foi possível atualizar a reserva. Refaça a operação e tente novamente"));
        exit();

    } finally {
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
