<?php
// Iniciar sessão se necessário
session_start();

// Verificar se os dados do usuário estão disponíveis na sessão
if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['usuarioNome']) || !isset($_SESSION['usuarioCodigoP'])) {
    header("Location: ../ViewFail/FailCreateUsuarioNaoAutenticado.php?erro=O usuário não está autenticado. Realize o login novamente");
    exit(); // Termina a execução do script após redirecionamento
}

// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Função para validar quantidade
function validarQuantidade($quantidade) {
    return is_numeric($quantidade) && $quantidade >= 0;
}

// Função para validar data
function validarData($data) {
    $format = 'Y-m-d';
    $d = DateTime::createFromFormat($format, $data);
    return $d && $d->format($format) === $data;
}

// Função para validar se a data é a data atual
function datasSaoValidas($dataSobrepor) {
    try {
        $timeZone = new DateTimeZone('America/Sao_Paulo');
        $dataCadastroObj = DateTime::createFromFormat('Y-m-d', $dataSobrepor, $timeZone);
        $currentDateObj = new DateTime('now', $timeZone);
        $dataCadastroFormatada = $dataCadastroObj->format('Y-m-d');
        $currentDate = $currentDateObj->format('Y-m-d');
        return $dataCadastroFormatada === $currentDate;
    } catch (Exception $e) {
        return false;
    }
}

// Obter e validar os dados do formulário
$idProduto = $_POST['id'] ?? '';
$quantidadeSobrepor = $_POST['Sobrepor'] ?? '';
$dataSobrepor = $_POST['DataSobrepor'] ?? '';
$observacao = $_POST['Observacao'] ?? '';

// Validar se os campos obrigatórios foram preenchidos e se os dados são válidos
if (empty($idProduto) || !validarQuantidade($quantidadeSobrepor) || !validarData($dataSobrepor) || !datasSaoValidas($dataSobrepor)) {
    header("Location: ../ViewFail/FailCreateDadosInvalidos.php?erro=Os dados fornecidos são inválidos. Tente novamente ");
    exit();
}

// Obter os dados do usuário da sessão
$idUsuario = $_SESSION['usuarioId'];
$nomeUsuario = $_SESSION['usuarioNome'];
$codigoPUsuario = $_SESSION['usuarioCodigoP'];

// Definir valores fixos
$operacao = "Sobrepor";
$situacao = "Alteração";

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
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

    // Inserir dados na tabela SOBREPOR usando prepared statement
    $sqlInsertSobrepor = "INSERT INTO SOBREPOR (QUANTIDADE, DATASOBREPOR, OBSERVACAO, OPERACAO, SITUACAO, IDPRODUTO, IDUSUARIO, NOME, CODIGOP) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsertSobrepor);
    $stmtInsert->bind_param("isssssiis", $quantidadeSobrepor, $dataSobrepor, $observacao, strtoupper($operacao), strtoupper($situacao), $idProduto, $idUsuario, strtoupper($nomeUsuario), $codigoPUsuario);

    if (!$stmtInsert->execute()) {
        header("Location: ../ViewFail/FailCreateInserirDadosSobrepor.php?erro=Não foi possível inserir os dados na tabela SOBREPOR. Informe o departamento de TI");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Atualizar a tabela ESTOQUE com a quantidade sobreposta
    $sqlUpdateEstoque = "UPDATE ESTOQUE SET QUANTIDADE = ? WHERE IDPRODUTO = ?";
    $stmtUpdate = $conn->prepare($sqlUpdateEstoque);
    $stmtUpdate->bind_param("si", $quantidadeSobrepor, $idProduto);

    if (!$stmtUpdate->execute()) {
        header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=Não foi possível atualizar o estoque do produto. Refaça a operação e tente novamente");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Commit da transação se todas as operações foram bem-sucedidas
    $conn->commit();

    if ($temReserva) {
        header("Location: ../ViewSucess/SucessCreateAtualizaEstoqueComTransferencia.php?sucesso=O estoque do produto será atualizado após a confirmação das transferências pendentes");
    } else {
        header("Location: ../ViewSucess/SucessCreateAtualizaEstoque.php?sucesso=O estoque do produto foi atualizado com sucesso");
    }
    exit();
    
} catch (Exception $e) {
    // Em caso de erro, fazer rollback da transação
    $conn->rollback();

    // Registrar o erro
    error_log("Erro: " . $e->getMessage());

    // Redirecionar para a página de falha com uma mensagem de erro
    header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=Não foi possível atualizar o estoque do produto. Refaça a operação e tente novamente");
    exit(); // Termina a execução do script após redirecionamento
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
