<?php
session_start();

// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Função para sanitizar entrada de dados
function sanitize($conn, $input) {
    return $conn->real_escape_string($input);
}

// Função para validar se as datas de recebimento e cadastro são válidas
function datasSaoValidas($datacadastro) {
    try {
        // Definir a zona de tempo para as datas recebidas e a data atual do servidor
        $timeZone = new DateTimeZone('America/Sao_Paulo'); // Substitua pela sua zona de tempo

        // Converter as datas para objetos DateTime com a zona de tempo definida
        $dataCadastroObj = DateTime::createFromFormat('Y-m-d', $datacadastro, $timeZone);
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

// Função para obter ou inserir e obter o ID de uma tabela
function getIdOrInsert($conn, $table, $column, $idColumn, $value) {
    $checkSql = "SELECT $idColumn FROM $table WHERE $column = '$value'";
    $result = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row[$idColumn];
    } else {
        $insertSql = "INSERT INTO $table ($column) VALUES ('$value')";
        if (mysqli_query($conn, $insertSql)) {
            return $conn->insert_id;
        } else {
            throw new Exception("Erro ao inserir na tabela $table");
        }
    }
}

// Verificar se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter os dados do formulário e sanitizar
    $material = sanitize($conn, $_POST['Material'] ?? '');
    $conector = sanitize($conn, $_POST['Conector'] ?? '');
    $metragem = sanitize($conn, $_POST['Metragem'] ?? '');
    $modelo = sanitize($conn, $_POST['Modelo'] ?? '');
    $quantidade = sanitize($conn, $_POST['Quantidade'] ?? '');
    $fornecedor = sanitize($conn, $_POST['Fornecedor'] ?? '');
    $datacadastro = sanitize($conn, $_POST['DataCadastro'] ?? '');
    $datacenterNome = sanitize($conn, $_POST['DataCenter'] ?? '');

    // Verificar se a quantidade é negativa
    if ($quantidade < 0) {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateQuantidadeNegativa.php?erro=Não é permitido o registro de valores negativos no campo de quantidade");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Verificar se a data de cadastro é válida
    if (!datasSaoValidas($datacadastro)) {
        // Redirecionar para a página de falha com mensagem de erro
        header("Location: ../ViewFail/FailCreateDataInvalida.php?erro=A data está fora do intervalo permitido");
        exit(); // Termina a execução do script após redirecionamento
    }

    try {
        $conn->begin_transaction();

        $idMaterial = getIdOrInsert($conn, 'MATERIAL', 'MATERIAL', 'IDMATERIAL', $material);
        $idConector = getIdOrInsert($conn, 'CONECTOR', 'CONECTOR', 'IDCONECTOR', $conector);
        $idMetragem = getIdOrInsert($conn, 'METRAGEM', 'METRAGEM', 'IDMETRAGEM', $metragem);
        $idModelo = getIdOrInsert($conn, 'MODELO', 'MODELO', 'IDMODELO', $modelo);
        $idFornecedor = getIdOrInsert($conn, 'FORNECEDOR', 'FORNECEDOR', 'IDFORNECEDOR', $fornecedor);
        $idDataCenter = getIdOrInsert($conn, 'DATACENTER', 'NOME', 'IDDATACENTER', $datacenterNome);

        // Verificar se um produto com os mesmos detalhes já existe no mesmo datacenter
        $check_sql = "SELECT p.* FROM PRODUTO p
                      WHERE p.IDMATERIAL = '$idMaterial'
                      AND p.IDCONECTOR = '$idConector'
                      AND p.IDMETRAGEM = '$idMetragem'
                      AND p.IDMODELO = '$idModelo'
                      AND p.IDFORNECEDOR = '$idFornecedor'
                      AND p.IDDATACENTER = '$idDataCenter'";
        $result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            // Se o produto já existe, redirecionar para a página de falha
            header("Location: ../ViewFail/FailCreateProdutoExistente.php?erro=Não foi possivel realizar o cadastro. Produto já cadastrado");
            exit(); // Termina a execução do script após redirecionamento
        } else {
            // Inserir dados na tabela PRODUTO
            $sqlInsertProduto = "INSERT INTO PRODUTO (IDMATERIAL, IDCONECTOR, IDMETRAGEM, IDMODELO, IDFORNECEDOR, DATACADASTRO, IDDATACENTER) 
                                 VALUES ('$idMaterial', '$idConector', '$idMetragem', '$idModelo', '$idFornecedor', '$datacadastro', '$idDataCenter')";
            if (!mysqli_query($conn, $sqlInsertProduto)) {
                header("Location: ../ViewFail/FailCreateInserirDadosProduto.php?erro=Não foi possível inserir os dados na tabela PRODUTO");
                exit();
            }

            // Obter o ID do produto inserido
            $idProduto = $conn->insert_id;

            // Inserir dados na tabela ESTOQUE
            $sqlInsertEstoque = "INSERT INTO ESTOQUE (IDPRODUTO, QUANTIDADE) 
                                 VALUES ('$idProduto', '$quantidade')";
            if (!mysqli_query($conn, $sqlInsertEstoque)) {
                header("Location: ../ViewFail/FailCreateInserirDadosEstoque.php?erro=Não foi possível inserir os dados na tabela ESTOQUE");
                exit();
            }

            // Commit da transação se todas as operações foram bem-sucedidas
            $conn->commit();

            // Redirecionar para a página de sucesso
            header("Location: ../ViewSucess/SucessCreate.php");
            exit(); // Termina a execução do script após redirecionamento
        }
    } catch (Exception $e) {
        // Em caso de erro, fazer rollback da transação
        $conn->rollback();

        // Exibir mensagem de erro
        echo "Erro: " . $e->getMessage();

        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreate.php?erro=Não foi possível realizar o cadastro");
        exit(); // Termina a execução do script após redirecionamento
    }
}

// Fechar a conexão
$conn->close();
?>