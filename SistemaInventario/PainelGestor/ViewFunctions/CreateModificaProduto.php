<?php
// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Verificar se os dados do formulário foram submetidos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar e validar os dados de entrada
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $materialId = filter_input(INPUT_POST, 'Material', FILTER_SANITIZE_NUMBER_INT);
    $conectorId = filter_input(INPUT_POST, 'Conector', FILTER_SANITIZE_NUMBER_INT);
    $metragemId = filter_input(INPUT_POST, 'Metragem', FILTER_SANITIZE_NUMBER_INT);
    $modeloId = filter_input(INPUT_POST, 'Modelo', FILTER_SANITIZE_NUMBER_INT);
    $fornecedorId = filter_input(INPUT_POST, 'Fornecedor', FILTER_SANITIZE_NUMBER_INT);
    $datacenterNome = filter_input(INPUT_POST, 'DataCenter', FILTER_SANITIZE_SPECIAL_CHARS);

    // Iniciar transação
    $conn->begin_transaction();

    try {
        // Verificar se o datacenter existe, se não, inserir
        $idDatacenter = null;
        if (!empty($datacenterNome)) {
            $sqlDatacenter = "SELECT IDDATACENTER FROM DATACENTER WHERE NOME = ?";
            $stmtDatacenter = $conn->prepare($sqlDatacenter);
            $stmtDatacenter->bind_param("s", $datacenterNome);
            $stmtDatacenter->execute();
            $stmtDatacenter->store_result();

            if ($stmtDatacenter->num_rows > 0) {
                $stmtDatacenter->bind_result($idDatacenter);
                $stmtDatacenter->fetch();
            } else {
                $sqlInsertDatacenter = "INSERT INTO DATACENTER (NOME) VALUES (?)";
                $stmtInsertDatacenter = $conn->prepare($sqlInsertDatacenter);
                $stmtInsertDatacenter->bind_param("s", $datacenterNome);
                $stmtInsertDatacenter->execute();
                $idDatacenter = $stmtInsertDatacenter->insert_id;
                $stmtInsertDatacenter->close();
            }

            $stmtDatacenter->close();
        }

        // Preparar a consulta SQL para atualização
        $sqlUpdate = "UPDATE PRODUTO SET ";
        $params = [];
        $types = '';

        if (!empty($materialId)) {
            $sqlUpdate .= "IDMATERIAL = ?, ";
            $params[] = $materialId;
            $types .= 'i';
        }
        if (!empty($conectorId)) {
            $sqlUpdate .= "IDCONECTOR = ?, ";
            $params[] = $conectorId;
            $types .= 'i';
        }
        if (!empty($metragemId)) {
            $sqlUpdate .= "IDMETRAGEM = ?, ";
            $params[] = $metragemId;
            $types .= 'i';
        }
        if (!empty($modeloId)) {
            $sqlUpdate .= "IDMODELO = ?, ";
            $params[] = $modeloId;
            $types .= 'i';
        }
        if (!empty($fornecedorId)) {
            $sqlUpdate .= "IDFORNECEDOR = ?, ";
            $params[] = $fornecedorId;
            $types .= 'i';
        }
        if (!empty($idDatacenter)) {
            $sqlUpdate .= "IDDATACENTER = ?, ";
            $params[] = $idDatacenter;
            $types .= 'i';
        }

        // Remove a última vírgula e espaço do SQL de atualização
        $sqlUpdate = rtrim($sqlUpdate, ", ");

        // Adicionar a cláusula WHERE
        $sqlUpdate .= " WHERE IDPRODUTO = ?";
        $params[] = $id;
        $types .= 'i';

        // Inicializar o statement
        $stmtUpdate = $conn->prepare($sqlUpdate);

        // Verificar se o statement foi preparado com sucesso
        if (!$stmtUpdate) {
            header("Location: ../ViewFail/FailCreateModificaProduto.php?erro=  Não foi possivel realizar a alteração no cadastro do produto");
            exit();
        }

        // Bind dos parâmetros
        if (!empty($types) && count($params) > 0) {
            $stmtUpdate->bind_param($types, ...$params);
        } else {
            header("Location: ../ViewFail/FailCreateModificaProduto.php?erro=  Não foi possivel realizar a alteração no cadastro do produto");
            exit();
        }

        // Executar o statement de atualização
        if ($stmtUpdate->execute()) {
            // Commit da transação se a atualização for bem-sucedida
            $conn->commit();
            header("Location: ../ViewSucess/SucessCreateModificaProduto.php?id=$idModificado");
            exit();
        } else {
            throw new Exception("Erro ao executar a atualização do produto");
        }

        // Fechar o statement de atualização
        $stmtUpdate->close();
    } catch (Exception $e) {
        // Rollback da transação em caso de erro
        $conn->rollback();
        header("Location: ../ViewFail/FailCreateModificaProduto.php?erro=  Não foi possivel realizar a alteração no cadastro do produto");
        exit();
    } finally {
        // Fechar a conexão
        $conn->close();
    }
} else {
    // Redirecionar para a página de falha se o método de requisição não for POST
    header("Location: ../ViewFail/FailCreateModificaProduto.php?erro=  Não foi possivel realizar a alteração no cadastro do produto");
    exit();
}
?>
