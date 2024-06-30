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

// Obter os dados do formulário
$idProduto = $_POST['id'] ?? '';
$numwo = $_POST['NumWo'] ?? '';
$quantidadeReservar = $_POST['Reservar'] ?? '';
$datareserva = $_POST['DataReserva'] ?? '';
$observacao = $_POST['Observacao'] ?? '';

// Obter os dados do usuário da sessão
$idUsuario = $_SESSION['usuarioId'];
$nomeUsuario = $_SESSION['usuarioNome'];
$codigoPUsuario = $_SESSION['usuarioCodigoP'];

// Definir valores fixos
$operacao = "Reservar";
$situacao = "Reservado";

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Sanitizar os dados de entrada para evitar injeção de SQL
$idProduto = $conn->real_escape_string($idProduto);
$numwo = $conn->real_escape_string($numwo);
$quantidadeReservar = $conn->real_escape_string($quantidadeReservar);
$datareserva = $conn->real_escape_string($datareserva);
$observacao = $conn->real_escape_string($observacao);
$idUsuario = $conn->real_escape_string($idUsuario);
$nomeUsuario = $conn->real_escape_string($nomeUsuario);
$codigoPUsuario = $conn->real_escape_string($codigoPUsuario);

// Verificar se a quantidade é negativa
if ($quantidadeReservar <= 0) {
    // Se a quantidade for não positiva, redirecionar para a página de falha
    header("Location:../ViewFail/FailCreateQuantidadeNegativa.php?erro=Não é permitido o registro de valores negativos no campo de quantidade");
    exit(); // Termina a execução do script após redirecionamento
}

// Função para validar se a data de acréscimo é válida
function datasSaoValidas($datareserva) {
    try {
        $timeZone = new DateTimeZone('America/Sao_Paulo'); // Substitua pela sua zona de tempo
        $datareservaObj = DateTime::createFromFormat('Y-m-d', $datareserva, $timeZone);
        $currentDateObj = new DateTime('now', $timeZone); // Data atual do servidor
        $datareservaFormatada = $datareservaObj->format('Y-m-d');
        $currentDate = $currentDateObj->format('Y-m-d');
        if ($datareservaFormatada !== $currentDate) {
            return false;
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Verificar se a data de acréscimo é válida
if (!datasSaoValidas($datareserva)) {
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

    // Inserir dados na tabela RESERVA usando prepared statement
    $sqlInsertReserva = "INSERT INTO RESERVA (NUMWO, QUANTIDADE, DATARESERVA, OBSERVACAO, OPERACAO, SITUACAO, IDPRODUTO, IDUSUARIO, NOME, CODIGOP) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmtInsert = $conn->prepare($sqlInsertReserva);
    $stmtInsert->bind_param("sissisiiss", $numwo, $quantidadeReservar, $datareserva, $observacao, $operacao, $situacao, $idProduto, $idUsuario, $nomeUsuario, $codigoPUsuario);
    
    if (!$stmtInsert->execute()) {
        header("Location: ../ViewFail/FailCreateInserirDadosReserva.php?erro=Não foi possível inserir os dados na tabela RESERVA");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Atualizar a tabela ESTOQUE subtraindo a quantidade reservada
    $sqlUpdateEstoque = "UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE - ?, RESERVADO = RESERVADO + ? WHERE IDPRODUTO = ?";
    
    $stmtUpdate = $conn->prepare($sqlUpdateEstoque);
    $stmtUpdate->bind_param("iii", $quantidadeReservar, $quantidadeReservar, $idProduto);
    
    if (!$stmtUpdate->execute()) {
        header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=Não foi possivel atualizar o estoque do produto");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Commit da transação se todas as operações foram bem-sucedidas
    $conn->commit();

    if ($temReserva) {
        header("Location: ../ViewSucess/SucessCreateAtualizaEstoqueComTransferencia.php");
    } else {
        header("Location: ../ViewSucess/SucessCreateAtualizaEstoque.php");
    }
    exit();

} catch (Exception $e) {
    // Em caso de erro, fazer rollback da transação
    $conn->rollback();

    // Exibir mensagem de erro
    echo "Erro: " . $e->getMessage();

    // Redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateReserva.php?erro=Não foi possível criar a reserva do produto");
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
