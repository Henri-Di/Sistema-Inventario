<?php
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['usuarioNome']) || !isset($_SESSION['usuarioCodigoP'])) {
    header("Location: ../ViewFail/FailCreateUsuarioNaoAutenticado.php?erro=O usuário não está autenticado. Realize o login novamente");
    exit();
}

// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Inicializa variáveis com os dados do formulário
$idProdutoOrigem = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$quantidadeTransferencia = filter_input(INPUT_POST, 'QuantidadeTransferencia', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$dataTransferencia = filter_input(INPUT_POST, 'DataTransferencia', FILTER_SANITIZE_STRING);
$idDataCenterDestino = filter_input(INPUT_POST, 'DataCenter', FILTER_SANITIZE_STRING);
$observacao = filter_input(INPUT_POST, 'Observacao', FILTER_SANITIZE_STRING);

// Obter os dados do usuário da sessão
$idUsuario = $_SESSION['usuarioId'];
$nomeUsuario = $_SESSION['usuarioNome'];
$codigoPUsuario = $_SESSION['usuarioCodigoP'];

// Definir valores fixos
$operacao = "Transferência";
$situacao = "Pendente"; // A transferência começa como "Pendente"

// Verifica se há campos vazios
if (empty($idProdutoOrigem) || empty($quantidadeTransferencia) || empty($dataTransferencia) || empty($idDataCenterDestino) || empty($observacao)) {
    header("Location: ../ViewFail/FailCreateTransferenciaErroDados.php?erro=Existem campos vazios no formulário. Verifique e tente novamente");
    exit();
}

// Verifica se a quantidade é válida
if (!is_numeric($quantidadeTransferencia) || $quantidadeTransferencia <= 0) {
    header("Location: ../ViewFail/FailCreateQuantidadeNegativa.php?erro=Não é permitido o registro de valores negativos no campo de quantidade");
    exit();
}

// Função para validar se as datas são válidas
function datasSaoValidas($dataTransferencia) {
    try {
        $timeZone = new DateTimeZone('America/Sao_Paulo');
        $dataCadastroObj = DateTime::createFromFormat('Y-m-d', $dataTransferencia, $timeZone);
        $currentDateObj = new DateTime('now', $timeZone);
        $dataCadastroFormatada = $dataCadastroObj->format('Y-m-d');
        $currentDate = $currentDateObj->format('Y-m-d');
        return $dataCadastroFormatada === $currentDate;
    } catch (Exception $e) {
        return false;
    }
}

$conn->begin_transaction();

try {
    // Obter informações do produto de origem
    $sqlProdutoOrigem = "SELECT IDMATERIAL, IDCONECTOR, IDMETRAGEM, IDMODELO, IDFORNECEDOR, e.QUANTIDADE, e.RESERVADO, p.IDDATACENTER 
                         FROM PRODUTO p
                         INNER JOIN ESTOQUE e ON p.IDPRODUTO = e.IDPRODUTO
                         WHERE p.IDPRODUTO = ?";
    $stmtProdutoOrigem = $conn->prepare($sqlProdutoOrigem);
    $stmtProdutoOrigem->bind_param("i", $idProdutoOrigem);
    $stmtProdutoOrigem->execute();
    $stmtProdutoOrigem->bind_result($idMaterial, $idConector, $idMetragem, $idModelo, $idFornecedor, $quantidadeAtualOrigem, $reservadoAtual, $idDataCenterOrigem);
    $stmtProdutoOrigem->fetch();
    $stmtProdutoOrigem->close();

    if ($quantidadeAtualOrigem < $quantidadeTransferencia) {
        header("Location: ../ViewFail/FailCreateEstoqueInsuficiente.php?erro=A transferência não pode ser realizada. O estoque do produto é insuficiente");
        exit();
    }

    // Obter ID do data center de destino
    $sqlDatacenterDestino = "SELECT IDDATACENTER FROM DATACENTER WHERE NOME = ?";
    $stmtDatacenterDestino = $conn->prepare($sqlDatacenterDestino);
    $stmtDatacenterDestino->bind_param("s", $idDataCenterDestino);
    $stmtDatacenterDestino->execute();
    $stmtDatacenterDestino->bind_result($idDatacenterDestino);
    $stmtDatacenterDestino->fetch();
    $stmtDatacenterDestino->close();

    if (!$idDatacenterDestino) {
        header("Location: ../ViewFail/FailCreateDatacenterDestinoNaoEncontrado.php?erro=O datacenter de destino não foi encontrado");
        exit();
    }

    if ($idDataCenterOrigem == $idDatacenterDestino) {
        throw new Exception('O produto de destino não pode ser igual ao produto de origem');
    }

    // Verificar se existe um produto de destino compatível no datacenter de destino
    $sqlProdutoDestino = "SELECT p.IDPRODUTO 
                          FROM PRODUTO p
                          INNER JOIN ESTOQUE e ON p.IDPRODUTO = e.IDPRODUTO
                          WHERE p.IDMATERIAL = ? AND p.IDCONECTOR = ? AND p.IDMETRAGEM = ? AND p.IDMODELO = ? AND p.IDFORNECEDOR = ? AND p.IDDATACENTER = ?";
    $stmtProdutoDestino = $conn->prepare($sqlProdutoDestino);
    $stmtProdutoDestino->bind_param("iiiiii", $idMaterial, $idConector, $idMetragem, $idModelo, $idFornecedor, $idDatacenterDestino);
    $stmtProdutoDestino->execute();
    $stmtProdutoDestino->store_result();
    $numRows = $stmtProdutoDestino->num_rows;
    $stmtProdutoDestino->close();

    if ($numRows == 0) {
        header("Location: ../ViewFail/FailCreateProdutoDestinoNaoEncontrado.php?erro=O produto de destino não foi encontrado no datacenter destino");
        exit();

    }

    // Obter ID do produto de destino
    $sqlIdProdutoDestino = "SELECT IDPRODUTO 
                            FROM PRODUTO 
                            WHERE IDMATERIAL = ? AND IDCONECTOR = ? AND IDMETRAGEM = ? AND IDMODELO = ? AND IDFORNECEDOR = ? AND IDDATACENTER = ?";
    $stmtIdProdutoDestino = $conn->prepare($sqlIdProdutoDestino);
    $stmtIdProdutoDestino->bind_param("iiiiii", $idMaterial, $idConector, $idMetragem, $idModelo, $idFornecedor, $idDatacenterDestino);
    $stmtIdProdutoDestino->execute();
    $stmtIdProdutoDestino->bind_result($idProdutoDestino);
    $stmtIdProdutoDestino->fetch();
    $stmtIdProdutoDestino->close();

    if (!$idProdutoDestino) {
        header("Location: ../ViewFail/FailCreateProdutoDestinoNaoEncontrado.php?erro=O produto de destino não foi encontrado no datacenter destino");
        exit();
    }

    // Inserir registro de transferência
    $sqlInsertTransferencia = "INSERT INTO TRANSFERENCIA (QUANTIDADE, DATA_TRANSFERENCIA, IDDATACENTER, OBSERVACAO, OPERACAO, SITUACAO, IDPRODUTO_ORIGEM, IDPRODUTO_DESTINO, IDUSUARIO, NOME, CODIGOP) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsertTransferencia);
    $stmtInsert->bind_param("isssssiiiss", $quantidadeTransferencia, $dataTransferencia, $idDatacenterDestino, $observacao, $operacao, $situacao, $idProdutoOrigem, $idProdutoDestino, $idUsuario, $nomeUsuario, $codigoPUsuario);

    if (!$stmtInsert->execute()) {
        header("Location: ../ViewFail/FailCreateInserirDadosTransferencia.php?erro=Não foi possível inserir os dados na tabela TRANSFERENCIA. Informe o departamento de TI");
        exit();
    }

    // Atualizar o campo RESERVADO no estoque do produto de origem
    $sqlTotalReservado = "SELECT SUM(QUANTIDADE) 
                          FROM TRANSFERENCIA 
                          WHERE IDPRODUTO_ORIGEM = ? AND SITUACAO = 'Pendente'";
    $stmtTotalReservado = $conn->prepare($sqlTotalReservado);
    $stmtTotalReservado->bind_param("i", $idProdutoOrigem);
    $stmtTotalReservado->execute();
    $stmtTotalReservado->bind_result($totalReservado);
    $stmtTotalReservado->fetch();
    $stmtTotalReservado->close();

    if (!$totalReservado) {
        $totalReservado = 0;
    }

    $sqlUpdateEstoque = "UPDATE ESTOQUE SET RESERVADO = ? WHERE IDPRODUTO = ?";
    $stmtUpdateEstoque = $conn->prepare($sqlUpdateEstoque);
    $stmtUpdateEstoque->bind_param("ii", $totalReservado, $idProdutoOrigem);
    if (!$stmtUpdateEstoque->execute()) {
        header("Location: ../ViewFail/FailCreateAtualizarReservado.php?erro=Não foi possível atualizar o campo RESERVADO na tabela ESTOQUE");
        exit();
    }

    // Commit da transação
    $conn->commit();

    // Redirecionar para a página de sucesso
    header("Location: ../ViewSucess/SucessCreateTransferencia.php?sucesso=A transferência foi realizada com sucesso. Aguarde a confirmação do datacenter de destino");
    exit();

} catch (Exception $e) {
    // Rollback da transação em caso de erro
    $conn->rollback();
    header("Location: ../ViewFail/FailCreateTransferencia.php?erro=Não foi possível criar a transferência do produto. Refaça a operação e tente novamente");
    exit();
} finally {
    // Fechar statements e conexão
    if (isset($stmtInsert)) {
        $stmtInsert->close();
    }
    if (isset($stmtUpdateEstoque)) {
        $stmtUpdateEstoque->close();
    }
    $conn->close();
}
?>
