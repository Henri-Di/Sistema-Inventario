<?php

session_start();

require_once('../../ViewConnection/ConnectionInventario.php');

function sanitize($conn, $input) {
    return $conn->real_escape_string($input);
}

function datasSaoValidas($dataRecebimento, $dataCadastro) {
    try {
        $timeZone = new DateTimeZone('America/Sao_Paulo');
        $dataRecebimentoObj = DateTime::createFromFormat('Y-m-d', $dataRecebimento, $timeZone);
        $dataCadastroObj = DateTime::createFromFormat('Y-m-d', $dataCadastro, $timeZone);
        $currentDateObj = new DateTime('now', $timeZone);

        if ($dataRecebimentoObj === false || $dataCadastroObj === false) {
            return false;
        }

        if ($dataRecebimentoObj > $currentDateObj || $dataCadastroObj > $currentDateObj) {
            return false;
        }

        return true;
    } catch (Exception $e) {
        return false;
    }
}

function notaFiscalExiste($conn, $numNotaFiscal) {
    $stmt = $conn->prepare("SELECT ID FROM NOTAFISCAL WHERE NUMNOTAFISCAL = ?");
    $stmt->bind_param("s", $numNotaFiscal);
    $stmt->execute();
    $stmt->store_result();
    $rows = $stmt->num_rows;
    $stmt->close();

    return $rows > 0;
}

function processarUploadArquivo($file) {
    $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    $uploadDir = '../../Uploads/';
    $fileName = basename($file['name']);
    $uploadFilePath = $uploadDir . $fileName;
    $fileType = mime_content_type($file['tmp_name']);

    if (!in_array($fileType, $allowedMimeTypes)) {
        header("Location: ../ViewFail/FailCreateTipoArquivoUploadNotaFiscal.php?erro=Tipo de arquivo não permitido. Apenas PDF, JPG e PNG são aceitos. Refaça a operação e tente novamente");
        exit();
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        header("Location: ../ViewFail/FailCreateUploadNotaFiscal.php?erro=Falha ao fazer o upload do arquivo. Refaça a operação e tente novamente");
        exit();
    }

    return $uploadFilePath;
}

function cadastrarNotaFiscal($conn, $numNotaFiscal, $valorNotaFiscal, $material, $conector, $metragem, $modelo, $quantidade, $fornecedor, $dataRecebimento, $dataCadastro, $dataCenter, $filePath) {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $conn->begin_transaction();

    try {
        if (notaFiscalExiste($conn, $numNotaFiscal)) {
            $conn->rollback();
            header("Location: ../ViewFail/FailCreateNotaFiscalExistente.php?erro=Não foi possivel realizar o cadastro. Está nota fiscal já cadastrada no sistema");
            exit();
        }

        if (!datasSaoValidas($dataRecebimento, $dataCadastro)) {
            $conn->rollback();
            header("Location: ../ViewFail/FailCreateDataInvalida.php?erro=A data está fora do intervalo permitido. A data deve ser igual a data atual");
            exit();
        }

        $idDatacenter = recuperarIdDatacenter($conn, $dataCenter);
        if (!$idDatacenter) {
            $idDatacenter = cadastrarNovoDatacenter($conn, $dataCenter);
            if (!$idDatacenter) {
                $conn->rollback();
                header("Location: ../ViewFail/FailCreateNovoDatacenterNotaFiscal.php?erro=Não foi possível cadastrar o DataCenter referente a nota fiscal. Refaça a operação e tente novamente");
                exit();
            }
        }

        $idMaterial = recuperarIdMaterial($conn, $material);
        $idConector = recuperarIdConector($conn, $conector);
        $idMetragem = recuperarIdMetragem($conn, $metragem);
        $idModelo = recuperarIdModelo($conn, $modelo);
        $idFornecedor = recuperarIdFornecedor($conn, $fornecedor);

        $idProduto = recuperarIdProdutoExistente($conn, $idMaterial, $idConector, $idMetragem, $idModelo, $idFornecedor, $idDatacenter);

        if ($idProduto) {
            atualizarEstoqueExistente($conn, $idProduto, $quantidade);
        } else {
            $idProduto = cadastrarNovoProduto($conn, $idMaterial, $idConector, $idMetragem, $idModelo, $idFornecedor, $dataCadastro, $idDatacenter);
            inserirEstoqueInicial($conn, $idProduto, $quantidade);
        }

        $stmt = $conn->prepare("INSERT INTO NOTAFISCAL (NUMNOTAFISCAL, VALORNOTAFISCAL, MATERIAL, CONECTOR, METRAGEM, MODELO, QUANTIDADE, FORNECEDOR, DATARECEBIMENTO, DATACADASTRO, DATACENTER, IDPRODUTO, IDDATACENTER, FILEPATH) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssissssiis", $numNotaFiscal, $valorNotaFiscal, $material, $conector, $metragem, $modelo, $quantidade, $fornecedor, $dataRecebimento, $dataCadastro, $dataCenter, $idProduto, $idDatacenter, $filePath);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        header("Location: ../ViewSucess/SucessCreateNotaFiscal.php?sucesso=A nota fiscal foi cadastrada com sucesso. O estoque do produto foi iniciado ou alterado");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: ../ViewFail/FailCreateNotaFiscal.php?erro=Não foi possivel realizar o cadastro da nota fiscal. A operação será desfeita. Tente novamente");
        exit();
    }
}

