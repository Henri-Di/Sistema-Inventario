<?php
// Inclui o arquivo de conexão com o banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Verifica se o ID da transferência foi passado via GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Obtendo o ID da transferência que você deseja imprimir
    $id_transferencia = (int) $_GET['id'];

    // Consultando a transferência
    $sql = "SELECT IDPRODUTO_ORIGEM FROM TRANSFERENCIA WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_transferencia);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Obtendo o ID do produto de origem
        $row = $result->fetch_assoc();
        $id_produto_origem = $row['IDPRODUTO_ORIGEM'];

        // Consultando o ID do datacenter do produto de origem
        $sql_datacenter = "SELECT IDDATACENTER FROM PRODUTO WHERE IDPRODUTO = ?";
        $stmt_datacenter = $conn->prepare($sql_datacenter);
        $stmt_datacenter->bind_param("i", $id_produto_origem);
        $stmt_datacenter->execute();
        $result_datacenter = $stmt_datacenter->get_result();

        if ($result_datacenter->num_rows > 0) {
            $row_datacenter = $result_datacenter->fetch_assoc();
            $id_datacenter = $row_datacenter['IDDATACENTER'];

            // Consultando o nome do datacenter de origem
            $sql_nome_datacenter = "SELECT NOME FROM DATACENTER WHERE IDDATACENTER = ?";
            $stmt_nome_datacenter = $conn->prepare($sql_nome_datacenter);
            $stmt_nome_datacenter->bind_param("i", $id_datacenter);
            $stmt_nome_datacenter->execute();
            $result_nome_datacenter = $stmt_nome_datacenter->get_result();

            if ($result_nome_datacenter->num_rows > 0) {
                $row_nome_datacenter = $result_nome_datacenter->fetch_assoc();
                $nome_datacenter = $row_nome_datacenter['NOME'];

                // Definindo qual documento será apresentado
                if ($nome_datacenter == 'CTC') {
                    // Documento 1 para CTC
                    header('Location: DocumentoSaidaTransferencia1.php?id_transferencia=' . $id_transferencia);
                } elseif ($nome_datacenter == 'DTC') {
                    // Documento 2 para DTC
                    header('Location: DocumentoSaidaTransferencia2.php?id_transferencia=' . $id_transferencia);
                } else {
                    echo "Datacenter de origem não identificado.";
                }
            } else {
                echo "Nome do datacenter de origem não encontrado.";
            }
        } else {
            echo "Datacenter do produto de origem não encontrado.";
        }
    } else {
        echo "Transferência não encontrada.";
    }

    // Fechando as conexões e a declaração
    $stmt->close();
    $stmt_datacenter->close();
    $stmt_nome_datacenter->close();
    $conn->close();
} else {
    echo "ID da transferência inválido ou não fornecido.";
}
?>
