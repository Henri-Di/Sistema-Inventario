<?php
// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Obter o ID da nota fiscal a ser atualizada
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Verificar se os dados do formulário foram submetidos
if (!empty($_POST['id'])) {
    // Sanitizar os dados de entrada
    $id = $conn->real_escape_string($_POST['id']);
    $numNotaFiscal = $conn->real_escape_string($_POST['NumNotaFiscal'] ?? '');
    $valorNotaFiscal = $conn->real_escape_string($_POST['ValorNotaFiscal'] ?? '');
    $material = $conn->real_escape_string($_POST['Material'] ?? '');
    $conector = $conn->real_escape_string($_POST['Conector'] ?? '');
    $metragem = $conn->real_escape_string($_POST['Metragem'] ?? '');
    $modelo = $conn->real_escape_string($_POST['Modelo'] ?? '');
    $quantidade = $conn->real_escape_string($_POST['Quantidade'] ?? '');
    $fornecedor = $conn->real_escape_string($_POST['Fornecedor'] ?? '');
    $dataRecebimento = $conn->real_escape_string($_POST['DataRecebimento'] ?? '');
    $dataCadastro = $conn->real_escape_string($_POST['DataCadastro'] ?? '');
    $datacenter = $conn->real_escape_string($_POST['DataCenter'] ?? '');

    // Verificar se o datacenter existe
    $sqlDatacenter = "SELECT IDDATACENTER FROM DATACENTER WHERE NOME = ?";
    $stmtDatacenter = $conn->prepare($sqlDatacenter);
    $stmtDatacenter->bind_param("s", $datacenter);
    $stmtDatacenter->execute();
    $stmtDatacenter->store_result();
    $stmtDatacenter->bind_result($idDatacenter);
    $stmtDatacenter->fetch();

    if ($stmtDatacenter->num_rows === 0) {
        // Redirecionar para a página de falha se o datacenter não for encontrado
        header("Location: ../ViewFail/FailCreateModificaNotaFiscal.php?erro=Não foi possível realizar a alteração da nota fiscal");
        exit();
    }
    
    $stmtDatacenter->close();

    // Construir a consulta SQL para atualização
    $sql = "UPDATE NOTAFISCAL 
            SET NUMNOTAFISCAL='$numNotaFiscal', VALORNOTAFISCAL='$valorNotaFiscal', MATERIAL='$material', CONECTOR='$conector', METRAGEM='$metragem', MODELO='$modelo', QUANTIDADE='$quantidade', FORNECEDOR='$fornecedor', DATARECEBIMENTO='$dataRecebimento', DATACADASTRO='$dataCadastro', IDDATACENTER='$idDatacenter' 
            WHERE ID='$id'";

    // Executar a consulta SQL
    if (mysqli_query($conn, $sql)) {
        // Redirecionar para a página de sucesso
        header("Location: ../ViewSucess/SucessCreateModificaNotaFiscal.php");
        exit(); // Termina a execução do script após redirecionamento
    } else {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateModificaNotaFiscal.php?erro=?erro=Não foi possível realizar a alteração da nota fiscal");
        exit(); // Termina a execução do script após redirecionamento
    }
} else {
    // Caso nenhum dado tenha sido submetido, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateModificaNotaFiscal.php?erro=?erro=Não foi possível realizar a alteração da nota fiscal");
    exit(); // Termina a execução do script após redirecionamento
}

// Fechar a conexão
$conn->close();
?>