function recuperarIdDatacenter($conn, $dataCenter) {
    $stmt = $conn->prepare("SELECT IDDATACENTER FROM DATACENTER WHERE NOME = ?");
    $stmt->bind_param("s", $dataCenter);
    $stmt->execute();
    $stmt->bind_result($idDatacenter);
    $stmt->fetch();
    $stmt->close();

    return $idDatacenter;
}

function cadastrarNovoDatacenter($conn, $dataCenter) {
    $stmt = $conn->prepare("INSERT INTO DATACENTER (NOME) VALUES (?)");
    $stmt->bind_param("s", $dataCenter);
    $stmt->execute();
    $idDatacenter = $stmt->insert_id;
    $stmt->close();

    return $idDatacenter;
}

function recuperarIdMaterial($conn, $material) {
    $stmt = $conn->prepare("SELECT IDMATERIAL FROM MATERIAL WHERE MATERIAL = ?");
    $stmt->bind_param("s", $material);
    $stmt->execute();
    $stmt->bind_result($idMaterial);
    $stmt->fetch();
    $stmt->close();

    return $idMaterial;
}

function recuperarIdConector($conn, $conector) {
    $stmt = $conn->prepare("SELECT IDCONECTOR FROM CONECTOR WHERE CONECTOR = ?");
    $stmt->bind_param("s", $conector);
    $stmt->execute();
    $stmt->bind_result($idConector);
    $stmt->fetch();
    $stmt->close();

    return $idConector;
}

function recuperarIdMetragem($conn, $metragem) {
    $stmt = $conn->prepare("SELECT IDMETRAGEM FROM METRAGEM WHERE METRAGEM = ?");
    $stmt->bind_param("s", $metragem);
    $stmt->execute();
    $stmt->bind_result($idMetragem);
    $stmt->fetch();
    $stmt->close();

    return $idMetragem;
}

function recuperarIdModelo($conn, $modelo) {
    $stmt = $conn->prepare("SELECT IDMODELO FROM MODELO WHERE MODELO = ?");
    $stmt->bind_param("s", $modelo);
    $stmt->execute();
    $stmt->bind_result($idModelo);
    $stmt->fetch();
    $stmt->close();

    return $idModelo;
}

function recuperarIdFornecedor($conn, $fornecedor) {
    $stmt = $conn->prepare("SELECT IDFORNECEDOR FROM FORNECEDOR WHERE FORNECEDOR = ?");
    $stmt->bind_param("s", $fornecedor);
    $stmt->execute();
    $stmt->bind_result($idFornecedor);
    $stmt->fetch();
    $stmt->close();

    return $idFornecedor;
}

function cadastrarNovoProduto($conn, $idMaterial, $idConector, $idMetragem, $idModelo, $idFornecedor, $dataCadastro, $idDatacenter) {
    $stmt = $conn->prepare("INSERT INTO PRODUTO (IDMATERIAL, IDCONECTOR, IDMETRAGEM, IDMODELO, IDFORNECEDOR, DATACADASTRO, IDDATACENTER) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiisi", $idMaterial, $idConector, $idMetragem, $idModelo, $idFornecedor, $dataCadastro, $idDatacenter);
    $stmt->execute();
    $idProduto = $stmt->insert_id;
    $stmt->close();

    return $idProduto;
}

function inserirEstoqueInicial($conn, $idProduto, $quantidade) {
    $stmt = $conn->prepare("INSERT INTO ESTOQUE (IDPRODUTO, QUANTIDADE) VALUES (?, ?)");
    $stmt->bind_param("ii", $idProduto, $quantidade);
    $stmt->execute();
    $stmt->close();
}

function atualizarEstoqueExistente($conn, $idProduto, $quantidade) {
    $stmt = $conn->prepare("UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE + ? WHERE IDPRODUTO = ?");
    $stmt->bind_param("ii", $quantidade, $idProduto);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numNotaFiscal = sanitize($conn, strtoupper($_POST['numNotaFiscal']));
    $valorNotaFiscal = sanitize($conn, strtoupper($_POST['valorNotaFiscal']));
    $material = sanitize($conn, strtoupper($_POST['material']));
    $conector = sanitize($conn, strtoupper($_POST['conector']));
    $metragem = sanitize($conn, strtoupper($_POST['metragem']));
    $modelo = sanitize($conn, strtoupper($_POST['modelo']));
    $quantidade = (int) sanitize($conn, $_POST['quantidade']);
    $fornecedor = sanitize($conn, strtoupper($_POST['fornecedor']));
    $dataRecebimento = sanitize($conn, $_POST['dataRecebimento']);
    $dataCadastro = sanitize($conn, $_POST['dataCadastro']);
    $dataCenter = sanitize($conn, strtoupper($_POST['dataCenter']));
    $file = $_FILES['file'];

    $filePath = processarUploadArquivo($file);

    cadastrarNotaFiscal($conn, $numNotaFiscal, $valorNotaFiscal, $material, $conector, $metragem, $modelo, $quantidade, $fornecedor, $dataRecebimento, $dataCadastro, $dataCenter, $filePath);
}
?>
