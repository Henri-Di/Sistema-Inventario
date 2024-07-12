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
$idProduto = $_POST['id'] ?? '';
$numwo = mb_strtoupper($_POST['NumWo'] ?? '', 'UTF-8');
$quantidadeReservar = $_POST['Reservar'] ?? '';
$datareserva = $_POST['DataReserva'] ?? '';
$observacao = mb_strtoupper($_POST['Observacao'] ?? '', 'UTF-8');

// Verificar se o campo observação excede 35 caracteres
if (mb_strlen($observacao, 'UTF-8') > 35) {
    header("Location: ../ViewFail/FailCreateObservacaoInvalida.php?erro=O campo observação excede o limite de 35 caracteres.");
    exit();
}

// Validar se os campos obrigatórios foram preenchidos e se os dados são válidos
if (empty($idProduto) || empty($numwo) || !validarQuantidade($quantidadeReservar) || !validarData($datareserva) || !datasSaoValidas($datareserva)) {
    header("Location: ../ViewFail/FailCreateDadosInvalidos.php?erro=Os dados fornecidos são inválidos. Tente novamente ");
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
    // Verificar se há reservas para o produto
    $sqlVerificaReserva = "SELECT RESERVADO_RESERVA FROM ESTOQUE WHERE IDPRODUTO = ?";
    $stmtVerificaReserva = $conn->prepare($sqlVerificaReserva);
    $stmtVerificaReserva->bind_param("i", $idProduto);
    $stmtVerificaReserva->execute();
    $stmtVerificaReserva->bind_result($reservado);
    $stmtVerificaReserva->fetch();
    $stmtVerificaReserva->close();

    // Calcular a quantidade reservada atualmente para este produto
    $quantidadeReservadaAtual = $reservado;

    // Inserir dados na tabela RESERVA usando prepared statement
    $sqlInsertReserva = "INSERT INTO RESERVA (NUMWO, QUANTIDADE, DATARESERVA, OBSERVACAO, OPERACAO, SITUACAO, IDPRODUTO, IDUSUARIO, NOME, CODIGOP) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsertReserva);
    $stmtInsert->bind_param("sissssiiss", $numwo, $quantidadeReservar, $datareserva, $observacao, $operacao, $situacao, $idProduto, $idUsuario, $nomeUsuario, $codigoPUsuario);

    if (!$stmtInsert->execute()) {
        header("Location: ../ViewFail/FailCreateInserirDadosReserva.php?erro=Não foi possível inserir os dados na tabela RESERVA. Informe o departamento de TI");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Calcular a quantidade nova reservada
    $quantidadeNovaReserva = $quantidadeReservar;

    // Calcular a quantidade total de reservas para este produto
    $quantidadeTotalReservada = $quantidadeReservadaAtual + $quantidadeNovaReserva;

    // Atualizar a tabela ESTOQUE para definir a quantidade reservada corretamente
    $sqlUpdateEstoque = "UPDATE ESTOQUE SET RESERVADO_RESERVA = ? WHERE IDPRODUTO = ?";
    $stmtUpdate = $conn->prepare($sqlUpdateEstoque);
    $stmtUpdate->bind_param("ii", $quantidadeTotalReservada, $idProduto);

    if (!$stmtUpdate->execute()) {
        header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=Não foi possível atualizar o estoque do produto. Refaça a operação e tente novamente");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Commit da transação se todas as operações foram bem-sucedidas
    $conn->commit();

    // Redirecionar para a página de sucesso apropriada
    header("Location: ../ViewSucess/SucessCreateReserva.php?sucesso=Reserva criada com sucesso");
    exit();

} catch (Exception $e) {
    // Em caso de erro, fazer rollback da transação
    $conn->rollback();

    // Registrar o erro
    error_log("Erro: " . $e->getMessage());

    // Redirecionar para a página de falha com uma mensagem de erro
    header("Location: ../ViewFail/FailCreateReserva.php?erro=Não foi possível criar a reserva do produto. Refaça a operação e tente novamente ");
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
