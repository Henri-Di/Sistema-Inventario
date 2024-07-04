<?php

// Iniciar sessão se necessário
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['usuarioNome']) || !isset($_SESSION['usuarioCodigoP'])) {
    header("Location: ../ViewFail/FailCreateUsuarioNaoAutenticado.php?erro=O usuário não está autenticado. Realize o login novamente");
    exit(); // Termina a execução do script após redirecionamento
}

// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Função para validar quantidade
function validarQuantidade($quantidade) {
    return is_numeric($quantidade) && $quantidade > 0;
}

// Função para validar data
function validarData($data) {
    $format = 'Y-m-d';
    $d = DateTime::createFromFormat($format, $data);
    return $d && $d->format($format) === $data;
}

// Função para validar se a data é a data atual
function datasSaoValidas($dataAcrescimo) {
    try {
        $timeZone = new DateTimeZone('America/Sao_Paulo'); // Substitua pela sua zona de tempo
        $dataAcrescimoObj = DateTime::createFromFormat('Y-m-d', $dataAcrescimo, $timeZone);
        $currentDateObj = new DateTime('now', $timeZone); // Data atual do servidor
        $dataAcrescimoFormatada = $dataAcrescimoObj->format('Y-m-d');
        $currentDate = $currentDateObj->format('Y-m-d');
        return $dataAcrescimoFormatada === $currentDate;
    } catch (Exception $e) {
        return false;
    }
}

// Obter e validar os dados do formulário
$idProduto = $_POST['id'] ?? '';
$quantidadeAcrescimo = $_POST['Acrescimo'] ?? '';
$dataAcrescimo = $_POST['DataAcrescimo'] ?? '';
$observacao = strtoupper($_POST['Observacao'] ?? '');

if (empty($idProduto) || !validarQuantidade($quantidadeAcrescimo) || !validarData($dataAcrescimo) || !datasSaoValidas($dataAcrescimo)) {
    header("Location: ../ViewFail/FailCreateDadosInvalidos.php?erro=Os dados fornecidos são inválidos. Tente novamente");
    exit();
}

// Obter os dados do usuário da sessão
$idUsuario = $_SESSION['usuarioId'];
$nomeUsuario = strtoupper($_SESSION['usuarioNome']);
$codigoPUsuario = strtoupper($_SESSION['usuarioCodigoP']);

// Definir valores fixos
$operacao = "ACRÉSCIMO";
$situacao = "ACRESCENTADO";

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Iniciar transação para garantir consistência
$conn->begin_transaction();

try {
    // Verificar se há reservas para o produto
    $sqlVerificaReserva = "SELECT RESERVADO FROM ESTOQUE WHERE IDPRODUTO = ?";
    $stmtVerificaReserva = $conn->prepare($sqlVerificaReserva);
    $stmtVerificaReserva->bind_param("i", $idProduto);
    $stmtVerificaReserva->execute();
    $stmtVerificaReserva->bind_result($reservado);
    $stmtVerificaReserva->fetch();
    $stmtVerificaReserva->close();

    $temReserva = $reservado > 0;

    // Inserir dados na tabela ACRESCIMO usando prepared statement
    $sqlInsertAcrescimo = "INSERT INTO ACRESCIMO (QUANTIDADE, DATAACRESCIMO, OBSERVACAO, OPERACAO, SITUACAO, IDPRODUTO, IDUSUARIO, NOME, CODIGOP) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsertAcrescimo);
    $stmtInsert->bind_param("issssisss", $quantidadeAcrescimo, $dataAcrescimo, $observacao, $operacao, $situacao, $idProduto, $idUsuario, $nomeUsuario, $codigoPUsuario);
    if (!$stmtInsert->execute()) {
        header("Location: ../ViewFail/FailCreateInserirDadosAcrescimo.php?erro=Não foi possível inserir os dados na tabela ACRESCIMO. Informe o departamento de TI");
        exit();
    }

    // Atualizar a tabela ESTOQUE acrescentando a quantidade
    $sqlUpdateEstoque = "UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE + ? WHERE IDPRODUTO = ?";
    $stmtUpdate = $conn->prepare($sqlUpdateEstoque);
    $stmtUpdate->bind_param("ii", $quantidadeAcrescimo, $idProduto);
    if (!$stmtUpdate->execute()) {
        header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=Não foi possível atualizar o estoque do produto. Refaça a operação e tente novamente");
        exit();
    }

    // Commit da transação se todas as operações foram bem-sucedidas
    $conn->commit();

    // Redirecionar para a página apropriada com base na existência de reservas
    if ($temReserva) {
        header("Location: ../ViewSucess/SucessCreateAtualizaEstoqueComTransferencia.php?sucesso=O estoque do produto será atualizado após a confirmação das transferências pendentes");
    } else {
        header("Location: ../ViewSucess/SucessCreateAtualizaEstoque.php?sucesso=O estoque do produto foi atualizado com sucesso");
    }
    exit();
    
} catch (Exception $e) {
    // Em caso de erro, fazer rollback da transação
    $conn->rollback();
    header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=Não foi possível atualizar o estoque do produto. Refaça a operação e tente novamente");
    exit();

} finally {
    // Fechar os statements e a conexão
    if (isset($stmtInsert)) {
        $stmtInsert->close();
    }
    if (isset($stmtUpdate)) {
        $stmtUpdate->close();
    }
    $conn->close();
}
?>
