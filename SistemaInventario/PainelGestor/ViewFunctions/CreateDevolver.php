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
$quantidadeDevolver = $_POST['Devolvida'] ?? '';
$datadevolucao = $_POST['DataDevolucao'] ?? '';
$observacao = $_POST['Observacao'] ?? '';

// Obter os dados do usuário da sessão
$idUsuario = $_SESSION['usuarioId'];
$nomeUsuario = $_SESSION['usuarioNome'];
$codigoPUsuario = $_SESSION['usuarioCodigoP'];

// Definir valores fixos
$operacao = "Devolução";
$situacao = "Devolvido";

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Sanitizar os dados de entrada para evitar injeção de SQL
$idProduto = $conn->real_escape_string($idProduto);
$numwo = $conn->real_escape_string($numwo);
$quantidadeDevolver = $conn->real_escape_string($quantidadeDevolver);
$datadevolucao = $conn->real_escape_string($datadevolucao);
$observacao = $conn->real_escape_string($observacao);
$idUsuario = $conn->real_escape_string($idUsuario);
$nomeUsuario = $conn->real_escape_string($nomeUsuario);
$codigoPUsuario = $conn->real_escape_string($codigoPUsuario);

// Verificar se a quantidade é positiva
if ($quantidadeDevolver <= 0) {
    // Se a quantidade for não positiva, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateQuantidadeNegativa.php?erro=Não é permitido o registro de valores negativos no campo de quantidade");
    exit(); // Termina a execução do script após redirecionamento
}

// Função para validar se a data de devolução é válida
function dataDevolucaoValida($datadevolucao) {
    try {
        // Definir a zona de tempo para as datas recebidas e a data atual do servidor
        $timeZone = new DateTimeZone('America/Sao_Paulo'); // Substitua pela sua zona de tempo

        // Converter as datas para objetos DateTime com a zona de tempo definida
        $dataDevolucaoObj = DateTime::createFromFormat('Y-m-d', $datadevolucao, $timeZone);
        $currentDateObj = new DateTime('now', $timeZone); // Data atual do servidor

        // Comparar apenas a parte da data (sem considerar horas, minutos, segundos)
        $dataDevolucaoFormatada = $dataDevolucaoObj->format('Y-m-d');
        $currentDate = $currentDateObj->format('Y-m-d');

        // Verificar se as datas recebidas são iguais à data atual do servidor
        if ($dataDevolucaoFormatada !== $currentDate) {
            return false;
        }

        return true;
    } catch (Exception $e) {
        // Tratar exceção de conversão de datas
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

    // Verificar se a data de devolução é válida
    if (!dataDevolucaoValida($datadevolucao)) {
        header("Location: ../ViewFail/FailCreateDataInvalida.php?erro=A data está fora do intervalo permitido ");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Inserir dados na tabela DEVOLVER usando prepared statement
    $sqlInsertDevolucao = "INSERT INTO DEVOLVER (NUMWO, QUANTIDADE, DATADEVOLUCAO, OBSERVACAO, OPERACAO, SITUACAO, IDPRODUTO, IDUSUARIO, NOME, CODIGOP) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmtInsert = $conn->prepare($sqlInsertDevolucao);
    $stmtInsert->bind_param("sissisiiss", $numwo, $quantidadeDevolver, $datadevolucao, $observacao, $operacao, $situacao, $idProduto, $idUsuario, $nomeUsuario, $codigoPUsuario);
    
    if (!$stmtInsert->execute()) {
        header("Location: ../ViewFail/FailCreateInserirDadosDevolver.php?erro=Não foi possível inserir os dados na tabela DEVOLVER");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Atualizar a tabela ESTOQUE adicionando a quantidade devolvida
    $sqlUpdateEstoque = "UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE + ? WHERE IDPRODUTO = ?";
    
    $stmtUpdate = $conn->prepare($sqlUpdateEstoque);
    $stmtUpdate->bind_param("ii", $quantidadeDevolver, $idProduto);
    
    if (!$stmtUpdate->execute()) {
        header("Location: ../ViewFail/FailCreaAtualizaEstoque.php?erro=Não foi possível atualizar o estoque do produto ");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Commit da transação se todas as operações foram bem-sucedidas
    $conn->commit();

    if ($temReserva) {
        header("Location: ../ViewSucess/SucessCreateAtualizaEstoqueComTransferencia.php");
    } else {
        header("Location: ../ViewSucess/SucessCreateAtualizaEstoque.php");
    }
    exit(); // Termina a execução do script após redirecionamento
} catch (Exception $e) {
    // Em caso de erro, fazer rollback da transação
    $conn->rollback();

    // Redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateDevolucao.php?erro=Não foi possível realizar a devolução do produto. Erro: " . $e->getMessage());
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
