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
$idProduto = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$numwo = filter_input(INPUT_POST, 'NumWo', FILTER_SANITIZE_SPECIAL_CHARS);
$quantidadeDevolver = filter_input(INPUT_POST, 'Devolvida', FILTER_SANITIZE_NUMBER_INT);
$datadevolucao = filter_input(INPUT_POST, 'DataDevolucao', FILTER_SANITIZE_SPECIAL_CHARS);
$observacao = filter_input(INPUT_POST, 'Observacao', FILTER_SANITIZE_SPECIAL_CHARS);

// Verificar se o campo observação excede 35 caracteres
if (mb_strlen($observacao, 'UTF-8') > 35) {
    header("Location: ../ViewFail/FailCreateObservacaoInvalida.php?erro=O campo observação excede o limite de 35 caracteres.");
    exit();
}

// Verificar se os dados obrigatórios estão preenchidos
if (!$idProduto || !$numwo || !$quantidadeDevolver || !$datadevolucao) {
    header("Location: ../ViewFail/FailCreateDadosInvalidos.php?erro=Os dados fornecidos são inválidos. Tente novamente");
    exit(); // Termina a execução do script após redirecionamento
}

// Verificar se a quantidade é positiva
if ($quantidadeDevolver <= 0) {
    // Se a quantidade for não positiva, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateQuantidadeNegativa.php?erro=Não é permitido o registro de valores negativos no campo de quantidade");
    exit(); // Termina a execução do script após redirecionamento
}

// Função para validar se a data de devolução é válida
function dataDevolucaoValida($datadevolucao) {
    try {
        $timeZone = new DateTimeZone('America/Sao_Paulo'); // Substitua pela sua zona de tempo
        $dataDevolucaoObj = DateTime::createFromFormat('Y-m-d', $datadevolucao, $timeZone);
        $currentDateObj = new DateTime('now', $timeZone); // Data atual do servidor

        $dataDevolucaoFormatada = $dataDevolucaoObj->format('Y-m-d');
        $currentDate = $currentDateObj->format('Y-m-d');

        return $dataDevolucaoFormatada === $currentDate;
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

    // Verificar se a data de devolução é válida
    if (!dataDevolucaoValida($datadevolucao)) {
        header("Location: ../ViewFail/FailCreateDataInvalida.php?erro=A data está fora do intervalo permitido. A data dever ser igual a data atual");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Converter os valores para letras maiúsculas
    $numwo = mb_strtoupper($numwo, 'UTF-8');
    $observacao = mb_strtoupper($observacao, 'UTF-8');
    $nomeUsuario = mb_strtoupper($_SESSION['usuarioNome'], 'UTF-8');
    $codigoPUsuario = mb_strtoupper($_SESSION['usuarioCodigoP'], 'UTF-8');

    // Definir valores fixos
    $operacao = "DEVOLUÇÃO";
    $situacao = "DEVOLVIDO";

    // Inserir dados na tabela DEVOLVER usando prepared statement
    $sqlInsertDevolucao = "INSERT INTO DEVOLVER (NUMWO, QUANTIDADE, DATADEVOLUCAO, OBSERVACAO, OPERACAO, SITUACAO, IDPRODUTO, IDUSUARIO, NOME, CODIGOP) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmtInsert = $conn->prepare($sqlInsertDevolucao);
    $stmtInsert->bind_param("sissisiiss", $numwo, $quantidadeDevolver, $datadevolucao, $observacao, $operacao, $situacao, $idProduto, $_SESSION['usuarioId'], $nomeUsuario, $codigoPUsuario);
    
    if (!$stmtInsert->execute()) {
        header("Location: ../ViewFail/FailCreateInserirDadosDevolver.php?erro=Não foi possível inserir os dados na tabela DEVOLVER. Informe o departamento de TI");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Atualizar a tabela ESTOQUE adicionando a quantidade devolvida
    $sqlUpdateEstoque = "UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE + ? WHERE IDPRODUTO = ?";
    
    $stmtUpdate = $conn->prepare($sqlUpdateEstoque);
    $stmtUpdate->bind_param("ii", $quantidadeDevolver, $idProduto);
    
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
    exit(); // Termina a execução do script após redirecionamento
} catch (Exception $e) {
    // Em caso de erro, fazer rollback da transação
    $conn->rollback();

    // Redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateDevolucao.php?erro=Não foi possivel realizar a devolução do produto. Refaça a operação e tente novamente");
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
