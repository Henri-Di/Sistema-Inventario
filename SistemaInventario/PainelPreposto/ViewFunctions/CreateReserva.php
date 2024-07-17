<?php
// Iniciar sessão se necessário
session_start();
session_regenerate_id(true);

// Adicionar cabeçalhos de segurança
header("Content-Security-Policy: default-src 'self'");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// Verificar se os dados do usuário estão disponíveis na sessão
if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['usuarioNome']) || !isset($_SESSION['usuarioCodigoP'])) {
    header("Location: ../ViewFail/FailCreateUsuarioNaoAutenticado.php?erro=" . urlencode("O usuário não está autenticado. Realize o login novamente"));
    exit();
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
function datasSaoValidas($datareserva) {
    try {
        $timeZone = new DateTimeZone('America/Sao_Paulo');
        $datareservaObj = DateTime::createFromFormat('Y-m-d', $datareserva, $timeZone);
        $currentDateObj = new DateTime('now', $timeZone);
        $datareservaFormatada = $datareservaObj->format('Y-m-d');
        $currentDate = $currentDateObj->format('Y-m-d');
        return $datareservaFormatada === $currentDate;
    } catch (Exception $e) {
        return false;
    }
}

// Obter e validar os dados do formulário
$idProduto = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$numwo = mb_strtoupper(filter_input(INPUT_POST, 'NumWo', FILTER_SANITIZE_SPECIAL_CHARS), 'UTF-8');
$quantidadeReservar = filter_input(INPUT_POST, 'Reservar', FILTER_VALIDATE_INT);
$datareserva = filter_input(INPUT_POST, 'DataReserva', FILTER_SANITIZE_SPECIAL_CHARS);
$observacao = mb_strtoupper(filter_input(INPUT_POST, 'Observacao', FILTER_SANITIZE_SPECIAL_CHARS), 'UTF-8');

// Verificar se o campo observação excede 35 caracteres
if (mb_strlen($observacao, 'UTF-8') > 35) {
    header("Location: ../ViewFail/FailCreateObservacaoInvalida.php?erro=" . urlencode("O campo observação excede o limite de 35 caracteres. Refaça a operação e tente novamente"));
    exit();
}

// Validar se os campos obrigatórios foram preenchidos e se os dados são válidos
if (empty($idProduto) || empty($numwo) || !$quantidadeReservar || !validarQuantidade($quantidadeReservar) || !validarData($datareserva) || !datasSaoValidas($datareserva)) {
    header("Location: ../ViewFail/FailCreateDadosInvalidos.php?erro=" . urlencode("Os dados fornecidos são inválidos. Refaça a operação e tente novamente"));
    exit();
}

// Obter os dados do usuário da sessão
$idUsuario = $_SESSION['usuarioId'];
$nomeUsuario = mb_strtoupper($_SESSION['usuarioNome'], 'UTF-8');
$codigoPUsuario = mb_strtoupper($_SESSION['usuarioCodigoP'], 'UTF-8');

// Definir valores fixos
$operacao = "RESERVAR";
$situacao = "PENDENTE";

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Iniciar transação para garantir consistência
$conn->begin_transaction();

try {
    // Verificar a quantidade total atual no estoque
    $sqlVerificaEstoque = "SELECT QUANTIDADE FROM ESTOQUE WHERE IDPRODUTO = ?";
    $stmtVerificaEstoque = $conn->prepare($sqlVerificaEstoque);
    $stmtVerificaEstoque->bind_param("i", $idProduto);
    $stmtVerificaEstoque->execute();
    $stmtVerificaEstoque->bind_result($quantidadeTotal);
    $stmtVerificaEstoque->fetch();
    $stmtVerificaEstoque->close();

    // Calcular a nova quantidade no estoque (subtraindo a quantidade reservada)
    $novaQuantidadeEstoque = $quantidadeTotal - $quantidadeReservar;

    // Atualizar a tabela ESTOQUE com a nova quantidade no estoque
    $sqlUpdateEstoque = "UPDATE ESTOQUE SET QUANTIDADE = ? WHERE IDPRODUTO = ?";
    $stmtUpdate = $conn->prepare($sqlUpdateEstoque);
    $stmtUpdate->bind_param("ii", $novaQuantidadeEstoque, $idProduto);

    if (!$stmtUpdate->execute()) {
        header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=" . urlencode("Não foi possível atualizar o estoque do produto. Refaça a operação e tente novamente"));
        exit(); // Termina a execução do script após redirecionamento
    }

    // Inserir dados na tabela RESERVA usando prepared statement
    $sqlInsertReserva = "INSERT INTO RESERVA (NUMWO, QUANTIDADE, DATARESERVA, OBSERVACAO, OPERACAO, SITUACAO, IDPRODUTO, IDUSUARIO, NOME, CODIGOP) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsertReserva);
    $stmtInsert->bind_param("sissssiiss", $numwo, $quantidadeReservar, $datareserva, $observacao, $operacao, $situacao, $idProduto, $idUsuario, $nomeUsuario, $codigoPUsuario);

    if (!$stmtInsert->execute()) {
        header("Location: ../ViewFail/FailCreateInserirDadosReserva.php?erro=" . urlencode("Não foi possível inserir os dados na tabela RESERVA. Informe o departamento de TI"));
        exit(); // Termina a execução do script após redirecionamento
    }

    // Commit da transação se todas as operações foram bem-sucedidas
    $conn->commit();

    // Redirecionar para a página de sucesso apropriada
    header("Location: ../ViewSucess/SucessCreateReserva.php?sucesso=" . urlencode("A reserva do produto foi realizada com sucesso"));
    exit();

} catch (Exception $e) {
    // Em caso de erro, fazer rollback da transação
    $conn->rollback();

    // Registrar o erro
    error_log("Erro: " . $e->getMessage());

    // Redirecionar para a página de falha com uma mensagem de erro
    header("Location: ../ViewFail/FailCreateReserva.php?erro=" . urlencode("Não foi possível criar a reserva do produto. Refaça a operação e tente novamente"));
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
