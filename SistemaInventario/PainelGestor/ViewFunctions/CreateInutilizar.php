<?php
// Iniciar sessão se necessário
session_start();

// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Verificar se os dados do usuário estão disponíveis na sessão
if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['usuarioNome']) || !isset($_SESSION['usuarioCodigoP'])) {
    header("Location: ../ViewFail/FailCreateUsuarioNaoAutenticado.php?erro=O usuário não está autenticado. Realize o login novamente");
    exit(); // Termina a execução do script após redirecionamento("Erro: Usuário não autenticado.");
}

// Obter os dados do formulário
$idProduto = $_POST['id'] ?? '';
$quantidadeInutilizada = $_POST['Inutilizar'] ?? '';
$dataInutilizar = $_POST['DataInutilizar'] ?? '';
$observacao = $_POST['Observacao'] ?? '';

// Obter os dados do usuário da sessão
$idUsuario = $_SESSION['usuarioId'];
$nomeUsuario = $_SESSION['usuarioNome'];
$codigoPUsuario = $_SESSION['usuarioCodigoP'];

// Definir valores fixos
$operacao = "Inutilizar";
$situacao = "Inutilizado";

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Sanitizar os dados de entrada para evitar injeção de SQL
$idProduto = $conn->real_escape_string($idProduto);
$quantidadeInutilizada = $conn->real_escape_string($quantidadeInutilizada);
$dataInutilizar = $conn->real_escape_string($dataInutilizar);
$observacao = $conn->real_escape_string($observacao);
$idUsuario = $conn->real_escape_string($idUsuario);
$nomeUsuario = $conn->real_escape_string($nomeUsuario);
$codigoPUsuario = $conn->real_escape_string($codigoPUsuario);

// Verificar se a quantidade é negativa
if ($quantidadeInutilizada <= 0) {
    // Se a quantidade for não positiva, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateQuantidadeNegativa.php?erro=Não é permitido o registro de valores negativos no campo de quantidade");
    exit(); // Termina a execução do script após redirecionamento
}

// Função para validar se as datas de recebimento e cadastro são válidas
function datasSaoValidas($dataInutilizar) {
    try {
        // Definir a zona de tempo para as datas recebidas e a data atual do servidor
        $timeZone = new DateTimeZone('America/Sao_Paulo'); // Substitua pela sua zona de tempo

        // Converter as datas para objetos DateTime com a zona de tempo definida
        $dataCadastroObj = DateTime::createFromFormat('Y-m-d', $dataInutilizar, $timeZone);
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


// Iniciar transação para garantir consistência
$conn->begin_transaction();

try {
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
        // Se a quantidade inutilizada for maior do que a quantidade disponível no estoque, redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateQuantidadeExcedeEstoque.php?erro=A quantidade inutilizada e superior a quantidade do estoque atual");
        exit();
    }


    // Inserir dados na tabela INUTILIZAR usando prepared statement
    $sqlInsertInutilizar = "INSERT INTO INUTILIZAR (QUANTIDADE, DATAINUTILIZAR, OBSERVACAO, OPERACAO, SITUACAO, IDPRODUTO, IDUSUARIO, NOME, CODIGOP) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmtInsert = $conn->prepare($sqlInsertInutilizar);
    $stmtInsert->bind_param("issssiiss", $quantidadeInutilizada, $dataInutilizar, $observacao, $operacao, $situacao, $idProduto, $idUsuario, $nomeUsuario, $codigoPUsuario);
    
    if (!$stmtInsert->execute()) {
        header("Location: ../ViewFail/FailCreateInserirDadosInutilizar.phperro=Não foi possível inserir os dados na tabela INUTILIZAR");
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

    // Verificar se a data de recebimento e data de cadastro são válidas
    if (!datasSaoValidas($dataInutilizar)) {
        header("Location: ../ViewFail/FailCreateDataInvalida.php?erro=A data está fora do intervalo permitido ");
        exit();
    }


    // Commit da transação se todas as operações foram bem-sucedidas
    $conn->commit();

    // Redirecionar para a página de sucesso
    header("Location: ../ViewSucess/SucessCreateAtualizaEstoque.php");
    exit(); // Termina a execução do script após redirecionamento
} catch (Exception $e) {
    // Em caso de erro, fazer rollback da transação
    $conn->rollback();

    // Exibir mensagem de erro
    echo "Erro: " . $e->getMessage();

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
