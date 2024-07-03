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
$numwo = $_POST['NumWo'];
$quantidadeSubtracao = $_POST['Subtracao'] ?? '';
$dataSubtracao = $_POST['DataSubtracao'] ?? '';
$observacao = $_POST['Observacao'] ?? '';

// Obter os dados do usuário da sessão
$idUsuario = $_SESSION['usuarioId'];
$nomeUsuario = $_SESSION['usuarioNome'];
$codigoPUsuario = $_SESSION['usuarioCodigoP'];

// Definir valores fixos
$operacao = "Subtração";
$situacao = "Diminuido";

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Sanitizar os dados de entrada para evitar injeção de SQL
$idProduto = $conn->real_escape_string($idProduto);
$numwo = $conn->real_escape_string($numwo);
$quantidadeSubtracao = $conn->real_escape_string($quantidadeSubtracao);
$dataSubtracao = $conn->real_escape_string($dataSubtracao);
$observacao = $conn->real_escape_string($observacao);
$idUsuario = $conn->real_escape_string($idUsuario);
$nomeUsuario = $conn->real_escape_string($nomeUsuario);
$codigoPUsuario = $conn->real_escape_string($codigoPUsuario);

// Verificar se a quantidade é positiva
if ($quantidadeSubtracao <= 0) {
    // Se a quantidade for negativa ou zero, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateQuantidadeNegativa.php?erro= Não é permitido o registro de valores negativos no campo de quantidade");
    exit(); // Termina a execução do script após redirecionamento
}

// Função para validar se as datas de recebimento e cadastro são válidas
function datasSaoValidas($dataSubtracao) {
    try {
        // Definir a zona de tempo para as datas recebidas e a data atual do servidor
        $timeZone = new DateTimeZone('America/Sao_Paulo'); // Substitua pela sua zona de tempo

        // Converter as datas para objetos DateTime com a zona de tempo definida
        $dataCadastroObj = DateTime::createFromFormat('Y-m-d', $dataSubtracao, $timeZone);
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

    // Verificar se há reservas para o produto
    $sqlVerificaReserva = "SELECT RESERVADO FROM ESTOQUE WHERE IDPRODUTO = ?";
    $stmtVerificaReserva = $conn->prepare($sqlVerificaReserva);
    $stmtVerificaReserva->bind_param("i", $idProduto);
    $stmtVerificaReserva->execute();
    $stmtVerificaReserva->bind_result($reservado);
    $stmtVerificaReserva->fetch();
    $stmtVerificaReserva->close();
    
    $temReserva = $reservado > 0;

    // Inserir dados na tabela SUBTRACAO usando prepared statement
    $sqlInsertSubtracao = "INSERT INTO SUBTRACAO (NUMWO, QUANTIDADE, DATASUBTRACAO, OBSERVACAO, OPERACAO, SITUACAO, IDPRODUTO, IDUSUARIO, NOME, CODIGOP) 
                           VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmtInsert = $conn->prepare($sqlInsertSubtracao);
    $stmtInsert->bind_param("sisssssiss", $numwo, $quantidadeSubtracao, $dataSubtracao, $observacao, $operacao, $situacao, $idProduto, $idUsuario, $nomeUsuario, $codigoPUsuario);
    
    if (!$stmtInsert->execute()) {
        header("Location: ../ViewFail/FailCreateInserirDadosSubtracao.php?erro=Não foi possível inserir os dados na tabela SUBTRACAO. Informe o departamento de TI");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Atualizar a tabela ESTOQUE subtraindo a quantidade
    $sqlUpdateEstoque = "UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE - ? WHERE IDPRODUTO = ?";
    
    $stmtUpdate = $conn->prepare($sqlUpdateEstoque);
    $stmtUpdate->bind_param("ii", $quantidadeSubtracao, $idProduto);
    
    if (!$stmtUpdate->execute()) {
        header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=Não foi possivel atualizar o estoque do produto. Refaça a operação e tente novamente");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Verificar se a data de recebimento e data de cadastro são válidas
    if (!datasSaoValidas($dataSubtracao)) {
        header("Location: ../ViewFail/FailCreateDataInvalida.php?erro=A data está fora do intervalo permitido. A data deve ser igual da data atual ");
        exit();
    }

    // Commit da transação se todas as operações foram bem-sucedidas
    $conn->commit();


    // Redirecionar para a página apropriada com base na existência de reservas
    if ($temReserva) {
        header("Location: ../ViewSucess/SucessCreateAtualizaEstoqueComTransferencia.php?sucesso=O estoque do produto será atualizado após a confirmação das transferências pendentes");
    } else {
        header("Location: ../ViewSucess/SucessCreateAtualizaEstoque.php?sucesso=O estoque do produto foi atualizado com sucesso");
    }
    exit();

} catch (Exception $e) {
    // Em caso de erro, fazer rollback da transação
    $conn->rollback();

    // Exibir mensagem de erro
    echo "Erro: " . $e->getMessage();

    // Redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateAtualizaEstoque.php?erro=Não foi possível atualizar o estoque do produto. Refaça a operação e tente novamente ");
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
