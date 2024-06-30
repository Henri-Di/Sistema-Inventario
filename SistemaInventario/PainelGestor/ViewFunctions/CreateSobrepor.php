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
$quantidadeSobrepor = $_POST['Sobrepor'] ?? '';
$dataSobrepor = $_POST['DataSobrepor'] ?? '';
$observacao = $_POST['Observacao'] ?? '';

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

// Sanitizar os dados de entrada para evitar injeção de SQL
$idProduto = $conn->real_escape_string($idProduto);
$quantidadeSobrepor = $conn->real_escape_string($quantidadeSobrepor);
$dataSobrepor = $conn->real_escape_string($dataSobrepor);
$observacao = $conn->real_escape_string($observacao);
$idUsuario = $conn->real_escape_string($idUsuario);
$nomeUsuario = $conn->real_escape_string($nomeUsuario);
$codigoPUsuario = $conn->real_escape_string($codigoPUsuario);

// Verificar se a quantidade é negativa
if ($quantidadeSobrepor < 0) {
    // Se a quantidade for negativa, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateQuantidadeNegativa.php?erro=Não é permitido o registro de valores negativos no campo de quantidade");
    exit(); // Termina a execução do script após redirecionamento
}

// Função para validar se as datas de recebimento e cadastro são válidas
function datasSaoValidas($dataSobrepor) {
    try {
        // Definir a zona de tempo para as datas recebidas e a data atual do servidor
        $timeZone = new DateTimeZone('America/Sao_Paulo'); // Substitua pela sua zona de tempo

        // Converter as datas para objetos DateTime com a zona de tempo definida
        $dataCadastroObj = DateTime::createFromFormat('Y-m-d', $dataSobrepor, $timeZone);
        $currentDateObj = new DateTime('now', $timeZone); // Data atual do servidor

        // Comparar apenas a parte da data (sem considerar horas, minutos, segundos)
        $dataCadastroFormatada = $dataCadastroObj->format('Y-m-d');
        $currentDate = $currentDateObj->format('Y-m-d');

        // Verificar se as datas recebidas são iguais à data atual do servidor
        if ($dataCadastroFormatada !== $currentDate) {
            return false;
        }

        return true;
    } catch (Exception $e) {
        // Tratar exceção de conversão de datas
        return false;
    }
}

// Verificar se a data de recebimento e data de cadastro são válidas
if (!datasSaoValidas($dataSobrepor)) {
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
        
    // Inserir dados na tabela SOBREPOR
    $sqlInsertSobrepor = "INSERT INTO SOBREPOR (QUANTIDADE, DATASOBREPOR, OBSERVACAO, OPERACAO, SITUACAO, IDPRODUTO, IDUSUARIO, NOME, CODIGOP) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsertSobrepor);
    $stmtInsert->bind_param("isssssiis", $quantidadeSobrepor, $dataSobrepor, $observacao, $operacao, $situacao, $idProduto, $idUsuario, $nomeUsuario, $codigoPUsuario);

    if (!$stmtInsert->execute()) {
        throw new Exception("Não foi possível inserir os dados na tabela SOBREPOR");
    }

    // Atualizar a tabela ESTOQUE com a quantidade sobreposta
    $sqlUpdateEstoque = "UPDATE ESTOQUE SET QUANTIDADE = ? WHERE IDPRODUTO = ?";
    $stmtUpdate = $conn->prepare($sqlUpdateEstoque);
    $stmtUpdate->bind_param("ii", $quantidadeSobrepor, $idProduto);

    if (!$stmtUpdate->execute()) {
        throw new Exception("Não foi possível atualizar o estoque do produto");
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

    // Redirecionar para a página de falha com a mensagem de erro
    header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=" . $e->getMessage());
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
