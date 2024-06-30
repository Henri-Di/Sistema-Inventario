<?php

// Iniciar sessão se necessário
session_start();

// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Verifica se o usuário está autenticado
if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['usuarioNome']) || !isset($_SESSION['usuarioCodigoP'])) {
    header("Location: ../ViewFail/FailCreateUsuarioNaoAutenticado.php?erro=O usuário não está autenticado. Realize o login novamente");
    exit(); // Termina a execução do script após redirecionamento
}

// Obter os dados do formulário
$idProduto = $_POST['id'] ?? '';
$quantidadeAcrescimo = $_POST['Acrescimo'] ?? '';
$dataAcrescimo = $_POST['DataAcrescimo'] ?? '';
$observacao = $_POST['Observacao'] ?? '';

// Obter os dados do usuário da sessão
$idUsuario = $_SESSION['usuarioId'];
$nomeUsuario = $_SESSION['usuarioNome'];
$codigoPUsuario = $_SESSION['usuarioCodigoP'];

// Definir valores fixos
$operacao = "Acréscimo";
$situacao = "Acrescentado";

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Sanitizar os dados de entrada para evitar injeção de SQL
$idProduto = $conn->real_escape_string($idProduto);
$quantidadeAcrescimo = $conn->real_escape_string($quantidadeAcrescimo);
$dataAcrescimo = $conn->real_escape_string($dataAcrescimo);
$observacao = $conn->real_escape_string($observacao);
$idUsuario = $conn->real_escape_string($idUsuario);
$nomeUsuario = $conn->real_escape_string($nomeUsuario);
$codigoPUsuario = $conn->real_escape_string($codigoPUsuario);

// Verificar se a quantidade é positiva
if ($quantidadeAcrescimo <= 0) {
    header("Location: ../ViewFail/FailCreateQuantidadeNegativa.php?erro=Não é permitido o registro de valores negativos no campo de quantidade");
    exit();
}

// Função para validar se a data de acréscimo é válida
function datasSaoValidas($dataAcrescimo) {
    try {
        $timeZone = new DateTimeZone('America/Sao_Paulo'); // Substitua pela sua zona de tempo
        $dataAcrescimoObj = DateTime::createFromFormat('Y-m-d', $dataAcrescimo, $timeZone);
        $currentDateObj = new DateTime('now', $timeZone); // Data atual do servidor
        $dataAcrescimoFormatada = $dataAcrescimoObj->format('Y-m-d');
        $currentDate = $currentDateObj->format('Y-m-d');
        if ($dataAcrescimoFormatada !== $currentDate) {
            return false;
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Verificar se a data de acréscimo é válida
if (!datasSaoValidas($dataAcrescimo)) {
    header("Location: ../ViewFail/FailCreateDataInvalida.php?erro=A data está fora do intervalo permitido");
    exit();
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
        header("Location: ../ViewFail/FailCreateInserirDadosAcrescimo.php?erro=Não foi possível inserir os dados na tabela ACRESCIMO");
        exit();
    }

    // Atualizar a tabela ESTOQUE acrescentando a quantidade
    $sqlUpdateEstoque = "UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE + ? WHERE IDPRODUTO = ?";
    $stmtUpdate = $conn->prepare($sqlUpdateEstoque);
    $stmtUpdate->bind_param("ii", $quantidadeAcrescimo, $idProduto);
    if (!$stmtUpdate->execute()) {
        header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=Não foi possivel atualizar o estoque do produto");
        exit();
    }

    // Commit da transação se todas as operações foram bem-sucedidas
    $conn->commit();

    // Redirecionar para a página apropriada com base na existência de reservas
    if ($temReserva) {
        header("Location: ../ViewSucess/SucessCreateAtualizaEstoqueComTransferencia.php");
    } else {
        header("Location: ../ViewSucess/SucessCreateAtualizaEstoque.php");
    }
    exit();
    
} catch (Exception $e) {
    // Em caso de erro, fazer rollback da transação
    $conn->rollback();
    header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=Não foi possível atualizar o estoque do produto");
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
