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

// Obter os dados do formulário e sanitizar
$idProduto = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$quantidadeInutilizada = filter_input(INPUT_POST, 'Inutilizar', FILTER_SANITIZE_NUMBER_INT);
$dataInutilizar = filter_input(INPUT_POST, 'DataInutilizar', FILTER_SANITIZE_SPECIAL_CHARS);
$observacao = filter_input(INPUT_POST, 'Observacao', FILTER_SANITIZE_SPECIAL_CHARS);

// Verificar se o campo observação excede 35 caracteres
if (mb_strlen($observacao, 'UTF-8') > 35) {
    header("Location: ../ViewFail/FailCreateObservacaoInvalida.php?erro=O campo observação excede o limite de 35 caracteres.");
    exit();
}

// Verificar se todos os campos obrigatórios estão presentes
if (empty($idProduto) || empty($quantidadeInutilizada) || empty($dataInutilizar) || empty($observacao)) {
    header("Location: ../ViewFail/FailCreateDadosIncompletos.php?erro=Todos os campos são obrigatórios");
    exit();
}

// Obter os dados do usuário da sessão
$idUsuario = $_SESSION['usuarioId'];
$nomeUsuario = $_SESSION['usuarioNome'];
$codigoPUsuario = $_SESSION['usuarioCodigoP'];

// Definir valores fixos
$operacao = "Inutilizar";
$situacao = "Inutilizado";

// Converter observação, operação e situação para letras maiúsculas usando mb_strtoupper
$observacao = mb_strtoupper($observacao, 'UTF-8');
$operacao = mb_strtoupper($operacao, 'UTF-8');
$situacao = mb_strtoupper($situacao, 'UTF-8');

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verificar se a quantidade é negativa
if ($quantidadeInutilizada <= 0) {
    header("Location: ../ViewFail/FailCreateQuantidadeNegativa.php?erro=Não é permitido o registro de valores negativos no campo de quantidade");
    exit(); // Termina a execução do script após redirecionamento
}

// Função para validar se a data de inutilização é válida
function dataSaoValida($dataInutilizar) {
    try {
        $timeZone = new DateTimeZone('America/Sao_Paulo');
        $dataInutilizarObj = DateTime::createFromFormat('Y-m-d', $dataInutilizar, $timeZone);
        $currentDateObj = new DateTime('now', $timeZone);

        if ($dataInutilizarObj === false) {
            return false;
        }

        $dataInutilizarFormatada = $dataInutilizarObj->format('Y-m-d');
        $currentDate = $currentDateObj->format('Y-m-d');

        return $dataInutilizarFormatada === $currentDate;
    } catch (Exception $e) {
        return false;
    }
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

    // Consulta para obter a quantidade atual no estoque
    $sqlSelectEstoque = "SELECT QUANTIDADE FROM ESTOQUE WHERE IDPRODUTO = ?";
    $stmtSelect = $conn->prepare($sqlSelectEstoque);
    $stmtSelect->bind_param("i", $idProduto);
    $stmtSelect->execute();
    $stmtSelect->bind_result($quantidadeAtual);
    $stmtSelect->fetch();
    $stmtSelect->close();

    // Verificar se a quantidade inutilizada é maior do que a quantidade atual no estoque
    if ($quantidadeInutilizada > $quantidadeAtual) {
        header("Location: ../ViewFail/FailCreateQuantidadeExcedeEstoque.php?erro=A quantidade inutilizada é superior à quantidade do estoque atual");
        exit();
    }

    // Inserir dados na tabela INUTILIZAR usando prepared statement
    $sqlInsertInutilizar = "INSERT INTO INUTILIZAR (QUANTIDADE, DATAINUTILIZAR, OBSERVACAO, OPERACAO, SITUACAO, IDPRODUTO, IDUSUARIO, NOME, CODIGOP) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsertInutilizar);
    $stmtInsert->bind_param("issssiiss", $quantidadeInutilizada, $dataInutilizar, $observacao, $operacao, $situacao, $idProduto, $idUsuario, $nomeUsuario, $codigoPUsuario);

    if (!$stmtInsert->execute()) {
        header("Location: ../ViewFail/FailCreateInserirDadosInutilizar.php?erro=Não foi possível inserir os dados na tabela INUTILIZAR");
        exit();
    }

    // Atualizar a tabela ESTOQUE subtraindo a quantidade inutilizada
    $sqlUpdateEstoque = "UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE - ? WHERE IDPRODUTO = ?";
    $stmtUpdate = $conn->prepare($sqlUpdateEstoque);
    $stmtUpdate->bind_param("ii", $quantidadeInutilizada, $idProduto);

    if (!$stmtUpdate->execute()) {
        header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=Não foi possível atualizar o estoque");
        exit();
    }

    // Verificar se a data de inutilização é válida
    if (!dataSaoValida($dataInutilizar)) {
        header("Location: ../ViewFail/FailCreateDataInvalida.php?erro=A data está fora do intervalo permitido");
        exit();
    }

    // Commit da transação se todas as operações foram bem-sucedidas
    $conn->commit();

    // Redirecionar para a página de sucesso
    header("Location: ../ViewSucess/SucessCreateAtualizaEstoque.php?sucesso=O estoque do produto foi atualizado com sucesso");
    exit(); // Termina a execução do script após redirecionamento
} catch (Exception $e) {
    // Em caso de erro, fazer rollback da transação
    $conn->rollback();

    // Registrar o erro em um log de servidor para análise futura
    error_log("Erro ao atualizar estoque: " . $e->getMessage());

    // Redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=Não foi possível atualizar o estoque");
    exit(); // Termina a execução do script após redirecionamento
} finally {
    // Fechar os statements
    if (isset($stmtInsert)) {
        $stmtInsert->close();
    }
    if (isset($stmtUpdate)) {
        $stmtUpdate->close();
    }
    
    // Fechar a conexão
    $conn->close();
}
?>
