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
    exit(); // Termina a execução do script após redirecionamento
}

// Função para sanitizar os dados
function sanitizeData($conn, $data) {
    // Remove espaços em branco no início e no fim
    $data = trim($data);
    // Remove barras invertidas adicionadas automaticamente
    $data = stripslashes($data);
    // Escapa caracteres especiais para evitar SQL Injection
    $data = $conn->real_escape_string($data);
    // Converte para maiúsculas
    $data = mb_strtoupper($data, 'UTF-8');
    return $data;
}

// Obter os dados do formulário e sanitizá-los
$idProduto = isset($_POST['id']) ? sanitizeData($conn, $_POST['id']) : '';
$quantidadeInutilizada = isset($_POST['Inutilizar']) ? sanitizeData($conn, $_POST['Inutilizar']) : '';
$dataInutilizar = isset($_POST['DataInutilizar']) ? sanitizeData($conn, $_POST['DataInutilizar']) : '';
$observacao = isset($_POST['Observacao']) ? sanitizeData($conn, $_POST['Observacao']) : '';

// Obter os dados do usuário da sessão e sanitizá-los
$idUsuario = sanitizeData($conn, $_SESSION['usuarioId']);
$nomeUsuario = sanitizeData($conn, $_SESSION['usuarioNome']);
$codigoPUsuario = sanitizeData($conn, $_SESSION['usuarioCodigoP']);

// Definir valores fixos
$operacao = "INUTILIZAR";
$situacao = "INUTILIZADO";

// Verificar se o campo observação excede 35 caracteres
if (mb_strlen($observacao, 'UTF-8') > 35) {
    header("Location: ../ViewFail/FailCreateObservacaoInvalida.php?erro=" . urlencode("O campo observação excede o limite de 35 caracteres. Refaça a operação e tente novamente"));
    exit();
}

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verificar se a quantidade é negativa
if ($quantidadeInutilizada <= 0) {
    header("Location: ../ViewFail/FailCreateQuantidadeNegativa.php?erro=" . urlencode("Não é permitido o registro de valores negativos no campo de quantidade"));
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
    $sqlVerificaReserva = "SELECT RESERVADO_TRANSFERENCIA FROM ESTOQUE WHERE IDPRODUTO = ?";
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
        header("Location: ../ViewFail/FailCreateQuantidadeExcedeEstoque.php?erro=" . urlencode("A quantidade inutilizada é superior à quantidade do estoque atual"));
        exit();
    }

    // Inserir dados na tabela INUTILIZAR usando prepared statement
    $sqlInsertInutilizar = "INSERT INTO INUTILIZAR (QUANTIDADE, DATAINUTILIZAR, OBSERVACAO, OPERACAO, SITUACAO, IDPRODUTO, IDUSUARIO, NOME, CODIGOP) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsertInutilizar);
    $stmtInsert->bind_param("issssiiss", $quantidadeInutilizada, $dataInutilizar, $observacao, $operacao, $situacao, $idProduto, $idUsuario, $nomeUsuario, $codigoPUsuario);

    if (!$stmtInsert->execute()) {
        header("Location: ../ViewFail/FailCreateInserirDadosInutilizar.php?erro=" . urlencode("Não foi possível inserir os dados na tabela INUTILIZAR. Informe o departamento de TI"));
        exit();
    }

    // Atualizar a tabela ESTOQUE subtraindo a quantidade inutilizada
    $sqlUpdateEstoque = "UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE - ? WHERE IDPRODUTO = ?";
    $stmtUpdate = $conn->prepare($sqlUpdateEstoque);
    $stmtUpdate->bind_param("ii", $quantidadeInutilizada, $idProduto);

    if (!$stmtUpdate->execute()) {
        header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=" . urlencode("Não foi possível atualizar o estoque do produto. Refaça a operação e tente novamente"));
        exit();
    }

    // Verificar se a data de inutilização é válida
    if (!dataSaoValida($dataInutilizar)) {
        header("Location: ../ViewFail/FailCreateDataInvalida.php?erro=" . urlencode("A data está fora do intervalo permitido, a data deve ser igual a data atual. Refaça a operação e tente novamente"));
        exit();
    }

    // Commit da transação se todas as operações foram bem-sucedidas
    $conn->commit();

    // Redirecionar para a página de sucesso
    header("Location: ../ViewSucess/SucessCreateAtualizaEstoque.php?sucesso=" . urlencode("O estoque do produto foi atualizado com sucesso"));
    exit(); // Termina a execução do script após redirecionamento
} catch (Exception $e) {
    // Em caso de erro, fazer rollback da transação
    $conn->rollback();

    // Registrar o erro em um log de servidor para análise futura
    error_log("Erro ao atualizar estoque: " . $e->getMessage());

    // Redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=" . urlencode("Não foi possível atualizar o estoque do produto. Refaça a operação e tente novamente"));
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
