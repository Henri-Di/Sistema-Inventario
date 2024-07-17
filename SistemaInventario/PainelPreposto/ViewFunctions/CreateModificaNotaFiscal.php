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
        header("Location: ../ViewFail/FailCreateModificaNotaFiscal.php?erro=" . urlencode("Não foi possível realizar a alteração da nota fiscal. Datacenter não encontrado."));
        exit();
    }
    
    $stmtDatacenter->close();

    // Construir a consulta SQL para atualização (usando prepared statement para segurança adicional)
    $sql = "UPDATE NOTAFISCAL 
            SET NUMNOTAFISCAL=?, VALORNOTAFISCAL=?, MATERIAL=?, CONECTOR=?, METRAGEM=?, MODELO=?, QUANTIDADE=?, FORNECEDOR=?, DATARECEBIMENTO=?, DATACADASTRO=?, IDDATACENTER=?
            WHERE ID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssi", $numNotaFiscal, $valorNotaFiscal, $material, $conector, $metragem, $modelo, $quantidade, $fornecedor, $dataRecebimento, $dataCadastro, $idDatacenter, $id);

    // Executar a consulta preparada
    if ($stmt->execute()) {
        // Redirecionar para a página de sucesso
        header("Location: ../ViewSucess/SucessCreateModificaNotaFiscal.php?sucesso=" . urlencode("A alteração foi realizada com sucesso na nota fiscal"));
        exit(); // Termina a execução do script após redirecionamento
    } else {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateModificaNotaFiscal.php?erro=" . urlencode("Não foi possível realizar a alteração da nota fiscal. Refaça a operação e tente novamente"));
        exit(); // Termina a execução do script após redirecionamento
    }

    // Fechar o statement
    $stmt->close();
} else {
    // Caso nenhum dado tenha sido submetido, redirecionar para a página de falha
    header("Location: ../ViewFail/FailCreateModificaNotaFiscal.php?erro=" . urlencode("Não foi possível realizar a alteração da nota fiscal. Refaça a operação e tente novamente"));
    exit(); // Termina a execução do script após redirecionamento
}

// Fechar a conexão
$conn->close();
?>
